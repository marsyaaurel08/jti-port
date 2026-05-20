<?php

namespace App\Livewire;

use App\Models\{CourseClass, Course, Order, OrderItem, Promo, UserCart, UserPromo};
use App\Services\Payment\Exceptions\PaymentException;
use App\Services\Payment\PaymentFactory;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Livewire\Attributes\{Layout, On, Validate};
use Livewire\Component;

#[Layout('layouts.app')]
class StudentClassCheckout extends Component
{
    public CourseClass $class;

    public string $productType = 'class';

    // User Information
    #[Validate('required|string|max:255')]
    public string $fullName = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|phone:ID,mobile')]
    public string $phoneNumber = '';

    // Promo Code
    public string $promoCode = '';
    public ?int $appliedPromoId = null;
    public float $discountAmount = 0;
    public bool $promoApplied = false;
    public string $promoMessage = '';

    // Transfer Method (Cara Transfer ke BNI VA)
    #[Validate('required|string')]
    public string $transferMethod = '';

    public array $transferMethods;

    public array $transferInstructions;

    // Payment Process
    public bool $isProcessing = false;

    public bool $showVAInstructions = false;

    public array $vaData = [];

    public string $redirectRoute = 'student.orders';

    public bool $showCustomPaymentModal = false;
    public ?array $paymentModalData = null;
    public string $externalId = '';
    public bool $isPaid = false;
    public int $successCountdown = 0;

    public function __construct()
    {
        $this->transferMethods = config('constants.transfer_methods');
        $this->transferInstructions = config('constants.instructions');
    }

    public function mount(string $slug): void
    {
        $this->class = CourseClass::where('slug', $slug)->firstOrFail();

        // Pre-fill user info if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $this->fullName = $user->name;
            $this->email = $user->email;
            $this->phoneNumber = $user->phone_number ?? '';
        }
    }

    public function getProductPrice(): float
    {
        return (float) $this->class->price;
    }

    public function applyPromoCode(): void
    {
        $this->validate(['promoCode' => 'required|string']);

        $promo = Promo::where('code', $this->promoCode)
            ->where('is_active', true)
            ->first();

        if (! $promo) {
            $this->promoMessage = 'Kode promo tidak valid atau tidak aktif.';
            $this->promoApplied = false;

            return;
        }

        $now = now();
        if ($promo->start_date && $now->lt($promo->start_date)) {
            $this->promoMessage = 'Kode promo belum dapat digunakan.';
            $this->promoApplied = false;

            return;
        }
        if ($promo->end_date && $now->gt($promo->end_date)) {
            $this->promoMessage = 'Kode promo sudah kedaluwarsa.';
            $this->promoApplied = false;

            return;
        }

        if ($this->getProductPrice() < $promo->min_order) {
            $this->promoMessage = 'Minimal transaksi Rp' . number_format($promo->min_order, 0, ',', '.');
            $this->promoApplied = false;

            return;
        }

        // Hanya validasi promo, TIDAK MENCATAT transaksi pengguna membatasi jika sekadar apply.
        // Kuota / validasi riil dari 'UserPromo' akan diproses kembali dan dieksekusi di 'processPayment()'.
        if ($promo->max_usage !== null && $promo->userPromos()->count() >= $promo->max_usage) {
            $this->promoMessage = 'Kuota promo sudah habis.';
            $this->promoApplied = false;

            return;
        }

        if (UserPromo::where('promo_id', $promo->id)->where('user_id', Auth::id())->count() >= $promo->max_usage_per_user) {
            $this->promoMessage = 'Anda sudah mencapai batas penggunaan kode promo ini.';
            $this->promoApplied = false;

            return;
        }

        if (! $promo->for_all_products && ! $promo->products()->where('class_id', $this->class->id)->exists()) {
            $this->promoMessage = 'Promo tidak berlaku untuk kelas ini.';
            $this->promoApplied = false;

            return;
        }

        if ($promo->type === 'percentage') {
            $discount = $this->getProductPrice() * ($promo->value / 100);
        } else {
            $discount = $promo->value;
        }

        $this->discountAmount = min($discount, $this->getProductPrice()); // Pastikan diskon tidak lebih besar dari harga
        $this->appliedPromoId = $promo->id;

        $this->promoMessage = 'Kode promo berhasil diterapkan! Diskon: Rp' . number_format($this->discountAmount, 0, ',', '.');
        $this->promoApplied = true;
    }

    public function removePromoCode(): void
    {
        $this->promoCode = '';
        $this->appliedPromoId = null;
        $this->discountAmount = 0;
        $this->promoApplied = false;
        $this->promoMessage = '';
    }

    public function getTotalAmount(): float
    {
        return $this->getProductPrice() - $this->discountAmount;
    }

    public function getTransferInstructions(): array
    {
        $instructions = $this->transferInstructions;

        return $instructions[$this->transferMethod] ?? [];
    }

    private function handlePostPayment(string $gateway, Order $order, array $gatewayResponse): void
    {
        if ($gateway === 'midtrans') {
            // Kirim snap_token ke frontend untuk memunculkan Midtrans Snap popup
            $this->dispatch('trigger-midtrans-snap', token: $gatewayResponse['snap_token']);

            return;
        }

        // BNI: tampilkan instruksi Virtual Account di halaman yang sama
        $this->vaData = $order->getVaData();
        $this->showVAInstructions = true;

        session()->flash('success', 'Nomor Virtual Account berhasil dibuat. Silakan lakukan transfer sesuai instruksi.');
    }

    public function processPayment(): void
    {
        // Bypass transfer method jika total Rp 0 (full promo)
        if ($this->getTotalAmount() <= 0) $this->transferMethod = 'promo';


        $this->validate();

        $this->isProcessing = true;

        try {
            DB::beginTransaction();

            // Re-validasi Promo sebelum create Order (mencegah race condition)
            if ($this->appliedPromoId) {
                $promo = Promo::find($this->appliedPromoId);

                if (!$promo || !$promo->is_active) {
                    throw new \Exception('Promo yang digunakan sudah tidak valid.');
                }

                if ($promo->max_usage !== null && $promo->userPromos()->count() >= $promo->max_usage) {
                    throw new \Exception('Kuota promo yang digunakan telah habis.');
                }

                if (
                    UserPromo::where('promo_id', $promo->id)
                    ->where('user_id', Auth::id())
                    ->count() >= $promo->max_usage_per_user
                ) {
                    throw new \Exception('Anda sudah mencapai batas penggunaan promo ini.');
                }
            }

            // Ambil gateway aktif dari setting
            $activeGateway = DB::table('settings')
                ->where('key', 'payment_gateway')
                ->value('value') ?? 'bni';

            $order = $this->createOrder($activeGateway);

            // ==================== HANDLE FREE PAYMENT ====================
            if ($this->getTotalAmount() <= 0) {
                $order->update(['status' => 'paid']);
                DB::commit();

                $this->clearCartItems();
                session()->flash('success', 'Pembelian berhasil menggunakan promo.');

                $this->redirect(route($this->redirectRoute), navigate: true);
                return;
            }

            // ==================== PROSES PAYMENT GATEWAY ====================
            $paymentService = PaymentFactory::make($activeGateway);

            if ($activeGateway === 'midtrans') {
                // === MIDTRANS CORE API + CUSTOM MODAL ===
                $gatewayResponse = $paymentService->createTransaction(
                    $order,
                    $this->transferMethod,
                    true
                );

                // Simpan response gateway ke Order
                $order->update($gatewayResponse);

                DB::commit();
                $this->clearCartItems();

                // Siapkan data untuk Custom Payment Modal
                $this->paymentModalData = $gatewayResponse;
                $this->paymentModalData['instructions'] = $this->getTransferInstructions();
                $this->externalId = $gatewayResponse['external_id'] ?? $order->invoice_number;

                $this->showCustomPaymentModal = true;
            } else {
                // === GATEWAY LAIN (BNI, dll) - Behavior Lama ===
                $gatewayResponse = $paymentService->createTransaction($order, $this->transferMethod);

                // Simpan response gateway ke Order
                $order->update($gatewayResponse);

                DB::commit();
                $this->clearCartItems();

                $this->handlePostPayment($activeGateway, $order, $gatewayResponse);
            }
        } catch (PaymentException $e) {
            DB::rollBack();
            Log::error('[Checkout] Payment Gateway Error', [
                'gateway' => $e->getGatewayName(),
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            session()->flash('error', 'Gagal terhubung ke layanan pembayaran. Silakan coba lagi.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (in_array($e->getMessage(), [
                'Promo yang digunakan sudah tidak valid.',
                'Kuota promo yang digunakan telah habis.',
                'Anda sudah mencapai batas penggunaan promo ini.',
            ])) {
                $this->removePromoCode();
                session()->flash('error', $e->getMessage() . ' Silakan periksa kembali.');
            } else {
                Log::error('[Checkout] Unexpected Error', [
                    'message' => $e->getMessage(),
                    'user_id' => Auth::id(),
                ]);
                session()->flash('error', 'Terjadi kesalahan. Silakan coba lagi atau hubungi support.');
            }
        } finally {
            $this->isProcessing = false;
        }
    }

    public function generateInvoiceNumber(): string
    {
        return 'INV-' . date('YmdHis') . '-' . Auth::id();
    }

    public function closeModal(): void
    {
        $this->showVAInstructions = false;
        $this->vaData = [];
        // Redirect to orders page
        redirect()->route('student.orders');
    }

    private function clearCartItems(): void
    {
        // Remove cart item for this class
        UserCart::where('user_id', Auth::id())
            ->where('class_id', $this->class->id)
            ->delete();
    }


    private function createOrder(string $paymentGateway): Order
    {
        DB::beginTransaction();

        try {
            // Re-validasi Promo
            if ($this->appliedPromoId) {
                $promo = Promo::find($this->appliedPromoId);

                if (!$promo || !$promo->is_active) {
                    throw new \Exception('Promo yang digunakan sudah tidak valid.');
                }

                if ($promo->max_usage !== null && $promo->userPromos()->count() >= $promo->max_usage) {
                    throw new \Exception('Kuota promo yang digunakan telah habis.');
                }

                if (
                    UserPromo::where('promo_id', $promo->id)
                    ->where('user_id', Auth::id())
                    ->count() >= $promo->max_usage_per_user
                ) {
                    throw new \Exception('Anda sudah mencapai batas penggunaan promo ini.');
                }
            }

            $order = Order::create([
                'user_id'          => Auth::id(),
                'invoice_number'   => $this->generateInvoiceNumber(),
                'subtotal'         => $this->getProductPrice(),
                'discount_amount'  => $this->discountAmount,
                'total_amount'     => $this->getTotalAmount(),
                'status'           => 'pending',
                'payment_gateway'  => $paymentGateway,        // ← Dinamis
                'payment_method'   => $this->transferMethod,
                'promo_id'         => $this->appliedPromoId,
                'promo_code'       => $this->appliedPromoId ? $this->promoCode : null,
            ]);

            // Catat UserPromo
            if ($this->appliedPromoId) {
                UserPromo::create([
                    'user_id'         => Auth::id(),
                    'promo_id'        => $this->appliedPromoId,
                    'order_id'        => $order->id,
                    'discount_amount' => $this->discountAmount,
                ]);
            }

            // Buat Order Item
            OrderItem::create([
                'order_id'          => $order->id,
                'course_id'         => $this->class->course_id,
                'class_id'          => $this->class->id,
                'price_at_purchase' => $this->getTotalAmount(),
                'item_name'         => $this->class->title,
            ]);

            DB::commit();

            $this->clearCartItems();

            return $order->fresh('items');   // fresh + load items

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Method Polling
    public function checkPaymentStatus(): void
    {
        if (empty($this->externalId)) return;

        $service = new \App\Services\Payment\MidtransService();
        $status = $service->checkTransactionStatus($this->externalId);

        // === PEMBAYARAN BERHASIL ===
        if ($status['is_paid'] ?? false) {
            $this->isPaid = true;

            // Mulai countdown sebelum redirect
            $this->successCountdown = 5;        // Detik (bisa diubah)
            $this->dispatch('payment-success-countdown');

            // Tidak langsung close modal, biarkan countdown berjalan
            return;
        }

        // === KEDALUWARSA / DIBATALKAN ===
        if ($status['is_expired'] ?? false || $status['is_cancelled'] ?? false) {
            $this->showCustomPaymentModal = false;
            session()->flash('error', 'Pembayaran kedaluwarsa atau dibatalkan.');
            $this->redirect(route('student.orders'), navigate: true);
        }
    }

    #[On('payment-success-countdown')]
    public function startSuccessCountdown(): void
    {
        // Jika countdown masih ada, jalankan redirect setelah jeda waktu habis
        if ($this->successCountdown <= 0) {
            $this->showCustomPaymentModal = false;
            $this->redirect(route('student.orders'), navigate: true);
            return;
        }

        // Kurangi 1 detik setiap kali di-poll oleh Blade
        $this->successCountdown--;
    }

    public function closePaymentModal(): void
    {
        $this->showCustomPaymentModal = false;
        $this->reset(['paymentModalData', 'externalId', 'isPaid', 'successCountdown']);
        $this->redirect(route('student.orders'), navigate: true);
    }


    public function render()
    {
        return view('livewire.student-checkout', [
            'product' => $this->class,
            'productType' => $this->productType,
            'productPrice' => $this->getProductPrice(),
            'totalAmount' => $this->getTotalAmount(),
            'transferMethod' => $this->transferMethod,
            'transferMethods' => $this->transferMethods,
            'transferInstructions' => $this->getTransferInstructions(),
        ]);
    }
}

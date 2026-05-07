<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserCart;
use App\Services\BNIService;
use App\Services\Payment\Exceptions\PaymentException;
use App\Services\Payment\PaymentFactory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class StudentCourseCheckout extends Component
{
    public Course $course;

    public string $productType = 'course';

    // User Information
    #[Validate('required|string|max:255')]
    public string $fullName = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|phone:ID,mobile')]
    public string $phoneNumber = '';

    // Promo Code
    public string $promoCode = '';

    public float $discountAmount = 0;

    public bool $promoApplied = false;

    public string $promoMessage = '';

    // Transfer Method (Cara Transfer ke BNI VA)
    public string $transferMethod = 'bni_mobile';

    public array $transferMethods;

    public array $transferInstructions;

    // Payment Process
    public bool $isProcessing = false;

    public bool $showVAInstructions = false;

    public array $vaData = [];

    public string $redirectRoute = 'student.orders';

    public function __construct()
    {
        $this->transferMethods = config('constants.transfer_methods');
        $this->transferInstructions = config('constants.instructions');
    }

    public function mount(string $slug): void
    {
        $this->course = Course::where('slug', $slug)->firstOrFail();

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
        return (float) $this->course->price;
    }

    public function applyPromoCode(): void
    {
        $this->validate(['promoCode' => 'required|string']);

        // TODO: Implement promo code validation logic
        // For now, just a simple mock
        if (strlen($this->promoCode) < 3) {
            $this->promoMessage = 'Kode promo tidak valid';
            $this->promoApplied = false;

            return;
        }

        // Mock: Apply 10% discount
        $this->discountAmount = $this->getProductPrice() * 0.10;
        $this->promoMessage = 'Kode promo berhasil diterapkan! Diskon: Rp' . number_format($this->discountAmount, 0, ',', '.');
        $this->promoApplied = true;
    }

    public function removePromoCode(): void
    {
        $this->promoCode = '';
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

    public function processPayment(): void
    {
        $this->validate();

        $this->isProcessing = true;

        try {
            DB::beginTransaction();

            // 1. Ambil gateway aktif dari settings
            $activeGateway = DB::table('settings')
                ->where('key', 'payment_gateway')
                ->value('value') ?? 'bni';

            // 2. Buat Order
            $order = Order::create([
                'user_id'         => Auth::id(),
                'invoice_number'  => $this->generateInvoiceNumber(),
                'subtotal'        => $this->getProductPrice(),
                'discount_amount' => $this->discountAmount,
                'total_amount'    => $this->getTotalAmount(),
                'status'          => 'pending',
                'payment_gateway' => $activeGateway,
                'payment_method'  => $this->transferMethod,
            ]);

            // 3. Buat Order Item
            OrderItem::create([
                'order_id'          => $order->id,
                'course_id'         => $this->course->id,
                'price_at_purchase' => $this->getTotalAmount(),
                'item_name'         => $this->course->title,
            ]);

            // 4. Kirim ke Payment Gateway via Factory
            $paymentService  = PaymentFactory::make($activeGateway);
            dd($paymentService);
            $gatewayResponse = $paymentService->createTransaction($order, $this->transferMethod);

            // 5. Simpan response gateway ke Order (snap_token / va_number / dll)
            $order->update($gatewayResponse);

            DB::commit();

            $this->clearCartItems();

            // 6. Tindak lanjut UI sesuai gateway
            $this->handlePostPayment($activeGateway, $order, $gatewayResponse);
        } catch (PaymentException $e) {
            // Error spesifik dari gateway (BNI / Midtrans)
            DB::rollBack();

            Log::error('[Checkout] Payment Gateway Error', [
                'gateway'  => $e->getGatewayName(),
                'message'  => $e->getMessage(),
                'user_id'  => Auth::id(),
            ]);

            session()->flash('error', 'Gagal terhubung ke layanan pembayaran. Silakan coba beberapa saat lagi.');
        } catch (\Throwable $e) {
            // Error tak terduga lainnya
            DB::rollBack();

            Log::error('[Checkout] Unexpected Error saat processPayment', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Terjadi kesalahan. Silakan coba lagi atau hubungi support.');
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
        // Remove cart item for this course
        UserCart::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->delete();
    }

    public function render()
    {
        return view('livewire.student-checkout', [
            'product' => $this->course,
            'productType' => $this->productType,
            'productPrice' => $this->getProductPrice(),
            'totalAmount' => $this->getTotalAmount(),
            'transferMethod' => $this->transferMethod,
            'transferMethods' => $this->transferMethods,
            'transferInstructions' => $this->getTransferInstructions(),
        ]);
    }
}

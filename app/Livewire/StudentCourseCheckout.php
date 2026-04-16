<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserCart;
use App\Services\BNIService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

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
        $instructions = [
            'bni_mobile' => [
                '1. Buka aplikasi BNI Mobile Banking',
                '2. Masuk dengan nomor rekening dan password',
                '3. Pilih menu "Transfer" > "Ke Bank Lain"',
                '4. Masukkan nomor VA dan nominal yang tertera',
                '5. Verifikasi data transfer',
                '6. Masukkan PIN atau biometric untuk konfirmasi',
                '7. Transfer berhasil, cek status akun Anda',
            ],
            'bni_atm' => [
                '1. Masukkan kartu ATM BNI ke mesin ATM',
                '2. Masukkan PIN ATM Anda',
                '3. Pilih menu "Transfer"',
                '4. Pilih "Transfer ke Bank Lain"',
                '5. Masukkan kode bank tujuan dan nomor VA',
                '6. Masukkan nominal sesuai yang ditampilkan',
                '7. Verifikasi dan konfirmkan transaksi',
                '8. Ambil bukti transfer (struk)',
            ],
            'bni_internet' => [
                '1. Buka website https://ibank.bni.co.id',
                '2. Login dengan user ID dan password',
                '3. Masuk ke menu "Transfer" > "Domestic"',
                '4. Pilih "To Other Bank"',
                '5. Isi nomor VA dan nominal pembayaran',
                '6. Review data transfer dengan teliti',
                '7. Verifikasi dengan token BNI atau mPIN',
                '8. Proses transfer selesai',
            ],
            'bca_mobile' => [
                '1. Buka aplikasi BCA Mobile atau M-BCA',
                '2. Login dengan username dan password',
                '3. Tap menu "m-Transfer"',
                '4. Pilih "Ke Bank Lain"',
                '5. Masukkan nomor VA penerima',
                '6. Verifikasi data, masukkan nominal',
                '7. Review rincian transfer',
                '8. Masukkan PIN atau gunakan biometric',
                '9. Transfer berhasil',
            ],
            'bca_atm' => [
                '1. Masukkan kartu ATM BCA ke mesin',
                '2. Pilih bahasa yang diinginkan',
                '3. Masukkan PIN ATM Anda',
                '4. Pilih "Transaksi Lain"',
                '5. Pilih "Transfer ke Bank Lain"',
                '6. Pilih "Input Rekening Tujuan"',
                '7. Masukkan nomor VA dan nominal',
                '8. Konfirmasi dan selesaikan transaksi',
            ],
            'bca_internet' => [
                '1. Buka website https://www.klikbca.com',
                '2. Login dengan user ID dan password',
                '3. Pilih "Pembayaran" > "Transfer Dana"',
                '4. Pilih "Ke Bank Lain (RTGS/SKN)"',
                '5. Input nomor VA dan data transfer',
                '6. Masukkan nominal sesuai yang ditampilkan',
                '7. Review kembali data transfer',
                '8. Verifikasi dengan Challenge dari SMS',
                '9. Transaksi berhasil',
            ],
            'mandiri_mobile' => [
                '1. Buka aplikasi Livin\' Mandiri',
                '2. Login dengan user ID dan password',
                '3. Navigasi ke "Transfer"',
                '4. Pilih "Transfer ke Bank Lain"',
                '5. Masukkan nomor VA dan nominal',
                '6. Verifikasi penerima dana',
                '7. Masukkan 6 digit PIN Livin\'',
                '8. Konfirmasi final transfer',
                '9. Transfer selesai, cek status transaksi',
            ],
            'mandiri_atm' => [
                '1. Masukkan kartu ATM Mandiri ke mesin',
                '2. Masukkan PIN ATM Anda',
                '3. Pilih "Transaksi Lainnya"',
                '4. Pilih "Transfer"',
                '5. Pilih "Ke Bank Lain"',
                '6. Input nomor rekening tujuan (VA)',
                '7. Masukkan jumlah transfer',
                '8. Konfirmasi dan masukkan PIN',
                '9. Transaksi berhasil',
            ],
            'bri_mobile' => [
                '1. Buka aplikasi BRImo',
                '2. Login dengan username dan PIN',
                '3. Pilih "Transfer"',
                '4. Pilih "Transfer ke Rekening Bank Lain"',
                '5. Masukkan nomor VA penerima',
                '6. Masukkan nominal transfer',
                '7. Verifikasi detail transaksi',
                '8. Masukkan PIN BRImo Anda',
                '9. Transfer berhasil',
            ],
            'bri_atm' => [
                '1. Masukkan kartu ATM BRI ke mesin',
                '2. Masukkan PIN ATM Anda',
                '3. Pilih "MENU UTAMA"',
                '4. Pilih "TRANSFER"',
                '5. Pilih "Ke Bank Lain"',
                '6. Masukkan nomor VA dan nominal',
                '7. Konfirmasi data transfer',
                '8. Masukkan PIN untuk final',
                '9. Ambil struk bukti transfer',
            ],
        ];

        return $instructions[$this->transferMethod] ?? [];
    }

    public function processPayment(): void
    {
        $this->validate();

        $this->isProcessing = true;

        try {
            // Create Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'subtotal' => $this->getProductPrice(),
                'discount_amount' => $this->discountAmount,
                'total_amount' => $this->getTotalAmount(),
                'status' => 'pending',
                'payment_method' => $this->transferMethod,
            ]);

            // Create Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $this->course->id,
                'class_id' => null,
                'price_at_purchase' => $this->getTotalAmount(),
                'item_name' => $this->course->title,
            ]);

            // Generate BNI Virtual Account using BNI Ecollection API
            $bniService = new BNIService;
            $vaDataResponse = $bniService->createVirtualAccount($order);

            // Store BNI details in order
            $order->update([
                'bni_transaction_id' => $vaDataResponse['va_number'] ?? null,
                'bni_va_number' => $vaDataResponse['va_number'] ?? null,
                'is_fallback_va' => $vaDataResponse['is_fallback'] ?? false,
                'bni_va_expired_at' => $vaDataResponse['expired_at'] ?? null,
            ]);

            $this->vaData = $vaDataResponse;
            $this->showVAInstructions = true;

            // Clear cart items after order created
            $this->clearCartItems();

            // Flash success notification
            session()->flash('success', 'Nomor Virtual Account berhasil dibuat. Silakan lakukan transfer sesuai instruksi.');
        } catch (\Exception $e) {
            $this->isProcessing = false;
            session()->flash('error', 'Gagal membuat Virtual Account: ' . $e->getMessage());
        }

        $this->isProcessing = false;
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

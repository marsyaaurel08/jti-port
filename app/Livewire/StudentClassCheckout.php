<?php

namespace App\Livewire;

use App\Models\CourseClass;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserCart;
use App\Services\VABankService;
use App\Services\BNIService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
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
                'course_id' => $this->class->course_id,
                'class_id' => $this->class->id,
                'price_at_purchase' => $this->getTotalAmount(),
                'item_name' => $this->class->title,
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
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
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
        // Remove cart item for this class
        UserCart::where('user_id', Auth::id())
            ->where('class_id', $this->class->id)
            ->delete();
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

<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class StudentOrders extends Component
{
    use WithFileUploads;

    public ?object $user = null;

    public bool $showProofUpload = false;
    public bool $showProofView = false;

    public array $vaData = [];
    public ?Order $selectedOrder = null;

    #[Validate('nullable|image|max:5120')]
    public $paymentProof = null;

    public array $transferMethods;
    public array $transferInstructions;

    // === Midtrans Payment Modal ===
    public bool $showMidtransPaymentModal = false;
    public ?array $paymentModalData = null;
    public bool $isPaid = false;
    public ?string $externalId = null;
    public int $successCountdown = 0;
    public float $totalAmount = 0;

    // === Fallback Payment Modal (Manual Transfer selain BNI) ===
    public bool $showFallbackPaymentModal = false;
    public ?array $fallbackPaymentData = null;

    // === BNI Payment Modal (Khusus BNI VA) ===
    public bool $showBNIPaymentModal = false;
    public ?array $bniPaymentData = null;

    // Cache configuration
    private const CACHE_TTL_MINUTES = 10;

    public function __construct()
    {
        $this->transferMethods = config('constants.transfer_methods');
        $this->transferInstructions = config('constants.instructions');
    }

    public function mount(): void
    {
        if (! Auth::check()) {
            redirect()->route('home');
            return;
        }

        $this->user = Auth::user();
    }

    /**
     * Get unpaid orders with Redis caching
     */
    protected function getUnpaidOrders()
    {
        $userId = Auth::id();

        return Order::with([
            'orderItems.course',
            'orderItems.class',
        ])
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTransferInstructions(string $transferMethod): array
    {
        $instructions = $this->transferInstructions;
        return $instructions[$transferMethod] ?? [];
    }

    // ====================== PROOF UPLOAD ======================
    public function openProofUpload($orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'pending') {
            session()->flash('error', 'Order ini sudah dibayar atau tidak dapat diverifikasi');
            return;
        }

        $this->selectedOrder = $order;
        $this->paymentProof = null;
        $this->showProofUpload = true;
    }

    public function closeProofUpload(): void
    {
        $this->showProofUpload = false;
        $this->paymentProof = null;
        $this->selectedOrder = null;
    }

    public function uploadPaymentProof(): void
    {
        if (! $this->selectedOrder) {
            session()->flash('error', 'Order tidak ditemukan');
            return;
        }

        $this->validate();

        try {
            $filename = 'proof_' . $this->selectedOrder->id . '_' . now()->timestamp . '.' . $this->paymentProof->extension();
            $path = $this->paymentProof->storeAs('payments/proof', $filename, 'public');

            $this->selectedOrder->update(['payment_proof' => $path]);

            session()->flash('success', 'Bukti pembayaran berhasil diupload. Admin akan memverifikasi dalam waktu 1x24 jam.');
            $this->closeProofUpload();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengupload bukti pembayaran: ' . $e->getMessage());
        }
    }

    // ====================== CANCEL ORDER ======================
    public function cancelOrder($orderId): void
    {
        try {
            $order = Order::findOrFail($orderId);

            if ($order->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki izin untuk membatalkan order ini');
            }

            if ($order->status !== 'pending') {
                session()->flash('error', 'Hanya order yang masih pending yang dapat dibatalkan');
                return;
            }

            $order->update(['status' => 'cancelled']);
            session()->flash('success', 'Order berhasil dibatalkan');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membatalkan order: ' . $e->getMessage());
        }
    }

    // ====================== MIDTRANS PAYMENT MODAL ======================
    public function openMidtransPaymentModal($orderId): void
    {
        $order = Order::findOrFail($orderId);
        $this->paymentModalData = is_string($order->payment_response)
            ? json_decode($order->payment_response, true)
            : $order->payment_response;

        $this->totalAmount = $order->total_amount;
        $this->paymentModalData['instructions'] = $this->getTransferInstructions($order->payment_method);
        $this->externalId = $order->external_id;

        Log::info('Opening Midtrans payment modal for order ID: ' . $orderId);

        $this->showMidtransPaymentModal = true;
    }

    public function checkPaymentStatus(): void
    {
        if (!$this->showMidtransPaymentModal) return;
        if (empty($this->externalId)) return;

        $service = new \App\Services\Payment\MidtransService();
        $status = $service->checkTransactionStatus($this->externalId);

        if ($status['is_paid'] ?? false) {
            $this->isPaid = true;
            $this->successCountdown = 5;
            $this->dispatch('payment-success-countdown');
            return;
        }

        if ($status['is_expired'] ?? false || $status['is_cancelled'] ?? false) {
            $this->showMidtransPaymentModal = false;
            session()->flash('error', 'Pembayaran kedaluwarsa atau dibatalkan.');
        }
    }

    #[On('payment-success-countdown')]
    public function startSuccessCountdown(): void
    {
        if ($this->successCountdown <= 0) {
            $this->showMidtransPaymentModal = false;
            return;
        }

        $this->successCountdown--;
    }

    public function closeMidtransPaymentModal(): void
    {
        $this->showMidtransPaymentModal = false;
        $this->reset(['paymentModalData', 'isPaid', 'successCountdown', 'externalId']);
    }

    // ====================== FALLBACK PAYMENT MODAL ======================
    public function openFallbackPaymentModal($orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'pending') {
            session()->flash('error', 'Order ini sudah dibayar');
            return;
        }

        $this->selectedOrder = $order;
        $this->fallbackPaymentData = [
            'order_id'       => $order->id,
            'invoice_number' => $order->invoice_number,
            'amount'         => $order->subtotal,
            'payment_method' => $order->payment_method,
            'bank'           => $this->getBankName($order->payment_method),
            'va_number'      => $order->va_number ?? null,
            'total_amount'    => $order->total_amount,
            'instructions'   => $this->getTransferInstructions($order->payment_method),
            'expired_at'     => $order->expired_at?->format('d M Y, H:i'),
        ];

        Log::info('Opening fallback payment modal for order ID: ' . $orderId, [
            'method' => $order->payment_method
        ]);

        $this->showFallbackPaymentModal = true;
    }

    public function closeFallbackPaymentModal(): void
    {
        $this->showFallbackPaymentModal = false;
        $this->reset(['fallbackPaymentData']);
    }

    // ====================== BNI PAYMENT MODAL ======================
    public function openBNIPaymentModal($orderId): void
    {
        // $order = Order::findOrFail($orderId);
        // $this->paymentModalData = is_string($order->payment_response)
        //     ? json_decode($order->payment_response, true)
        //     : $order->payment_response;

        // $this->totalAmount = $order->subtotal;
        // $this->paymentModalData['instructions'] = $this->getTransferInstructions($order->payment_method);
        // $this->externalId = $order->external_id;
        Log::info('Opening BNI payment modal for order ID: ' . $orderId);

        $this->showBNIPaymentModal = true;
    }

    public function closeBNIPaymentModal(): void
    {
        $this->showBNIPaymentModal = false;
        $this->reset(['bniPaymentData']);
    }

    // ====================== HELPER METHODS ======================
    private function getBankName(string $paymentMethod): string
    {
        $map = [
            'bri'      => 'BRI',
            'bca'      => 'BCA',
            'mandiri'  => 'Mandiri',
            'permata'  => 'Permata',
            // tambahkan sesuai kebutuhan
        ];

        return $map[strtolower($paymentMethod)] ?? strtoupper($paymentMethod);
    }

    private function getVaNumberFromResponse(Order $order): ?string
    {
        if (empty($order->payment_response)) return null;

        $data = is_string($order->payment_response)
            ? json_decode($order->payment_response, true)
            : $order->payment_response;

        return $data['va_numbers'][0]['va_number']
            ?? $data['bill_key']
            ?? $data['permata_va_number']
            ?? null;
    }

    // ====================== OTHER METHODS ======================
    public function viewProof($orderId): void
    {
        try {
            $order = Order::findOrFail($orderId);

            if ($order->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki izin untuk melihat bukti ini');
            }

            if (! $order->payment_proof) {
                session()->flash('error', 'Bukti pembayaran tidak ditemukan');
                return;
            }

            $this->selectedOrder = $order;
            $this->showProofView = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuka bukti pembayaran: ' . $e->getMessage());
        }
    }

    public function closeProofView(): void
    {
        $this->showProofView = false;
        $this->selectedOrder = null;
    }

    public function handlePaymentFinished(int $orderId): void
    {
        $order = Order::find($orderId);
        if (! $order) return;

        $order->refresh();

        if (in_array($order->status, ['paid', 'success', 'settlement'])) {
            session()->flash('success', 'Terima kasih! Pembayaran untuk Invoice ' . $order->invoice_number . ' telah berhasil dikonfirmasi.');
        } else {
            session()->flash('info', 'Sistem sedang memverifikasi pembayaran Anda. Status akan diperbarui secara otomatis.');
        }
    }
    public function openPaymentModal($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->is_fallback_va) {
            return $this->openFallbackPaymentModal($orderId);
        }

        if ($order->payment_gateway === 'midtrans') {
            return $this->openMidtransPaymentModal($orderId);
        }

        // BNI Masih Error, jadi sementara pakai fallback dulu
        return $this->openFallbackPaymentModal($orderId);
        // return $this->openBNIPaymentModal($orderId);
    }
    public function render()
    {
        $unpaidOrders = $this->getUnpaidOrders();

        return view('livewire.student-orders', [
            'orders' => $unpaidOrders,
            'transferMethods' => $this->transferMethods,
            'transferInstructions' => $this->transferInstructions,
        ]);
    }
}

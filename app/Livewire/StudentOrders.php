<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\{Auth, Cache};
use Livewire\Attributes\{Layout, Validate};
use Livewire\{Component, WithFileUploads};

#[Layout('layouts.app')]
class StudentOrders extends Component
{
    use WithFileUploads;

    public ?object $user = null;

    public bool $showVAInstructions = false;

    public bool $showProofUpload = false;

    public bool $showProofView = false;

    public array $vaData = [];

    public ?Order $selectedOrder = null;

    #[Validate('nullable|image|max:5120')]
    public $paymentProof = null;

    public array $transferMethods;
    public array $transferInstructions;

    // Cache configuration
    private const CACHE_TTL_MINUTES = 10; // sesuaikan kebutuhan (5-15 menit biasanya cukup)

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
        $cacheKey = "user:{$userId}:unpaid_orders";

        return Order::where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTransferInstructions(?string $transferMethod = null): array
    {
        $method = $transferMethod ?? 'bni_mobile';
        return $this->transferInstructions[$method] ?? [];
    }

    public function viewVAInstructions($orderId): void
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
        $this->vaData = $order->getVaData();
        $this->showVAInstructions = true;
    }

    public function closeModal(): void
    {
        $this->showVAInstructions = false;
        $this->vaData = [];
        $this->selectedOrder = null;
    }

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

    public function viewProof($orderId): void
    {
        try {
            $order = Order::findOrFail($orderId);

            // Check authorization
            if ($order->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki izin untuk melihat bukti ini');
            }

            // Check if proof exists
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

        if (!$order) {
            return;
        }

        $order->refresh();

        if (in_array($order->status, ['paid', 'success', 'settlement'])) {
            session()->flash('success', 'Terima kasih! Pembayaran untuk Invoice ' . $order->invoice_number . ' telah berhasil dikonfirmasi.');
        } else {
            session()->flash('info', 'Sistem sedang memverifikasi pembayaran Anda. Status akan diperbarui secara otomatis dalam beberapa saat.');
        }

    }

    public function render()
    {
        $unpaidOrders = $this->getUnpaidOrders();

        $transferInstructions = [];
        if ($this->selectedOrder) {
            $transferInstructions = $this->getTransferInstructions($this->selectedOrder->payment_method);
        }

        return view('livewire.student-orders', [
            'orders' => $unpaidOrders,
            'transferMethods' => $this->transferMethods,
            'transferInstructions' => $transferInstructions,
        ]);
    }
}

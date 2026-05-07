<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Payment\Exceptions\PaymentException;
use Midtrans\{Config, Snap, Transaction};
use Illuminate\Support\Facades\Log;

class MidtransService implements PaymentServiceInterface
{
    public function __construct()
    {
        $this->configure();
    }

    // -------------------------------------------------------------------------
    // Configuration
    // -------------------------------------------------------------------------

    private function configure(): void
    {
        Config::$serverKey    = config('payment.midtrans.server_key');
        Config::$clientKey    = config('payment.midtrans.client_key');
        Config::$isProduction = config('payment.midtrans.is_production', false);
        Config::$isSanitized  = config('payment.midtrans.is_sanitized', true);
        Config::$is3ds        = config('payment.midtrans.is_3ds', true);
    }

    // -------------------------------------------------------------------------
    // Interface Implementation
    // -------------------------------------------------------------------------

    /**
     * Membuat Snap Token via Midtrans.
     *
     * Akan mengembalikan array yang siap di-update ke field Order:
     * ['snap_token' => '...', 'external_id' => '...']
     */
    public function createTransaction(Order $order, ?string $preferredMethod = null): array
    {
        $params = $this->buildSnapParams($order, $preferredMethod);

        try {
            $snapToken = Snap::getSnapToken($params);

            return [
                'snap_token'  => $snapToken,
                'external_id' => $order->invoice_number, // Midtrans pakai order_id kita sebagai referensi
            ];
        } catch (\Exception $e) {
            Log::error('[MidtransService] Gagal membuat Snap Token', [
                'order_id'       => $order->id,
                'invoice_number' => $order->invoice_number,
                'error'          => $e->getMessage(),
            ]);

            throw new PaymentException(
                message: 'Gagal membuat transaksi Midtrans: ' . $e->getMessage(),
                gatewayName: 'midtrans',
                rawResponse: $e,
                previous: $e,
            );
        }
    }

    /**
     * Memeriksa status transaksi langsung dari Midtrans API.
     */
    public function checkTransactionStatus(string $externalId): array
    {
        try {
            $status = Transaction::status($externalId);

            return $this->normalizeTransactionStatus((array) $status);
        } catch (\Exception $e) {
            Log::error('[MidtransService] Gagal memeriksa status transaksi', [
                'external_id' => $externalId,
                'error'       => $e->getMessage(),
            ]);

            throw new PaymentException(
                message: 'Gagal memeriksa status transaksi Midtrans: ' . $e->getMessage(),
                gatewayName: 'midtrans',
                rawResponse: $e,
                previous: $e,
            );
        }
    }

    /**
     * Memproses dan normalisasi payload webhook dari Midtrans.
     * Midtrans mengirim notifikasi via HTTP POST ke endpoint webhook kita.
     *
     * Mengembalikan array normalisasi:
     * [
     *   'is_paid'        => bool,
     *   'is_cancelled'   => bool,
     *   'external_id'    => string,   // order_id dari Midtrans (= invoice_number kita)
     *   'transaction_id' => string,   // ID unik transaksi dari Midtrans
     *   'payment_type'   => string,
     *   'paid_at'        => string|null,
     * ]
     */
    public function handleWebhookPayload(array $payload): array
    {
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? '';

        $isPaid      = $this->isPaidStatus($transactionStatus, $fraudStatus);
        $isCancelled = in_array($transactionStatus, ['cancel', 'expire', 'deny'], true);

        return [
            'is_paid'        => $isPaid,
            'is_cancelled'   => $isCancelled,
            'external_id'    => $payload['order_id'] ?? null,          // = invoice_number kita
            'transaction_id' => $payload['transaction_id'] ?? null,    // ID unik dari Midtrans
            'payment_type'   => $payload['payment_type'] ?? null,
            'paid_at'        => $isPaid ? ($payload['settlement_time'] ?? now()->toDateTimeString()) : null,
        ];
    }

    // -------------------------------------------------------------------------
    // Signature Verification
    // -------------------------------------------------------------------------

    /**
     * Memverifikasi tanda tangan (signature key) yang dikirim Midtrans di webhook.
     * Wajib divalidasi sebelum memproses payload webhook.
     *
     * @see https://docs.midtrans.com/docs/verifying-data-integrity
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        $orderId           = $payload['order_id'] ?? '';
        $statusCode        = $payload['status_code'] ?? '';
        $grossAmount       = $payload['gross_amount'] ?? '';
        $incomingSignature = $payload['signature_key'] ?? '';

        $serverKey        = config('payment.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $incomingSignature);
    }

    // -------------------------------------------------------------------------
    // Helpers (Private)
    // -------------------------------------------------------------------------

    /**
     * Menyusun parameter untuk Midtrans Snap API.
     */
    private function buildSnapParams(Order $order, ?string $preferredMethod = null): array
    {
        $user = $order->user;

        return [
            // Identitas transaksi — gunakan invoice_number sebagai order_id
            'transaction_details' => [
                'order_id'     => $order->invoice_number,
                'gross_amount' => (int) $order->total_amount,
            ],

            // Detail customer
            'customer_details' => [
                'first_name' => $user->name ?? 'Pelanggan',
                'email'      => $user->email ?? '',
                'phone'      => $user->phone ?? '',
            ],

            // Detail item (dari relasi orderItems jika ada, fallback ke summary)
            'item_details' => $this->buildItemDetails($order),

            // Aktifkan hanya metode pembayaran yang diperlukan (opsional)
            // 'enabled_payments' => ['credit_card', 'bni_va', 'bca_va', 'gopay', 'qris'],
            'enabled_payments' => $preferredMethod ? [$preferredMethod] : null,

            // Konfigurasi halaman pembayaran
            'callbacks' => [
                'finish'    => route('payment.finish'),
                'unfinish'  => route('payment.unfinish'),
                'error'     => route('payment.error'),
            ],
        ];
    }

    /**
     * Menyusun item_details untuk Snap dari relasi orderItems.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildItemDetails(Order $order): array
    {
        // Jika Order memiliki relasi items, gunakan data relasi
        if ($order->relationLoaded('items') && $order->items->isNotEmpty()) {
            return $order->items->map(fn($item) => [
                'id'       => (string) $item->id,
                'name'     => $item->item_name,
                'price'    => (int) $item->price_at_purchase,
                'quantity' => 1,
            ])->toArray();
        }

        // Fallback: 1 item summary dari Order
        $items = [[
            'id'       => $order->invoice_number,
            'name'     => 'Pembayaran ' . $order->invoice_number,
            'price'    => (int) $order->total_amount,
            'quantity' => 1,
        ]];

        // Tambahkan baris diskon jika ada
        if ($order->discount_amount > 0) {
            $items[] = [
                'id'       => 'DISCOUNT',
                'name'     => 'Diskon',
                'price'    => -(int) $order->discount_amount,
                'quantity' => 1,
            ];
        }

        return $items;
    }

    /**
     * Normalisasi response status dari Midtrans Transaction::status().
     */
    private function normalizeTransactionStatus(array $status): array
    {
        $transactionStatus = $status['transaction_status'] ?? '';
        $fraudStatus       = $status['fraud_status'] ?? '';

        return [
            'is_paid'        => $this->isPaidStatus($transactionStatus, $fraudStatus),
            'is_cancelled'   => in_array($transactionStatus, ['cancel', 'expire', 'deny'], true),
            'external_id'    => $status['order_id'] ?? null,
            'transaction_id' => $status['transaction_id'] ?? null,
            'payment_type'   => $status['payment_type'] ?? null,
            'paid_at'        => $status['settlement_time'] ?? null,
            'raw'            => $status,
        ];
    }

    /**
     * Menentukan apakah kombinasi status transaksi dan fraud dianggap "lunas".
     *
     * @see https://docs.midtrans.com/docs/getting-the-transaction-status
     */
    private function isPaidStatus(string $transactionStatus, string $fraudStatus): bool
    {
        if ($transactionStatus === 'capture') {
            // capture: hanya dianggap lunas jika fraud_status = accept (bukan challenge)
            return $fraudStatus === 'accept';
        }

        return $transactionStatus === 'settlement';
    }
}

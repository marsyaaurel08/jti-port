<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Payment\Exceptions\PaymentException;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;
use Midtrans\Transaction;

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
        Config::$serverKey = config('payment.midtrans.server_key');
        Config::$clientKey = config('payment.midtrans.client_key');
        Config::$isProduction = config('payment.midtrans.is_production', false);
        Config::$isSanitized = config('payment.midtrans.is_sanitized', true);
        Config::$is3ds = config('payment.midtrans.is_3ds', true);
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
    public function createTransaction(Order $order, ?string $preferredMethod = null, bool $useCoreApi = false): array
    {
        if ($useCoreApi) {
            return $this->createCoreTransactionWithMethod($order, $preferredMethod);
        }
        $params = $this->buildSnapParams($order, $preferredMethod);

        try {
            $snapToken = Snap::getSnapToken($params);

            return [
                'snap_token' => $snapToken,
                'external_id' => $order->invoice_number, // Midtrans pakai order_id kita sebagai referensi
            ];
        } catch (\Exception $e) {
            Log::error('[MidtransService] Gagal membuat Snap Token', [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'error' => $e->getMessage(),
                'total_amount' => $order->total_amount,
            ]);

            throw new PaymentException(
                message: 'Gagal membuat transaksi Midtrans: '.$e->getMessage(),
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
        // === MOCK MODE UNTUK LOAD TESTING ===
        if (config('app.midtrans_mock_mode', false) === true) {
            Log::info('[MidtransService] MOCK MODE AKTIF - Return settlement', [
                'external_id' => $externalId,
            ]);

            // Data mock yang sudah sesuai format Midtrans
            $mockData = [
                'transaction_status' => 'settlement',     // ini yang penting
                'status_code' => '200',
                'status_message' => 'Mock settlement for load testing',
                'order_id' => $externalId,
                'transaction_id' => 'mock-trx-'.time().'-'.rand(1000, 9999),
                'gross_amount' => '150000',         // sesuaikan jika perlu
                'payment_type' => 'bank_transfer',
                'fraud_status' => 'accept',
                'settlement_time' => now()->toDateTimeString(),
                'transaction_time' => now()->toDateTimeString(),
            ];

            return $this->normalizeTransactionStatus($mockData);
        }
        try {
            $status = Transaction::status($externalId);

            Log::info('[MidtransService] Raw check transaction status', [
                'external_id' => $externalId,
                'response' => (array) $status,
            ]);

            return $this->normalizeTransactionStatus((array) $status);
        } catch (\Exception $e) {
            Log::error('[MidtransService] Gagal memeriksa status transaksi', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            throw new PaymentException(
                message: 'Gagal memeriksa status transaksi Midtrans: '.$e->getMessage(),
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
        $fraudStatus = $payload['fraud_status'] ?? '';

        $isPaid = $this->isPaidStatus($transactionStatus, $fraudStatus);
        $isCancelled = in_array($transactionStatus, ['cancel', 'expire', 'deny'], true);

        return [
            'is_paid' => $isPaid,
            'is_cancelled' => $isCancelled,
            'external_id' => $payload['order_id'] ?? null,          // = invoice_number kita
            'transaction_id' => $payload['transaction_id'] ?? null,    // ID unik dari Midtrans
            'payment_type' => $payload['payment_type'] ?? null,
            'paid_at' => $isPaid ? ($payload['settlement_time'] ?? now()->toDateTimeString()) : null,
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
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $incomingSignature = $payload['signature_key'] ?? '';

        $serverKey = config('payment.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

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
                'order_id' => $order->invoice_number,
                'gross_amount' => (int) $order->total_amount,
            ],

            // Detail customer
            'customer_details' => [
                'first_name' => $user->name ?? 'Pelanggan',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
            ],

            // Detail item (dari relasi orderItems jika ada, fallback ke summary)
            'item_details' => $this->buildItemDetails($order),

            // Aktifkan hanya metode pembayaran yang diperlukan (opsional)
            // 'enabled_payments' => ['credit_card', 'bni_va', 'bca_va', 'gopay', 'qris'],
            'enabled_payments' => $preferredMethod ? [$preferredMethod] : null,

            // Konfigurasi halaman pembayaran
            'callbacks' => [
                'finish' => route('payment.finish'),
                'unfinish' => route('payment.unfinish'),
                'error' => route('payment.error'),
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
        // Pastikan relasi items sudah di-load agar item yang baru dibuat terbaca
        $order->loadMissing('items');

        // Jika Order memiliki relasi items, gunakan data relasi
        if ($order->relationLoaded('items') && $order->items->isNotEmpty()) {
            return $order->items->map(fn ($item) => [
                'id' => (string) $item->id,
                'name' => mb_substr($item->item_name, 0, 50), // Midtrans membatasi panjang nama item maksimal 50 karakter
                'price' => (int) $item->price_at_purchase,
                'quantity' => 1,
            ])->toArray();
        }

        // Fallback: 1 item summary dari Order
        $items = [[
            'id' => $order->invoice_number,
            'name' => 'Pembayaran '.$order->invoice_number,
            'price' => (int) $order->subtotal,
            'quantity' => 1,
        ]];

        // Tambahkan baris diskon jika ada
        if ($order->discount_amount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'name' => 'Diskon',
                'price' => -(int) $order->discount_amount,
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
        $fraudStatus = $status['fraud_status'] ?? '';

        return [
            'is_paid' => $this->isPaidStatus($transactionStatus, $fraudStatus),
            'is_cancelled' => in_array($transactionStatus, ['cancel', 'expire', 'deny'], true),
            'external_id' => $status['order_id'] ?? null,
            'transaction_id' => $status['transaction_id'] ?? null,
            'payment_type' => $status['payment_type'] ?? null,
            'paid_at' => $status['settlement_time'] ?? null,
            'raw' => $status,
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

    /**
     * Core API dengan mapping payment method (mirip logic di initiateCustomPayment)
     */
    private function createCoreTransactionWithMethod(Order $order, ?string $preferredMethod): array
    {
        if (empty($preferredMethod)) {
            $preferredMethod = 'bni_va'; // default
        }

        $methodMap = [
            'bni_va' => ['payment_type' => 'bank_transfer', 'data' => ['bank_transfer' => ['bank' => 'bni']]],
            'bca_va' => ['payment_type' => 'bank_transfer', 'data' => ['bank_transfer' => ['bank' => 'bca']]],
            'bri_va' => ['payment_type' => 'bank_transfer', 'data' => ['bank_transfer' => ['bank' => 'bri']]],
            'permata_va' => ['payment_type' => 'bank_transfer', 'data' => ['bank_transfer' => ['bank' => 'permata']]],
            'echannel' => ['payment_type' => 'echannel', 'data' => ['echannel' => ['bill_info1' => 'Pembayaran', 'bill_info2' => 'Kursus']]],
            'gopay' => ['payment_type' => 'qris', 'data' => ['qris' => ['acquirer' => 'gopay']]],
            'shopeepay' => ['payment_type' => 'qris', 'data' => ['qris' => ['acquirer' => 'airpay shopee']]],
            'indomaret' => ['payment_type' => 'cstore', 'data' => ['cstore' => ['store' => 'indomaret', 'message' => 'Pembayaran Kursus']]],
            'alfamart' => ['payment_type' => 'cstore', 'data' => ['cstore' => ['store' => 'alfamart', 'message' => 'Pembayaran Kursus']]],
        ];

        $config = $methodMap[$preferredMethod] ?? $methodMap['bni_va'];

        return $this->createCoreTransaction(
            order: $order,
            paymentType: $config['payment_type'],
            paymentData: $config['data']
        );
    }

    /**
     * Buat transaksi via Core API (bukan Snap).
     * Digunakan untuk custom modal dengan full control UI.
     *
     * @param  string  $paymentType  Contoh: 'qris', 'bank_transfer'
     * @param  array  $paymentData  Detail spesifik metode (bank, dll)
     */
    public function createCoreTransaction(Order $order, string $paymentType, array $paymentData = []): array
    {
        $params = $this->buildCoreParams($order, $paymentType, $paymentData);

        try {
            $response = CoreApi::charge($params);
            $response = (array) $response;

            Log::info('[MidtransService] Core API charge response', [
                'order_id' => $order->id,
                'payment_type' => $paymentType,
                'response' => $response,
            ]);

            return $this->normalizeCoreResponse($response, $paymentType);
        } catch (\Exception $e) {
            Log::error('[MidtransService] Gagal Core API charge', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new PaymentException(
                message: 'Gagal membuat transaksi Core API: '.$e->getMessage(),
                gatewayName: 'midtrans',
                rawResponse: $e,
                previous: $e,
            );
        }
    }

    /**
     * Bangun parameter untuk Core API charge.
     */
    private function buildCoreParams(Order $order, string $paymentType, array $paymentData): array
    {
        $base = [
            'payment_type' => $paymentType,
            'transaction_details' => [
                'order_id' => $order->invoice_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name ?? 'Pelanggan',
                'email' => $order->user->email ?? '',
                'phone' => $order->user->phone ?? '',
            ],
            'item_details' => $this->buildItemDetails($order),
        ];

        return array_merge($base, $paymentData);
    }

    /**
     * Normalisasi response Core API ke format seragam.
     * Tujuan: Frontend hanya perlu baca 1 struktur, bukan tiap metode beda format.
     */
    private function normalizeCoreResponse($response, string $paymentType): array
    {
        // === PERBAIKAN UTAMA: Konversi ke array ===
        if (is_object($response)) {
            $response = json_decode(json_encode($response), true);
        }

        if (! is_array($response)) {
            $response = [];
        }

        $normalized = [
            'payment_type' => $paymentType,
            'external_id' => $response['order_id'] ?? null,
            'transaction_id' => $response['transaction_id'] ?? null,
            'status' => $response['transaction_status'] ?? null,
            'expiry' => $response['expiry_time'] ?? null,

            // QR
            'qr_string' => null,
            'qr_url' => null,
            'acquirer' => null,

            // Virtual Account
            'va_number' => null,
            'bank' => null,

            // Echannel (Mandiri)
            'bill_key' => null,
            'biller_code' => null,

            // Convenience Store
            'payment_code' => null,
            'store' => null,

            // E-Wallet
            'deeplink_url' => null,
        ];

        switch ($paymentType) {

            case 'qris':
                $normalized['acquirer'] = $response['acquirer'] ?? null;
                $normalized['qr_string'] = $response['qr_string'] ?? null;

                $actions = $response['actions'] ?? [];

                // Proteksi kuat jika actions masih berbentuk object / stdClass
                if (is_object($actions)) {
                    $actions = json_decode(json_encode($actions), true);
                }

                // Jika actions bukan array, jadikan array kosong
                if (! is_array($actions)) {
                    $actions = [];
                }

                foreach ($actions as $action) {
                    // Proteksi jika item action masih object
                    if (is_object($action)) {
                        $action = json_decode(json_encode($action), true);
                    }

                    if (! is_array($action)) {
                        continue;
                    }

                    $actionName = $action['name'] ?? '';

                    // Lebih fleksibel — terima beberapa kemungkinan nama
                    if (in_array($actionName, ['generate-qr-code', 'generate-qr-code-v2'])) {
                        $normalized['qr_url'] = $action['url'] ?? null;
                        break;                    // Langsung stop setelah menemukan
                    }
                }

                // Fallback: Jika masih null, coba cari di root response (kadang Midtrans menaruh langsung)
                if (empty($normalized['qr_url']) && ! empty($response['qr_url'])) {
                    $normalized['qr_url'] = $response['qr_url'];
                }

                break;

            case 'bank_transfer':
                $vaNumbers = $response['va_numbers'] ?? [];

                if (is_object($vaNumbers)) {
                    $vaNumbers = json_decode(json_encode($vaNumbers), true);
                }

                if (! empty($vaNumbers) && is_array($vaNumbers)) {
                    $firstVa = $vaNumbers[0] ?? [];
                    if (is_object($firstVa)) {
                        $firstVa = json_decode(json_encode($firstVa), true);
                    }

                    $normalized['va_number'] = $firstVa['va_number'] ?? null;
                    $normalized['bank'] = strtoupper($firstVa['bank'] ?? '');
                }

                // Permata VA fallback
                if (empty($normalized['va_number']) && isset($response['permata_va_number'])) {
                    $normalized['va_number'] = $response['permata_va_number'];
                    $normalized['bank'] = 'PERMATA';
                }
                break;

            case 'echannel':
                $normalized['bill_key'] = $response['bill_key'] ?? null;
                $normalized['biller_code'] = $response['biller_code'] ?? null;
                break;

            case 'cstore':
                $normalized['payment_code'] = $response['payment_code'] ?? null;
                $normalized['store'] = strtoupper($response['store'] ?? '');
                break;
        }

        return $normalized;
    }
}

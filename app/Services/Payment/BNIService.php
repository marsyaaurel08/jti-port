<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Payment\Exceptions\PaymentException;
use BniApi\BniPhp\Bni;
use BniApi\BniPhp\api\Ecollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * BNIService
 *
 * Implementasi Virtual Account BNI menggunakan produk Ecollection.
 * SDK: composer require bni-api/bni-php
 *
 * Ecollection mendukung beberapa tipe billing VA:
 *   'c' = closed amount (jumlah tetap, paling umum untuk payment)
 *   'o' = open amount   (user bisa bayar berapa saja)
 *
 * Konfigurasi di config/payment.php → 'bni' => [...]
 * dan .env:
 *   BNI_CLIENT_ID=
 *   BNI_CLIENT_SECRET=
 *   BNI_API_KEY=
 *   BNI_API_SECRET=
 *   BNI_APP_NAME=
 *   BNI_IS_PRODUCTION=false
 *   BNI_VA_EXPIRY_HOURS=24
 *   BNI_WEBHOOK_ALLOWED_IPS=   # Comma-separated IP whitelist BNI (opsional)
 */
class BNIService implements PaymentServiceInterface
{
    private Bni $bni;
    private Ecollection $ecollection;

    public function __construct()
    {
        $this->configure();
    }

    // -------------------------------------------------------------------------
    // Configuration
    // -------------------------------------------------------------------------

    private function configure(): void
    {
        $this->bni = new Bni(
            env:          config('payment.bni.is_production') ? 'prod' : 'sandbox',
            clientId:     config('payment.bni.client_id'),
            clientSecret: config('payment.bni.client_secret'),
            apiKey:       config('payment.bni.api_key'),
            apiSecret:    config('payment.bni.api_secret'),
            appName:      config('payment.bni.app_name'),
        );

        $this->ecollection = new Ecollection($this->bni);
    }

    // -------------------------------------------------------------------------
    // Interface Implementation
    // -------------------------------------------------------------------------

    /**
     * Membuat Virtual Account BNI via Ecollection.
     *
     * Mengembalikan array yang siap di-update ke model Order:
     * [
     *   'va_number'      => '8002xxxxxxxxxx',
     *   'va_expired_at'  => Carbon,
     *   'external_id'    => 'INV-xxx',   // trxId yang kita kirimkan
     *   'is_fallback_va' => false,
     * ]
     *
     * @throws PaymentException
     */
    public function createTransaction(Order $order, ?string $preferredMethod = null): array
    {
        $user      = $order->user;
        $expiredAt = $this->resolveExpiry();
        $trxId     = $this->sanitizeTrxId($order->invoice_number);

        try {
            $response = $this->ecollection->createBilling(
                trxId:           $trxId,
                trxAmount:       (string) (int) $order->total_amount,  // string integer, tanpa desimal
                billingType:     'c',                                   // 'c' = closed / jumlah tetap
                customerName:    $this->sanitizeName($user->name ?? 'Pelanggan'),
                customerEmail:   $user->email ?? '',
                customerPhone:   $user->phone ?? '',
                virtualAccount:  '',                                    // kosong = autogenerate oleh BNI
                datetimeExpired: $expiredAt->format('Y-m-d\TH:i:sP'),  // ISO 8601
                description:     'Pembayaran ' . $order->invoice_number,
            );

            $this->assertSuccess($response, 'createBilling');

            $vaNumber = $response->virtualAccount ?? null;

            if (empty($vaNumber)) {
                throw new PaymentException(
                    message:     'BNI Ecollection tidak mengembalikan nomor VA.',
                    gatewayName: 'bni',
                    rawResponse: (array) $response,
                );
            }

            Log::info('[BNIService] VA berhasil dibuat.', [
                'invoice_number' => $order->invoice_number,
                'va_number'      => $vaNumber,
                'expired_at'     => $expiredAt->toDateTimeString(),
            ]);

            return [
                'va_number'      => $vaNumber,
                'va_expired_at'  => $expiredAt,
                'external_id'    => $trxId,
                'is_fallback_va' => false,
            ];

        } catch (PaymentException $e) {
            throw $e; // re-throw tanpa dibungkus lagi

        } catch (\Throwable $e) {
            Log::error('[BNIService] Gagal membuat VA.', [
                'invoice_number' => $order->invoice_number,
                'error'          => $e->getMessage(),
            ]);

            throw new PaymentException(
                message:     'Gagal membuat Virtual Account BNI: ' . $e->getMessage(),
                gatewayName: 'bni',
                rawResponse: $e,
                previous:    $e,
            );
        }
    }

    /**
     * Inquiry status pembayaran VA ke BNI (untuk rekonsiliasi / self-healing job).
     *
     * Mengembalikan array normalisasi:
     * [
     *   'is_paid'        => bool,
     *   'is_cancelled'   => bool,
     *   'external_id'    => string,
     *   'payment_type'   => 'bank_transfer',
     *   'paid_at'        => string|null,
     *   'raw'            => array,
     * ]
     *
     * @throws PaymentException
     */
    public function checkTransactionStatus(string $externalId): array
    {
        try {
            $response = $this->ecollection->inquiryBilling(
                trxId: $externalId,
            );

            $this->assertSuccess($response, 'inquiryBilling');

            // paymentFlagStatus: 'Y' = sudah dibayar
            $paymentFlag = $response->paymentFlagStatus ?? $response->paymentStatus ?? '';
            $isPaid      = in_array(strtoupper($paymentFlag), ['Y', 'PAID', '1'], true);

            $paidAt = null;
            if ($isPaid) {
                $rawDate = $response->paymentDate ?? $response->datetimePayment ?? null;
                $paidAt  = $rawDate
                    ? Carbon::parse($rawDate)->toDateTimeString()
                    : now()->toDateTimeString();
            }

            return [
                'is_paid'        => $isPaid,
                'is_cancelled'   => false, // VA BNI tidak mengenal status cancelled
                'external_id'    => $externalId,
                'payment_type'   => 'bank_transfer',
                'paid_at'        => $paidAt,
                'raw'            => (array) $response,
            ];

        } catch (PaymentException $e) {
            throw $e;

        } catch (\Throwable $e) {
            Log::error('[BNIService] Gagal inquiry VA.', [
                'external_id' => $externalId,
                'error'       => $e->getMessage(),
            ]);

            throw new PaymentException(
                message:     'Gagal inquiry status VA BNI: ' . $e->getMessage(),
                gatewayName: 'bni',
                rawResponse: $e,
                previous:    $e,
            );
        }
    }

    /**
     * Normalisasi payload webhook/callback BNI menjadi format standar aplikasi.
     *
     * BNI Ecollection mengirim callback ke endpoint kita saat VA dibayar.
     * Contoh payload JSON yang dikirim BNI:
     * {
     *   "trxId"             : "INV-20240115-001",
     *   "virtualAccount"    : "8002123456789012",
     *   "customerName"      : "Budi Santoso",
     *   "trxAmount"         : "100000",
     *   "paymentAmount"     : "100000",
     *   "paymentDate"       : "2024-01-15T10:30:00+07:00",
     *   "journalNum"        : "2024011500001",
     *   "paymentFlagStatus" : "Y"
     * }
     *
     * @throws PaymentException
     */
    public function handleWebhookPayload(array $payload): array
    {
        $trxId = $payload['trxId'] ?? null;

        if (empty($trxId)) {
            throw new PaymentException(
                message:     'Payload webhook BNI tidak mengandung trxId.',
                gatewayName: 'bni',
                rawResponse: $payload,
            );
        }

        $paymentFlag = $payload['paymentFlagStatus'] ?? $payload['paymentStatus'] ?? '';
        $isPaid      = in_array(strtoupper($paymentFlag), ['Y', 'PAID', '1'], true);

        $paidAt = null;
        if ($isPaid) {
            $rawDate = $payload['paymentDate'] ?? $payload['datetimePayment'] ?? null;
            $paidAt  = $rawDate
                ? Carbon::parse($rawDate)->toDateTimeString()
                : now()->toDateTimeString();
        }

        return [
            'is_paid'        => $isPaid,
            'is_cancelled'   => false,
            'external_id'    => $trxId,                              // = invoice_number kita
            'transaction_id' => $payload['journalNum'] ?? null,      // ID jurnal dari BNI
            'payment_type'   => 'bank_transfer',
            'paid_at'        => $paidAt,
        ];
    }

    // -------------------------------------------------------------------------
    // BNI-Specific Public Methods
    // -------------------------------------------------------------------------

    /**
     * Update billing — misal perpanjangan expired atau perubahan nominal.
     *
     * @throws PaymentException
     */
    public function updateBilling(Order $order, ?Carbon $newExpiry = null): array
    {
        $trxId     = $this->sanitizeTrxId($order->invoice_number);
        $expiredAt = $newExpiry ?? $this->resolveExpiry();
        $user      = $order->user;

        try {
            $response = $this->ecollection->updateBilling(
                trxId:           $trxId,
                trxAmount:       (string) (int) $order->total_amount,
                customerName:    $this->sanitizeName($user->name ?? 'Pelanggan'),
                customerEmail:   $user->email ?? '',
                customerPhone:   $user->phone ?? '',
                virtualAccount:  $order->va_number ?? '',
                datetimeExpired: $expiredAt->format('Y-m-d\TH:i:sP'),
                description:     'Pembayaran ' . $order->invoice_number,
            );

            $this->assertSuccess($response, 'updateBilling');

            return [
                'va_expired_at' => $expiredAt,
            ];

        } catch (PaymentException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new PaymentException(
                message:     'Gagal update billing BNI: ' . $e->getMessage(),
                gatewayName: 'bni',
                rawResponse: $e,
                previous:    $e,
            );
        }
    }

    /**
     * Nonaktifkan VA BNI — dipanggil saat order dibatalkan.
     *
     * @throws PaymentException
     */
    public function inactivateBilling(Order $order): void
    {
        if (empty($order->va_number)) {
            return;
        }

        $trxId = $this->sanitizeTrxId($order->invoice_number);

        try {
            $response = $this->ecollection->inactiveBilling(
                trxId:          $trxId,
                virtualAccount: $order->va_number,
            );

            $this->assertSuccess($response, 'inactiveBilling');

            Log::info('[BNIService] VA berhasil dinonaktifkan.', [
                'invoice_number' => $order->invoice_number,
                'va_number'      => $order->va_number,
            ]);

        } catch (PaymentException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('[BNIService] Gagal menonaktifkan VA.', [
                'invoice_number' => $order->invoice_number,
                'error'          => $e->getMessage(),
            ]);

            throw new PaymentException(
                message:     'Gagal menonaktifkan billing BNI: ' . $e->getMessage(),
                gatewayName: 'bni',
                rawResponse: $e,
                previous:    $e,
            );
        }
    }

    // -------------------------------------------------------------------------
    // Webhook Signature Verification
    // -------------------------------------------------------------------------

    /**
     * Verifikasi callback dari BNI.
     *
     * BNI Ecollection umumnya menggunakan IP whitelist sebagai mekanisme
     * keamanan callback. Daftarkan IP BNI di config payment.bni.webhook_allowed_ips.
     *
     * Jika BNI Anda dikonfigurasi menggunakan HMAC signature,
     * aktifkan blok Opsi B di bawah dan sesuaikan nama header-nya.
     */
    public function verifyWebhookSignature(array $payload, string $rawBody = ''): bool
    {
        // Opsi A: IP Whitelist (paling umum untuk BNI Ecollection)
        $allowedIps = array_filter(
            explode(',', config('payment.bni.webhook_allowed_ips', '')),
        );

        if (! empty($allowedIps)) {
            $requestIp = request()->ip();

            if (! in_array($requestIp, $allowedIps, true)) {
                Log::warning('[BNIService] Webhook dari IP tidak terdaftar.', [
                    'ip'          => $requestIp,
                    'allowed_ips' => $allowedIps,
                ]);
                return false;
            }
        }

        // Opsi B: HMAC-SHA256 Signature
        // Aktifkan jika BNI mengirim signature header.
        //
        // $incomingSignature = request()->header('X-BNI-Signature', '');
        // if (! empty($incomingSignature)) {
        //     $expected = hash_hmac('sha256', $rawBody, config('payment.bni.api_secret'));
        //     return hash_equals($expected, $incomingSignature);
        // }

        return true;
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Waktu kadaluarsa VA dari konfigurasi BNI_VA_EXPIRY_HOURS.
     */
    private function resolveExpiry(): Carbon
    {
        $hours = (int) config('payment.bni.va_expiry_hours', 24);

        return now()->addHours($hours);
    }

    /**
     * BNI trxId: maksimal 18 karakter, hanya alfanumerik dan strip.
     */
    private function sanitizeTrxId(string $invoiceNumber): string
    {
        return substr(
            preg_replace('/[^a-zA-Z0-9\-]/', '', $invoiceNumber),
            0,
            18,
        );
    }

    /**
     * Nama customer BNI: maksimal 30 karakter, hanya huruf/angka/spasi.
     */
    private function sanitizeName(string $name): string
    {
        return substr(
            preg_replace('/[^a-zA-Z0-9 ]/', '', $name),
            0,
            30,
        );
    }

    /**
     * Validasi response code dari BNI Ecollection.
     * Sukses: '0000', '00', atau '000' tergantung versi endpoint.
     *
     * @throws PaymentException
     */
    private function assertSuccess(mixed $response, string $operation): void
    {
        $code = $response->responseCode ?? $response->status ?? null;

        if ($code !== null && ! in_array((string) $code, ['0000', '00', '000'], true)) {
            throw new PaymentException(
                message: sprintf(
                    '[BNI Ecollection] %s gagal. responseCode: %s, message: %s',
                    $operation,
                    $code,
                    $response->responseMessage ?? '-',
                ),
                gatewayName: 'bni',
                rawResponse: (array) $response,
            );
        }
    }
}

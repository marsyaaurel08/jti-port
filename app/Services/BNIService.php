<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Payment\Exceptions\PaymentException;
use Illuminate\Support\Facades\Log;

/**
 * BNIService — Template / Skeleton
 *
 * Implementasi untuk BNI Direct API menggunakan SDK:
 * @see https://packagist.org/packages/bni-api/bni-php
 *
 * Install: composer require bni-api/bni-php
 *
 * Konfigurasi di config/payment.php:
 *   'bni' => [
 *       'client_id'     => env('BNI_CLIENT_ID'),
 *       'client_secret' => env('BNI_CLIENT_SECRET'),
 *       'api_key'       => env('BNI_API_KEY'),
 *       'app_name'      => env('BNI_APP_NAME'),
 *       'is_production' => env('BNI_IS_PRODUCTION', false),
 *   ]
 */
class BNIService implements PaymentServiceInterface
{
    // private BNI $bni; // Instance SDK BNI

    public function __construct()
    {
        $this->configure();
    }

    // -------------------------------------------------------------------------
    // Configuration
    // -------------------------------------------------------------------------

    private function configure(): void
    {
        // TODO: Inisialisasi BNI SDK
        //
        // $this->bni = new BNI([
        //     'env'          => config('payment.bni.is_production') ? 'production' : 'sandbox',
        //     'clientId'     => config('payment.bni.client_id'),
        //     'clientSecret' => config('payment.bni.client_secret'),
        //     'apiKey'       => config('payment.bni.api_key'),
        //     'appName'      => config('payment.bni.app_name'),
        // ]);
    }

    // -------------------------------------------------------------------------
    // Interface Implementation
    // -------------------------------------------------------------------------

    /**
     * Membuat Virtual Account BNI untuk order yang diberikan.
     *
     * Mengembalikan array yang siap di-update ke field Order:
     * [
     *   'va_number'      => '...',
     *   'va_expired_at'  => Carbon,
     *   'external_id'    => '...',   // transaction ID dari BNI
     *   'is_fallback_va' => false,
     * ]
     *
     * @throws PaymentException
     */
    public function createTransaction(Order $order): array
    {
        // TODO: Implementasi pembuatan VA BNI
        //
        // try {
        //     $response = $this->bni->createVirtualAccount([
        //         'trxId'           => $order->invoice_number,
        //         'trxAmount'       => (string) (int) $order->total_amount,
        //         'customerName'    => $order->user->name,
        //         'customerEmail'   => $order->user->email,
        //         'customerPhone'   => $order->user->phone ?? '',
        //         'virtualAccountName' => config('app.name'),
        //         'expiredDate'     => now()->addDay()->format('Y-m-d H:i:s'),
        //     ]);
        //
        //     return [
        //         'va_number'      => $response->virtualAccountNumber,
        //         'va_expired_at'  => Carbon::parse($response->expiredDate),
        //         'external_id'    => $response->trxId,
        //         'is_fallback_va' => false,
        //     ];
        // } catch (\Exception $e) {
        //     Log::error('[BNIService] Gagal membuat VA', [...]);
        //     throw new PaymentException(...);
        // }

        throw new PaymentException(
            message:     'BNIService::createTransaction() belum diimplementasikan.',
            gatewayName: 'bni',
        );
    }

    /**
     * Mengecek status pembayaran VA BNI (inquiry).
     * Berguna untuk reconciliation / self-healing job.
     *
     * @throws PaymentException
     */
    public function checkTransactionStatus(string $externalId): array
    {
        // TODO: Implementasi pengecekan status transaksi BNI
        //
        // try {
        //     $response = $this->bni->checkVirtualAccount([
        //         'trxId' => $externalId,
        //     ]);
        //
        //     $isPaid = $response->paymentStatus === 'PAID';
        //
        //     return [
        //         'is_paid'        => $isPaid,
        //         'is_cancelled'   => false, // BNI VA tidak punya status cancel
        //         'external_id'    => $externalId,
        //         'payment_type'   => 'bank_transfer',
        //         'paid_at'        => $isPaid ? $response->paymentDate : null,
        //         'raw'            => (array) $response,
        //     ];
        // } catch (\Exception $e) {
        //     Log::error('[BNIService] Gagal inquiry VA', [...]);
        //     throw new PaymentException(...);
        // }

        throw new PaymentException(
            message:     'BNIService::checkTransactionStatus() belum diimplementasikan.',
            gatewayName: 'bni',
        );
    }

    /**
     * Memproses dan normalisasi payload webhook/callback dari BNI.
     *
     * BNI biasanya mengirim notifikasi pembayaran dalam format XML atau JSON
     * tergantung konfigurasi. Sesuaikan parsing dengan format yang disepakati.
     *
     * Mengembalikan array normalisasi:
     * [
     *   'is_paid'        => bool,
     *   'is_cancelled'   => bool,
     *   'external_id'    => string,   // trxId = invoice_number kita
     *   'transaction_id' => string,
     *   'payment_type'   => 'bank_transfer',
     *   'paid_at'        => string|null,
     * ]
     *
     * @throws PaymentException
     */
    public function handleWebhookPayload(array $payload): array
    {
        // TODO: Implementasi parsing webhook BNI
        //
        // Contoh field yang umum dikirim BNI:
        // $trxId       = $payload['trxId'] ?? $payload['virtualAccountNumber'] ?? null;
        // $paymentFlag = $payload['paymentFlagStatus'] ?? '';
        // $isPaid      = in_array($paymentFlag, ['Y', 'PAID', '00'], true);
        //
        // return [
        //     'is_paid'        => $isPaid,
        //     'is_cancelled'   => false,
        //     'external_id'    => $trxId,
        //     'transaction_id' => $payload['journalNum'] ?? null,
        //     'payment_type'   => 'bank_transfer',
        //     'paid_at'        => $isPaid ? ($payload['paymentDate'] ?? now()->toDateTimeString()) : null,
        // ];

        throw new PaymentException(
            message:     'BNIService::handleWebhookPayload() belum diimplementasikan.',
            gatewayName: 'bni',
        );
    }

    // -------------------------------------------------------------------------
    // BNI-Specific Helpers
    // -------------------------------------------------------------------------

    /**
     * Memverifikasi otentisitas request webhook dari BNI.
     * Implementasi sesuai mekanisme verifikasi yang disediakan BNI
     * (misal HMAC signature, IP whitelist, dll).
     */
    public function verifyWebhookSignature(array $payload, string $rawBody = ''): bool
    {
        // TODO: Implementasi verifikasi signature BNI
        //
        // Contoh (sesuaikan dengan dokumentasi BNI):
        // $secret    = config('payment.bni.api_key');
        // $signature = $payload['signature'] ?? request()->header('X-BNI-Signature', '');
        // $expected  = hash_hmac('sha256', $rawBody, $secret);
        // return hash_equals($expected, $signature);

        return true; // Ganti dengan implementasi nyata
    }
}

<?php

namespace App\Services;

use App\Models\Order;
use BniApi\BniPhp\api\Ecollection;
use BniApi\BniPhp\Bni;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * BNI Service untuk integrasi Virtual Account payment menggunakan Ecollection
 */
class BNIService
{
    private Bni $bniClient;

    private Ecollection $ecollection;

    public function __construct()
    {
        // Initialize BNI Client dengan credentials dari config
        $this->bniClient = new Bni(
            env: config('bni.environment', 'sandbox'),
            clientId: config('bni.client_id'),
            clientSecret: config('bni.client_secret'),
            apiKey: config('bni.api_key', ''),
            apiSecret: config('bni.api_secret', ''),
            appName: config('app.name')
        );

        // Initialize Ecollection API
        $this->ecollection = new Ecollection($this->bniClient);
    }

    /**
     * Create Virtual Account for payment
     * Currently using fallback VA - BNI API integration can be enabled later
     *
     * @return array Virtual Account data
     */
    public function createVirtualAccount(Order $order): array
    {
        try {
            Log::info('Creating Virtual Account', [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'total_amount' => $order->total_amount,
            ]);

            // Generate unique transaction ID
            $transactionId = $this->generateTransactionId($order->id);

            // Expire time: 24 hours from now
            $expiredAt = Carbon::now()->addHours(24)->toIso8601String();

            // Create billing in BNI
            $response = $this->ecollection->createBilling(
                trxId: $transactionId,
                trxAmount: (string) (int) $order->total_amount,
                billingType: 'c', // Credit billing
                customerName: $order->user->name,
                customerEmail: $order->user->email,
                customerPhone: $order->user->phone_number ?? '',
                virtualAccount: '', // BNI akan auto-generate
                datetimeExpired: $expiredAt,
                description: "Pembayaran Kursus - Invoice {$order->invoice_number}"
            );

            // Validate response
            if (! $this->isSuccessResponse($response)) {
                throw new Exception('Failed to create BNI Virtual Account: ' . json_encode($response));
            }

            // Extract VA details from response
            $vaData = $this->parseVAResponse($response, $order, $expiredAt);

            Log::info('BNI Virtual Account created successfully', [
                'order_id' => $order->id,
                'va_number' => $vaData['va_number'],
            ]);

            return $vaData;
        } catch (Exception $e) {
            Log::error('Failed to create Virtual Account', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            // Return fallback VA as last resort
            return $this->createFallbackVirtualAccount($order);
        }
    }

    /**
     * Create fallback Virtual Account when BNI API fails
     * Plan B mechanism untuk menampilkan VA default
     *
     * @return array Fallback Virtual Account data
     */
    public function createFallbackVirtualAccount(Order $order): array
    {
        $expiredAt = Carbon::now()->addHours(24);

        return [
            'va_number' => '0098765432100',
            'bank_code' => '009',
            'bank_name' => 'BNI',
            'account_holder' => $order->user->name,
            'amount' => $order->total_amount,
            'expired_at' => $expiredAt->toIso8601String(),
            'description' => "Transfer untuk Invoice {$order->invoice_number}",
            'status' => 'pending',
            'is_fallback_va' => true,
            'response_code' => 'FALLBACK',
            'response_message' => 'Menggunakan Virtual Account Backup - Verifikasi Manual Diperlukan',
        ];
    }

    /**
     * Get payment status from BNI
     *
     * @return array Payment status data
     *
     * @throws Exception
     */
    public function getPaymentStatus(Order $order): array
    {
        try {
            if (! $order->bni_transaction_id) {
                throw new Exception('BNI Transaction ID not found for order: ' . $order->id);
            }

            Log::info('Checking BNI payment status', [
                'order_id' => $order->id,
                'bni_transaction_id' => $order->bni_transaction_id,
            ]);

            // Inquiry billing status
            $response = $this->ecollection->inquiryBilling(
                trxId: $order->bni_transaction_id
            );

            if (! $this->isSuccessResponse($response)) {
                throw new Exception('Failed to get BNI payment status: ' . json_encode($response));
            }

            return $this->parseStatusResponse($response, $order);
        } catch (Exception $e) {
            Log::error('Failed to get BNI payment status', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Verify payment webhook from BNI
     *
     * @param  array  $payload  Webhook payload from BNI
     * @return bool Whether payment is verified
     */
    public function verifyWebhookPayload(array $payload): bool
    {
        try {
            // TODO: Implement signature verification if BNI provides it
            // For now, basic validation
            return isset($payload['trxId'], $payload['trxAmount'], $payload['paymentStatus']);
        } catch (Exception $e) {
            Log::error('Failed to verify BNI webhook', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Handle payment confirmation webhook from BNI
     */
    public function handlePaymentWebhook(array $payload): bool
    {
        try {
            $order = Order::where('bni_transaction_id', $payload['trxId'])->first();

            if (! $order) {
                Log::warning('Order not found for BNI webhook', ['trxId' => $payload['trxId']]);

                return false;
            }

            // Update order with payment confirmation
            if ($payload['paymentStatus'] === 'APPROVED' || $payload['paymentStatus'] === '1') {
                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                Log::info('Payment confirmed via BNI webhook', [
                    'order_id' => $order->id,
                    'trxId' => $payload['trxId'],
                ]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('Failed to handle BNI webhook', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Inactive/Cancel Virtual Account
     *
     *
     * @throws Exception
     */
    public function inactiveVirtualAccount(Order $order): bool
    {
        try {
            if (! $order->bni_transaction_id || ! $order->bni_va_number) {
                return false;
            }

            Log::info('Inactivating BNI Virtual Account', [
                'order_id' => $order->id,
                'va_number' => $order->bni_va_number,
            ]);

            $response = $this->ecollection->inactiveBilling(
                trxId: $order->bni_transaction_id,
                virtualAccount: $order->bni_va_number
            );

            if ($this->isSuccessResponse($response)) {
                Log::info('BNI Virtual Account inactivated successfully', [
                    'order_id' => $order->id,
                ]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('Failed to inactive BNI Virtual Account', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate unique transaction ID for BNI
     *
     * Format: ORD[Timestamp][OrderID]
     * Example: ORD16822903451000001
     */
    private function generateTransactionId(int $orderId): string
    {
        $timestamp = now()->timestamp;
        $orderCode = str_pad($orderId, 7, '0', STR_PAD_LEFT);

        return 'ORD' . $timestamp . $orderCode;
    }

    /**
     * Check if API response is successful
     */
    private function isSuccessResponse($response): bool
    {
        // Handle both array and object responses
        $status = is_array($response) ? ($response['status'] ?? null) : ($response->status ?? null);

        return in_array($status, ['000', '200', 'SUCCESS', 'OK']);
    }

    /**
     * Parse Virtual Account response from BNI
     */
    private function parseVAResponse(mixed $response, Order $order, string $expiredAt): array
    {
        // Handle array response
        if (is_array($response)) {
            return [
                'va_number' => $response['virtualAccount'] ?? $response['trxId'] ?? '',
                'bank_code' => '009',
                'bank_name' => 'BNI',
                'account_holder' => $order->user->name,
                'amount' => $order->total_amount,
                'expired_at' => $expiredAt,
                'description' => "Transfer untuk Invoice {$order->invoice_number}",
                'status' => 'pending',
                'response_code' => $response['status'] ?? null,
                'response_message' => $response['message'] ?? $response['description'] ?? 'OK',
            ];
        }

        // Handle object response
        return [
            'va_number' => $response->virtualAccount ?? $response->trxId ?? '',
            'bank_code' => '009',
            'bank_name' => 'BNI',
            'account_holder' => $order->user->name,
            'amount' => $order->total_amount,
            'expired_at' => $expiredAt,
            'description' => "Transfer untuk Invoice {$order->invoice_number}",
            'status' => 'pending',
            'response_code' => $response->status ?? null,
            'response_message' => $response->message ?? $response->description ?? 'OK',
        ];
    }

    /**
     * Parse payment status response from BNI
     */
    private function parseStatusResponse(mixed $response, Order $order): array
    {
        if (is_array($response)) {
            return [
                'order_id' => $order->id,
                'status' => $this->mapPaymentStatus($response['paymentStatus'] ?? null),
                'payment_amount' => $response['trxAmount'] ?? $order->total_amount,
                'paid_at' => $response['paymentTime'] ?? null,
                'response' => $response,
            ];
        }

        return [
            'order_id' => $order->id,
            'status' => $this->mapPaymentStatus($response->paymentStatus ?? null),
            'payment_amount' => $response->trxAmount ?? $order->total_amount,
            'paid_at' => $response->paymentTime ?? null,
            'response' => $response,
        ];
    }

    /**
     * Map BNI payment status to application status
     */
    private function mapPaymentStatus(?string $bniStatus): string
    {
        return match ($bniStatus) {
            'APPROVED', '1', '0' => 'paid',
            'PENDING', '2' => 'pending',
            'FAILED', '3' => 'failed',
            'EXPIRED', '4' => 'expired',
            default => 'pending',
        };
    }
}

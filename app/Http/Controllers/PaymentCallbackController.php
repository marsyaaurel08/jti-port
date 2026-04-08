<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BNIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Payment Callback Controller
 *
 * Handles webhook callbacks from BNI for payment confirmation
 */
class PaymentCallbackController extends Controller
{
    private BNIService $bniService;

    public function __construct(BNIService $bniService)
    {
        $this->bniService = $bniService;
    }

    /**
     * Handle BNI Ecollection payment webhook
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bniCallback(Request $request): JsonResponse
    {
        try {
            Log::info('BNI Payment Callback received', [
                'payload' => $request->all(),
            ]);

            // Verify webhook payload
            if (! $this->bniService->verifyWebhookPayload($request->all())) {
                Log::warning('Invalid BNI webhook payload', ['payload' => $request->all()]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid webhook payload',
                ], 400);
            }

            // Handle payment confirmation
            $success = $this->bniService->handlePaymentWebhook($request->all());

            if ($success) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment confirmed',
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process payment',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error processing BNI callback', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Check payment status by transaction ID
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'bni_transaction_id' => 'required|string',
            ]);

            $order = Order::where('bni_transaction_id', $request->input('bni_transaction_id'))
                ->firstOrFail();

            $status = $this->bniService->getPaymentStatus($order);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order_id' => $order->id,
                    'payment_status' => $status['status'],
                    'amount' => $status['payment_amount'],
                    'paid_at' => $status['paid_at'],
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check payment status',
            ], 400);
        }
    }

    /**
     * Get order payment details
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function getOrderPayment(Order $order): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'order_id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'status' => $order->status,
                    'amount' => $order->total_amount,
                    'payment_method' => $order->payment_method,
                    'bni_va_number' => $order->bni_va_number,
                    'bni_va_expired_at' => $order->bni_va_expired_at,
                    'paid_at' => $order->paid_at,
                    'created_at' => $order->created_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error getting order payment', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get payment details',
            ], 400);
        }
    }
}

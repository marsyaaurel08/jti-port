<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\PaymentWebhook;
use App\Services\Payment\BNIService;
use App\Services\Payment\MidtransService;
use App\Services\Payment\SelfHealingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $selfHealingService;

    public function __construct(SelfHealingService $selfHealingService)
    {
        $this->selfHealingService = $selfHealingService;
    }

    /**
     * API Trigger Self Healing (untuk testing & load testing)
     */
    public function selfHealing(Request $request)
    {
        // Keamanan: Tidak boleh dijalankan di production
        if (app()->environment('production')) {
            return response()->json([
                'success' => false,
                'message' => 'Self healing trigger tidak diizinkan di production',
            ], 403);
        }

        $startTime = microtime(true);
        $force = $request->boolean('force', false);   // Ambil parameter force

        try {
            Log::info('[SelfHealing API] Self healing dipanggil via API', [
                'force_mode' => $force,
            ]);

            // ← PERBAIKAN: Kirim parameter $force ke service
            $result = $this->selfHealingService->run($force);

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'force_mode' => $force,
                'message' => 'Self Healing berhasil dijalankan',
                'execution_time_ms' => $executionTime,
                'processed' => $result['processed'] ?? 0,
                'success_count' => $result['success'] ?? 0,
                'failed_count' => $result['failed'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('[SelfHealing API] Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi error saat menjalankan self healing',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // -------------------------------------------------------------------------
    // Midtrans Webhook
    // -------------------------------------------------------------------------

    /**
     * Endpoint: POST /webhook/midtrans
     *
     * Midtrans mengirim notifikasi JSON ke sini setiap kali status transaksi berubah.
     * Alur:
     *  1. Catat raw payload ke tabel payment_webhooks (status = pending)
     *  2. Validasi signature key
     *  3. Proses: update Order berdasarkan status Midtrans
     *  4. Update status payment_webhooks (processed / failed)
     */
    public function midtrans(Request $request, MidtransService $midtrans): Response
    {
        $payload = $request->all();

        // 1. Catat webhook masuk terlebih dahulu (raw, sebelum diproses)
        $webhook = $this->recordWebhook(
            gateway: 'midtrans',
            invoiceNumber: $payload['order_id'] ?? null,
            externalId: $payload['transaction_id'] ?? null,
            payload: $payload,
        );

        // 2. Verifikasi signature key Midtrans
        if (! $midtrans->verifyWebhookSignature($payload)) {
            Log::warning('[Webhook][Midtrans] Signature tidak valid.', [
                'order_id' => $payload['order_id'] ?? null,
            ]);

            $this->markWebhookFailed($webhook, 'Signature key tidak valid.');

            // Tetap return 200 agar Midtrans tidak retry terus-menerus
            return response('Unauthorized', 200);
        }

        // 3. Proses webhook
        try {
            $normalized = $midtrans->handleWebhookPayload($payload);
            $this->applyPaymentResult($normalized, $webhook);
            $this->markWebhookProcessed($webhook);
        } catch (\Throwable $e) {
            Log::error('[Webhook][Midtrans] Gagal memproses webhook.', [
                'order_id' => $payload['order_id'] ?? null,
                'error' => $e->getMessage(),
            ]);

            $this->markWebhookFailed($webhook, $e->getMessage());
        }

        // Selalu return 200 agar Midtrans tidak melakukan retry berulang
        return response('OK', 200);
    }

    // -------------------------------------------------------------------------
    // BNI Webhook
    // -------------------------------------------------------------------------

    /**
     * Endpoint: POST /webhook/bni
     *
     * BNI mengirim notifikasi ke sini saat Virtual Account dibayar.
     */
    public function bni(Request $request, BNIService $bniService): Response
    {
        $payload = $request->all();

        // 1. Catat webhook masuk
        $webhook = $this->recordWebhook(
            gateway: 'bni',
            invoiceNumber: $payload['trxId'] ?? $payload['virtualAccountNumber'] ?? null,
            externalId: $payload['journalNum'] ?? null,
            payload: $payload,
        );

        // 2. Verifikasi signature BNI (sesuaikan setelah implementasi BNIService)
        $rawBody = $request->getContent();
        if (! $bniService->verifyWebhookSignature($payload, $rawBody)) {
            Log::warning('[Webhook][BNI] Signature tidak valid.');
            $this->markWebhookFailed($webhook, 'Signature BNI tidak valid.');

            return response('Unauthorized', 200);
        }

        // 3. Proses webhook
        try {
            $normalized = $bniService->handleWebhookPayload($payload);
            $this->applyPaymentResult($normalized, $webhook);
            $this->markWebhookProcessed($webhook);
        } catch (\Throwable $e) {
            Log::error('[Webhook][BNI] Gagal memproses webhook.', [
                'error' => $e->getMessage(),
            ]);

            $this->markWebhookFailed($webhook, $e->getMessage());
        }

        return response('OK', 200);
    }

    // -------------------------------------------------------------------------
    // Core Logic
    // -------------------------------------------------------------------------

    /**
     * Mengaplikasikan hasil normalisasi webhook ke Order yang sesuai.
     * Berjalan dalam DB Transaction untuk menjaga konsistensi data.
     */
    private function applyPaymentResult(array $normalized, PaymentWebhook $webhook): void
    {
        $invoiceNumber = $normalized['external_id'] ?? $webhook->order_invoice_number;

        if (! $invoiceNumber) {
            throw new \RuntimeException('external_id / invoice_number tidak ditemukan di payload.');
        }

        DB::transaction(function () use ($normalized, $invoiceNumber) {
            /** @var Order|null $order */
            $order = Order::where('invoice_number', $invoiceNumber)
                ->lockForUpdate()
                ->first();

            if (! $order) {
                Log::warning('[Webhook] Order tidak ditemukan.', [
                    'invoice_number' => $invoiceNumber,
                ]);

                return;
            }

            // Guard: jangan proses ulang order yang sudah lunas atau dibatalkan
            if (in_array($order->status, ['paid', 'cancelled'], true)) {
                Log::info('[Webhook] Order sudah final, skip.', [
                    'invoice_number' => $invoiceNumber,
                    'status' => $order->status,
                ]);

                return;
            }

            if ($normalized['is_paid']) {
                $order->update([
                    'status' => 'paid',
                    'paid_at' => $normalized['paid_at'] ?? now(),
                    'payment_method' => $normalized['payment_type'] ?? $order->payment_method,
                ]);

                Log::info('[Webhook] Order berhasil dibayar.', [
                    'invoice_number' => $invoiceNumber,
                    'payment_type' => $normalized['payment_type'] ?? '-',
                ]);

                // Daftarkan user ke enrollments dan buat course progress untuk setiap order item
                $orderItems = $order->orderItems;

                foreach ($orderItems as $item) {
                    $enrollment = Enrollment::create([
                        'user_id' => $order->user_id,
                        'course_id' => $item->course_id,
                        'class_id' => $item->class_id,
                        'order_id' => $order->id,
                        'enrolled_at' => now(),
                        'completed_lesson_count' => 0,
                    ]);

                    // Buat CourseProgress untuk semua lesson pada course (jika tersedia)
                    $course = $item->course;
                    if ($course) {
                        foreach ($course->chapters as $chapter) {
                            foreach ($chapter->lessons as $lesson) {
                                CourseProgress::create([
                                    'enrollment_id' => $enrollment->id,
                                    'lesson_id' => $lesson->id,
                                    'is_completed' => false,
                                ]);
                            }
                        }
                    }
                }
                // TODO: Trigger event / notifikasi ke user setelah pembayaran berhasil
                // event(new OrderPaid($order));

            } elseif ($normalized['is_cancelled']) {
                $order->update(['status' => 'cancelled']);

                Log::info('[Webhook] Order dibatalkan/expired.', [
                    'invoice_number' => $invoiceNumber,
                ]);
            }
        });
    }

    // -------------------------------------------------------------------------
    // PaymentWebhook Record Helpers
    // -------------------------------------------------------------------------

    private function recordWebhook(
        string $gateway,
        ?string $invoiceNumber,
        ?string $externalId,
        array $payload,
    ): PaymentWebhook {
        return PaymentWebhook::create([
            'payment_gateway' => $gateway,
            'order_invoice_number' => $invoiceNumber,
            'external_id' => $externalId,
            'payload' => $payload,
            'status' => 'pending',
        ]);
    }

    private function markWebhookProcessed(PaymentWebhook $webhook): void
    {
        $webhook->update(['status' => 'processed']);
    }

    private function markWebhookFailed(PaymentWebhook $webhook, string $errorMessage): void
    {
        $webhook->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}

<?php

namespace App\Services\Payment;

use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\LogSelfHealing;
use App\Models\Order;
use App\Services\Payment\Exceptions\PaymentException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelfHealingService
{
    /**
     * Backoff schedule — [attempt => minutes_since_order_created].
     *
     * Attempt 1 → cek setelah 5 menit
     * Attempt 2 → cek setelah 30 menit
     * Attempt 3 → cek setelah 90 menit -> 1.5 jam
     * Attempt 4 → cek setelah 300 menit -> 5 jam
     * Attempt 5 → cek setelah 600 menit -> 10 jam
     * Attempt 6 → cek setelah 1440 menit -> 24 jam
     */
    private const BACKOFF_MINUTES = [
        1 => 5,
        2 => 30,
        3 => 90,
        4 => 300,
        5 => 600,
        6 => 1440,
    ];

    private const MAX_ATTEMPTS = 6;

    // -------------------------------------------------------------------------
    // Entry Point — dipanggil oleh Scheduler
    // -------------------------------------------------------------------------

    /**
     * Jalankan satu siklus self-healing:
     * 1. Temukan semua candidate
     * 2. Proses tiap candidate ke gateway
     * 3. Catat hasilnya di logs_self_healing
     */
    public function run(): void
    {
        $candidates = $this->detectCandidates();

        if ($candidates->isEmpty()) {
            Log::info('[SelfHealing] Tidak ada kandidat yang perlu diproses.');

            return;
        }

        Log::info('[SelfHealing] Memulai siklus.', ['total' => $candidates->count()]);

        foreach ($candidates as $order) {
            $this->reconcile($order);
        }

        Log::info('[SelfHealing] Siklus selesai.');
    }

    // -------------------------------------------------------------------------
    // Candidate Detection
    // -------------------------------------------------------------------------

    /**
     * Mengambil order yang memenuhi semua kriteria self-healing:
     *
     * - Status masih 'pending'
     * - Menggunakan gateway otomatis (bni / midtrans), bukan manual
     * - Tidak menggunakan fallback VA
     * - Sudah melewati minimal 10 menit sejak dibuat (attempt 1)
     * - Belum melewati batas maksimum attempt
     * - Belum ada log attempt pada interval yang sedang berjalan
     */
    private function detectCandidates(): Collection
    {
        // Waktu minimum: order harus sudah berumur > 10 menit
        $minAge = now()->subMinutes(self::BACKOFF_MINUTES[1]);

        // Waktu maksimum: order yang sudah > 8 jam + buffer dianggap expired
        $maxAge = now()->subMinutes(self::BACKOFF_MINUTES[self::MAX_ATTEMPTS])->subHour();

        return Order::query()
            ->where('status', 'pending')
            ->whereIn('payment_gateway', ['bni', 'midtrans'])
            ->where('is_fallback_va', false)
            ->where('created_at', '<=', $minAge)     // minimal sudah 10 menit
            ->where('created_at', '>=', $maxAge)     // jangan proses yang terlalu lama
            ->whereDoesntHave('paymentWebhooks')     // belum ada webhook masuk
            ->where(function ($q) {
                // Belum pernah di-attempt, ATAU attempt terakhirnya sudah lewat window berikutnya
                $q->whereDoesntHave('selfHealingLogs')
                    ->orWhereHas('selfHealingLogs', function ($sub) {
                        $sub->where('attempt', '<', self::MAX_ATTEMPTS)
                            ->where('result', '!=', 'paid');
                    });
            })
            ->with(['user', 'selfHealingLogs'])
            ->get()
            ->filter(fn(Order $order) => $this->isDueForAttempt($order));
    }

    /**
     * Memeriksa apakah order ini sudah waktunya di-attempt berikutnya.
     */
    private function isDueForAttempt(Order $order): bool
    {
        $nextAttempt = $order->selfHealingLogs->count() + 1;
        $minutesNeeded = self::BACKOFF_MINUTES[$nextAttempt] ?? null;

        if ($minutesNeeded === null) {
            return false; // Sudah melewati MAX_ATTEMPTS
        }

        // Order harus sudah berumur >= minutesNeeded menit
        return $order->created_at->diffInMinutes(now()) >= $minutesNeeded;
    }

    // -------------------------------------------------------------------------
    // Reconciliation
    // -------------------------------------------------------------------------

    /**
     * Proses satu order: cek ke gateway, update order jika lunas, catat log.
     */
    private function reconcile(Order $order): void
    {
        $attempt = $order->selfHealingLogs->count() + 1;
        $gateway = $order->payment_gateway;

        Log::info('[SelfHealing] Memproses order.', [
            'invoice_number' => $order->invoice_number,
            'gateway' => $gateway,
            'attempt' => $attempt,
        ]);

        // Guard: skip jika order sudah final (mungkin diproses bersamaan oleh webhook)
        $order->refresh();
        if (in_array($order->status, ['paid', 'cancelled'], true)) {
            $this->log($order, $attempt, 'skipped', gateway: $gateway);

            return;
        }

        try {
            $paymentService = PaymentFactory::make($gateway);
            $externalId = $order->external_id ?? $order->invoice_number;
            $statusResult = $paymentService->checkTransactionStatus($externalId);

            if ($statusResult['is_paid']) {
                $this->markAsPaid($order, $statusResult);
                $this->log($order, $attempt, 'paid', raw: $statusResult, gateway: $gateway);

                Log::info('[SelfHealing] Order berhasil diupdate ke paid.', [
                    'invoice_number' => $order->invoice_number,
                    'attempt' => $attempt,
                ]);
            } else {
                $this->log($order, $attempt, 'pending', raw: $statusResult, gateway: $gateway);

                Log::info('[SelfHealing] Order masih pending di gateway.', [
                    'invoice_number' => $order->invoice_number,
                    'attempt' => $attempt,
                ]);
            }
        } catch (PaymentException $e) {
            $this->log(
                order: $order,
                attempt: $attempt,
                result: 'error',
                error: $e->getMessage(),
                gateway: $gateway,
            );

            Log::warning('[SelfHealing] Gagal cek gateway.', [
                'invoice_number' => $order->invoice_number,
                'attempt' => $attempt,
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Order Update
    // -------------------------------------------------------------------------

    private function markAsPaid(Order $order, array $statusResult): void
    {
        DB::transaction(function () use ($order, $statusResult) {
            $order->refresh()->lockForUpdate();

            // Double-check setelah lock — hindari race condition dengan webhook
            if ($order->status === 'paid') {
                return;
            }

            $order->update([
                'status' => 'paid',
                'paid_at' => $statusResult['paid_at'] ?? now(),
            ]);

            $this->processEnrollments($order);
        });
    }

    private function processEnrollments(Order $order): void
    {
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

            // Create CourseProgress records for all lessons in the course
            $course = $item->course;
            if ($course) {
                $lessons = $course->chapters()->first()->lessons()->get();
                foreach ($lessons as $lesson) {
                    CourseProgress::create([
                        'enrollment_id' => $enrollment->id,
                        'lesson_id' => $lesson->id,
                        'is_completed' => false,
                    ]);
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // Logging
    // -------------------------------------------------------------------------

    private function log(
        Order $order,
        int $attempt,
        string $result,
        string $gateway = '',
        ?string $error = null,
        array $raw = [],
    ): void {
        LogSelfHealing::create([
            'order_id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'payment_gateway' => $gateway ?: $order->payment_gateway,
            'attempt' => $attempt,
            'result' => $result,
            'error_message' => $error,
            'raw_response' => ! empty($raw) ? $raw : null,
        ]);
    }
}

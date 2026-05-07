<?php

namespace App\Services\Payment\Contracts;

use App\Models\Order;

interface PaymentServiceInterface
{
    /**
     * Membuat transaksi baru ke Payment Gateway.
     * Mengembalikan array berisi field yang akan di-update ke model Order.
     * Contoh: ['va_number' => '...', 'va_expired_at' => '...', 'snap_token' => '...']
     *
     * @param  Order  $order
     * @return array<string, mixed>
     *
     * @throws \App\Services\Payment\Exceptions\PaymentException
     */
    public function createTransaction(Order $order, ?string $preferredMethod = null): array;

    /**
     * Memverifikasi status transaksi dari Payment Gateway.
     *
     * @param  string  $externalId  ID transaksi dari gateway
     * @return array<string, mixed>
     *
     * @throws \App\Services\Payment\Exceptions\PaymentException
     */
    public function checkTransactionStatus(string $externalId): array;

    /**
     * Memproses payload webhook yang masuk dari Payment Gateway.
     * Mengembalikan array hasil normalisasi (status, external_id, dll).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     *
     * @throws \App\Services\Payment\Exceptions\PaymentException
     */
    public function handleWebhookPayload(array $payload): array;
}

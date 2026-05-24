<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model LogSelfHealing
 *
 * Model ini digunakan untuk mencatat log self-healing/reconciliation payment
 * untuk tracking percobaan verifikasi pembayaran dari payment gateway.
 *
 * @property int $id
 * @property int $order_id
 * @property string $invoice_number
 * @property string $payment_gateway 'bni' | 'midtrans'
 * @property int $attempt Nomor percobaan ke-N
 * @property string $result 'paid' | 'pending' | 'error' | 'skipped'
 * @property string|null $error_message Pesan error jika result = error
 * @property array|null $raw_response Raw response dari gateway
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Order $order
 */
class LogSelfHealing extends Model
{
    protected $table = 'logs_self_healing';

    protected $fillable = [
        'order_id',
        'invoice_number',
        'payment_gateway',
        'attempt',
        'result',
        'error_message',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'raw_response' => 'array',
            'attempt' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model LogSelfHealing
 *
 * Model ini digunakan untuk mencatat aktivitas self-healing pada sistem,
 * terutama untuk reconciliation logs antara BNI dan Midtrans.
 *
 * @property int $id
 * @property int $order_id
 * @property string $gateway
 * @property string $action
 * @property array|null $request_payload
 * @property array|null $response_payload
 * @property string $status
 * @property string|null $notes
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

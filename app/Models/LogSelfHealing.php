<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model LogSelfHealing
 *
 * Model ini digunakan untuk mencatat aktivitas self-healing pada sistem,
 * terutama perubahan status order secara otomatis oleh sistem.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $order_id
 * @property string|null $order_type
 * @property string|null $invoice_number
 * @property string|null $status_before
 * @property string|null $status_after
 * @property string|null $message
 * @property array|null $payload
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \App\Models\User $user
 *
 * @package App\Models
 */
class LogSelfHealing extends Model
{
    /**
     * Nama tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'logs_self_healing';

    /**
     * Kolom yang boleh diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'order_type',
        'invoice_number',
        'status_before',
        'status_after',
        'message',
        'payload',
    ];

    /**
     * Casting attribute untuk kolom tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Relasi ke model User.
     *
     * Setiap log self-healing dimiliki oleh satu user (biasanya user yang
     * terkait dengan order atau sistem yang menjalankan self-healing).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

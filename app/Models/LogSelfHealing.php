<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogSelfHealing extends Model
{
    protected $table = 'logs_self_healing';

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

    protected $casts = [
        'payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

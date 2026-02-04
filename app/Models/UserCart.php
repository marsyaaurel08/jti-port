<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class UserCart extends Model
{
    protected $fillable = [
        'user_id',
        'chart_data',
    ];

    protected function casts(): array
    {
        return [
            'chart_data' => 'json',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

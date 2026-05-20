<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order',
        'max_usage',
        'max_usage_per_user',
        'start_date',
        'end_date',
        'is_active',
        'for_all_products',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'string',
            'value' => 'decimal:2',
            'min_order' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
            'for_all_products' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(PromoProduct::class);
    }

    public function userPromos(): HasMany
    {
        return $this->hasMany(UserPromo::class);
    }
}

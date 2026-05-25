<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'promo_id',
        'product_id',
        'type_product',
    ];

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'product_id')->where('type_product', 'course');
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'product_id')->where('type_product', 'class');
    }
}

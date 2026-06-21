<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'course_id',
        'class_id',
        'price_at_purchase',
        'item_name',
    ];

    protected function casts(): array
    {
        return [
            'price_at_purchase' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id')->withTrashed();
    }
}

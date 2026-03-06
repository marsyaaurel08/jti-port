<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseClass extends Model
{
    protected $table = 'classes';

    use HasFactory;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'category_id',
        'level',
        'thumbnail_url',
        'description',
        'title',
        'slug',
        'price',
        'start_date',
        'end_date',
        'max_capacity',
        'current_enrolled',
        'meeting_link',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'max_capacity' => 'integer',
            'current_enrolled' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        // Tambahkan parameter kedua untuk menentukan kolom 'class_id' secara eksplisit
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function userCarts(): HasMany
    {
        return $this->hasMany(UserCart::class);
    }
    public function available_slots(): int
    {
        return $this->max_capacity - $this->current_enrolled;
    }
}

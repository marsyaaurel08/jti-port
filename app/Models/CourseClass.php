<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseClass extends Model
{
    protected $table = 'classes';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'instructor_id',
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
        return $this->belongsTo(Course::class)->withTrashed();
    }

    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(Category::class, Course::class, 'id', 'id', 'course_id', 'category_id');
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

<?php

namespace App\Models;

use App\Observers\CourseObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\{HasMedia, InteractsWithMedia};

class Course extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, LogsActivity;

    /**
     * Register the observer for the model.
     */
    protected static function booted(): void
    {
        static::observe(CourseObserver::class);
    }

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'price',
        'level',
        'thumbnail_url',
        'status',
        'total_duration',
        'total_lessons',
        'avg_rating',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'avg_rating' => 'decimal:2',
        ];
    }

    /**
     * Get the thumbnail URL from media library or fall back to thumbnail_url field.
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at', 'deleted_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Course {$eventName}");
    }
}

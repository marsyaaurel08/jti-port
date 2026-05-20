<?php

namespace App\Models;

use App\Observers\LessonObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lesson extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Register the observer for the model.
     */
    protected static function booted(): void
    {
        static::observe(LessonObserver::class);
    }

    protected $fillable = [
        'chapter_id',
        'title',
        'type',
        'slug',
        'content_url',
        'content_text',
        'text_after_video',
        'text_position',
        'duration_seconds',
        'is_free_preview',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'is_free_preview' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'last_accessed_lesson_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Lesson {$eventName}");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'title',
        'type',
        'slug',
        'content_url',
        'content_text',
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

    /**
     * Get the YouTube embed URL from the content_url
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->content_url) {
            return null;
        }

        // Extract video ID from various YouTube URL formats
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/)([a-zA-Z0-9_-]{11})/',
            '/^([a-zA-Z0-9_-]{11})$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->content_url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        // If it's already an embed URL, return it as is
        if (str_contains($this->content_url, 'youtube.com/embed/')) {
            return $this->content_url;
        }

        return null;
    }
}

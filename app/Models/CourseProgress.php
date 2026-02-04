<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'progress_percentage',
        'last_accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'last_accessed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}

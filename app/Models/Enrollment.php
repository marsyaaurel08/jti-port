<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

use App\Observers\EnrollmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([EnrollmentObserver::class])]
class Enrollment extends Model
{
    use HasFactory, HasRoles, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'class_id',
        'order_id',
        'enrolled_at',
        'expires_at',
        'is_completed',   // apakah course / class sudah selesai
        'completed_at',
        'certificate_code',
        'certificate_url',
        'completed_lesson_count', // jumlah pelajaran yang sudah diselesaikan, ini bisa dihitung dari CourseProgress tapi disimpan di sini untuk query yang lebih cepat
        'last_accessed_lesson_id', // untuk menyimpan pelajaran terakhir yang diakses, memudahkan untuk melanjutkan belajar
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'completed_lesson_count' => 'integer',
        ];
    }

    protected $appends = [
        'progress_percentage',
        'status',
        'actual_completed_lesson_count',
    ];


    //  Get the actual completed lesson count from CourseProgress records.
    //  This is the single source of truth for lesson completion.

    public function getActualCompletedLessonCountAttribute(): int
    {
        if ($this->relationLoaded('progress')) {
            return $this->progress->where('is_completed', true)->count();
        }

        return $this->progress()->where('is_completed', true)->count();
    }

    public function getProgressPercentageAttribute(): float
    {
        if (!$this->course || $this->course->total_lessons === 0) {
            return 0.0;
        }
        return round(($this->actual_completed_lesson_count / $this->course->total_lessons) * 100, 2);
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_completed) {
            return 'completed';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }
    public function class(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id')->withTrashed();
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function lastAccessedLesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'last_accessed_lesson_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }

    public function isStudent(): bool
    {
        return !$this->hasAnyRole(['admin', 'super_admin', 'pengajar']);
    }
}

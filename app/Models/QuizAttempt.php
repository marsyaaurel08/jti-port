<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'started_at',
        'finished_at',
        'current_question_index',
        'total_score_obtained',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'total_score_obtained' => 'decimal:2',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function getTotalScoreWeight(): int
    {
        return $this->quiz->total_score_weight ?? 0;
    }

    public function getTotalScoreObtained(): float
    {
        return (float) $this->answers()->sum('score_obtained');
    }

    public function isPassed(): bool
    {
        return $this->score >= $this->quiz->pass_score;
    }
}

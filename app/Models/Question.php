<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'type',
        'question_text',
        'question_config',
        'answer_key',
        'explanation',
        'hint',
        'score_weight',
    ];

    protected function casts(): array
    {
        return [
            'question_config' => 'array',
            'answer_key' => 'array',
            'score_weight' => 'integer',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }
}

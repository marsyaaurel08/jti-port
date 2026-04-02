<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Log;

class QuizScoringService
{

    /**
     * Grade entire attempt
     */
    public function gradeAttempt(QuizAttempt $attempt): void
    {
        Log::info('Grading attempt', ['attempt_id' => $attempt->id]);

        $questions = $attempt->quiz->questions;

        foreach ($questions as $question) {
            $answer = $attempt->answers()
                ->where('question_id', $question->id)
                ->first();

            if ($answer) {
                $this->gradeAnswer($answer);
            }
        }

        $totalWeight = $attempt->getTotalScoreWeight();
        $totalObtained = $attempt->getTotalScoreObtained();

        $finalScore = $totalWeight > 0
            ? ($totalObtained / $totalWeight) * 100
            : 0.0;

        $attempt->update([
            'score' => $finalScore,
            'total_score_obtained' => $totalObtained,
        ]);

        Log::info('Attempt graded', [
            'attempt_id' => $attempt->id,
            'final_score' => $finalScore,
        ]);
    }


    /**
     * Grade a single answer
     */
    public function gradeAnswer(QuizAnswer $answer): void
    {
        $isCorrect = $this->validateAnswer($answer->question, $answer->user_answer);
        $score = $this->calculateQuestionScore($answer->question, $answer->user_answer);
        $answer->update([
            'is_correct' => $isCorrect,
            'score_obtained' => $score,
        ]);
    }

    /**
     * Validate a user's answer
     */
    public function validateAnswer(Question $question, $userAnswer): bool
    {
        $answerKey = $question->answer_key;

        if (empty($userAnswer) || empty($answerKey)) {
            return false;
        }

        if (is_array($answerKey) && isset($answerKey['correct'])) {
            $answerKey = $answerKey['correct'];
        }

        return match ($question->type) {
            'single_choice' => $this->validateSingleChoice($userAnswer, $answerKey),
            'multiple_choice' => $this->validateMultipleChoice($userAnswer, $answerKey),
            'boolean' => $this->validateBoolean($userAnswer, $answerKey),
            'matching', 'drag_drop_sorting', 'drag_drop_zones'
            => $this->validateMapping($userAnswer, $answerKey),
            'fill_in_blank' => $this->validateFillInBlank($userAnswer, $answerKey),
            'essay' => false,
            default => false,
        };
    }

    /**
     * Calculate score per question
     */
    public function calculateQuestionScore(Question $question, $userAnswer): float
    {
        if ($question->type === 'essay') {
            return 0.0;
        }

        $isCorrect = $this->validateAnswer($question, $userAnswer);

        return $isCorrect ? (float) $question->score_weight : 0.0;
    }




    // ======================
    // VALIDATION METHODS
    // ======================

    protected function validateSingleChoice($userAnswer, $answerKey): bool
    {
        if (is_array($answerKey)) {
            $answerKey = $answerKey[0] ?? null;
        }

        return (string) $userAnswer === (string) $answerKey;
    }

    protected function validateMultipleChoice($userAnswer, $answerKey): bool
    {
        if (!is_array($userAnswer) || !is_array($answerKey)) {
            return false;
        }

        sort($userAnswer);
        sort($answerKey);

        return $userAnswer === $answerKey;
    }

    protected function validateBoolean($userAnswer, $answerKey): bool
    {
        return (bool) $userAnswer === (bool) $answerKey;
    }

    protected function validateMapping($userAnswer, $answerKey): bool
    {
        if (!is_array($userAnswer) || !is_array($answerKey)) {
            return false;
        }

        ksort($userAnswer);
        ksort($answerKey);

        return $userAnswer === $answerKey;
    }

    protected function validateFillInBlank($userAnswer, $answerKey): bool
    {
        if (is_array($answerKey)) {
            return in_array(
                strtolower(trim((string) $userAnswer)),
                array_map('strtolower', $answerKey)
            );
        }

        return strtolower(trim((string) $userAnswer)) === strtolower(trim((string) $answerKey));
    }
}

// ssss

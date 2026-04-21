<?php

namespace App\Observers;

use App\Models\Question;

class QuestionObserver
{
    /**
     * Handle the Question "created" event.
     * Update the parent quiz's total_score_weight.
     */
    public function created(Question $question): void
    {
        $this->updateQuizTotalScoreWeight($question);
    }

    /**
     * Handle the Question "updated" event.
     * Update the parent quiz's total_score_weight if score_weight changed.
     */
    public function updated(Question $question): void
    {
        // Only update if score_weight has changed
        if ($question->isDirty('score_weight')) {
            $this->updateQuizTotalScoreWeight($question);
        }
    }

    /**
     * Handle the Question "deleted" event.
     * Update the parent quiz's total_score_weight.
     */
    public function deleted(Question $question): void
    {
        $this->updateQuizTotalScoreWeight($question);
    }

    /**
     * Update the parent quiz's total_score_weight by summing all question score_weights.
     */
    protected function updateQuizTotalScoreWeight(Question $question): void
    {
        if ($question->quiz) {
            $quiz = $question->quiz;
            $totalScoreWeight = $quiz->questions()->sum('score_weight');

            if ($quiz->total_score_weight !== $totalScoreWeight) {
                $quiz->total_score_weight = $totalScoreWeight;
                $quiz->saveQuietly(); // Save without triggering the observer again
            }
        }
    }
}

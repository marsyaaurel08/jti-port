<?php

namespace App\Livewire;

use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class QuizScoreHistory extends Component
{
    public int $quizId;

    public function mount(int $quizId): void
    {
        $this->quizId = $quizId;
    }

    #[Computed]
    public function quizAttempts(): array
    {
        $attempts = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $this->quizId)
            ->whereNotNull('finished_at')
            ->orderBy('finished_at', 'desc')
            ->get();

        $totalAttempts = $attempts->count();

        return $attempts->map(function ($attempt, $index) use ($totalAttempts) {
            return [
                'id' => $attempt->id,
                'attempt_number' => $totalAttempts - $index,
                'score' => $attempt->score,
                'started_at' => $attempt->started_at,
                'finished_at' => $attempt->finished_at,
                'time_taken' => $attempt->duration, // Use the duration accessor
                'is_passed' => $attempt->isPassed(),
                'pass_score' => $attempt->quiz->pass_score,
            ];
        })->toArray();
    }

    #[Computed]
    public function quizStats(): array
    {
        $attempts = $this->quizAttempts;

        if (empty($attempts)) {
            return [
                'total_attempts' => 0,
                'best_score' => 0,
                'average_score' => 0,
                'last_attempt_status' => null,
            ];
        }

        $scores = collect($attempts)->pluck('score');
        $lastAttempt = $attempts[0] ?? null;

        return [
            'total_attempts' => count($attempts),
            'best_score' => $scores->max(),
            'average_score' => round($scores->avg(), 2),
            'last_attempt_status' => $lastAttempt ? ($lastAttempt['is_passed'] ? 'Lulus' : 'Gagal') : null,
        ];
    }

    public function formatTimeTaken($timeTaken): string
    {
        if (!$timeTaken) {
            return '-';
        }

        // If it's already a formatted string (from the duration accessor), return it directly
        if (is_string($timeTaken)) {
            return $timeTaken;
        }

        // Otherwise, parse the HH:MM:SS format
        $parts = explode(':', $timeTaken);

        if (count($parts) === 3) {
            // HH:MM:SS format
            $hours = (int) $parts[0];
            $minutes = (int) $parts[1];
            $seconds = (int) $parts[2];

            if ($hours > 0) {
                return sprintf('%dj %dm %ds', $hours, $minutes, $seconds);
            }

            if ($minutes > 0) {
                return sprintf('%dm %ds', $minutes, $seconds);
            }

            return sprintf('%ds', $seconds);
        }

        // MM:SS format
        $minutes = (int) $parts[0];
        $seconds = (int) $parts[1];

        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        }

        return sprintf('%ds', $seconds);
    }

    public function render()
    {
        return view('livewire.quiz-score-history');
    }
}

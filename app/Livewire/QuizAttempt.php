<?php

/**
 * QuizAttempt Livewire Component
 *
 *
 * Route: /student/quiz/attempt/{id}
 */

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizAttempt as QuizAttemptModel;
use App\Models\QuizAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.quiz')]
class QuizAttempt extends Component
{
    public $quizId;

    public $currentQuestionIndex = 0;

    public $selectedAnswer = null;

    public $selectedAnswers = [];

    public $quiz = null;

    public $questions = [];

    public $attempt = null;

    public $answeredQuestionIds = [];

    public $timeRemaining = 0;

    public function mount($id): void
    {
        $this->quizId = $id;
        $this->loadQuiz();
        $this->initializeAttempt();
        $this->loadAnswers();
    }

    public function updatedSelectedAnswer(): void
    {
        $this->saveCurrentAnswer();
    }

    public function updatedSelectedAnswers(): void
    {
        $this->saveCurrentAnswer();
    }

    public function loadQuiz(): void
    {
        $this->quiz = Quiz::with('questions')->findOrFail($this->quizId);
        $this->questions = $this->quiz->questions->toArray();

        // Calculate time remaining in seconds
        if ($this->attempt && $this->attempt->started_at) {
            $elapsed = now()->diffInSeconds($this->attempt->started_at);
            $timeLimit = $this->quiz->time_limit_minutes * 60;
            $this->timeRemaining = max(0, $timeLimit - $elapsed);
        } else {
            $this->timeRemaining = $this->quiz->time_limit_minutes * 60;
        }
    }

    public function initializeAttempt(): void
    {
        $this->attempt = QuizAttemptModel::where('user_id', Auth::id())
            ->where('quiz_id', $this->quizId)
            ->whereNull('finished_at')
            ->first();

        if (!$this->attempt) {
            $this->attempt = QuizAttemptModel::create([
                'user_id' => Auth::id(),
                'quiz_id' => $this->quizId,
                'score' => 0,
                'started_at' => now(),
                'current_question_index' => 0,
            ]);
        } else {
            // Restore the saved question position
            $this->currentQuestionIndex = $this->attempt->current_question_index ?? 0;
        }
    }

    public function loadAnswers(): void
    {
        $this->answeredQuestionIds = QuizAnswer::where('quiz_attempt_id', $this->attempt->id)
            ->pluck('question_id')
            ->toArray();

        // Load current question's answer if exists
        if (isset($this->questions[$this->currentQuestionIndex])) {
            $currentQuestion = $this->questions[$this->currentQuestionIndex];
            $currentQuestionId = $currentQuestion['id'];
            $questionType = $currentQuestion['type'] ?? 'single_choice';

            $answer = QuizAnswer::where('quiz_attempt_id', $this->attempt->id)
                ->where('question_id', $currentQuestionId)
                ->first();

            if ($answer) {
                if ($questionType === 'multiple_choice') {
                    $this->selectedAnswers = $answer->user_answer ?? [];
                    $this->selectedAnswer = null;
                } else {
                    $this->selectedAnswer = $answer->user_answer ?? null;
                    $this->selectedAnswers = [];
                }
            } else {
                $this->selectedAnswer = null;
                $this->selectedAnswers = [];
            }
        }
    }

    public function goToQuestion($index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            // Save current answer before navigating (if navigating to a different question)
            if ($index !== $this->currentQuestionIndex) {
                $this->saveCurrentAnswer();
            }

            $this->currentQuestionIndex = $index;
            $this->selectedAnswer = null;
            $this->selectedAnswers = [];
            $this->loadAnswers();

            // Save the current question position to database
            $this->attempt->update([
                'current_question_index' => $this->currentQuestionIndex,
            ]);
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->goToQuestion($this->currentQuestionIndex - 1);
        }
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->goToQuestion($this->currentQuestionIndex + 1);
        }
    }

    public function saveCurrentAnswer(): void
    {
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        $questionType = $currentQuestion['type'] ?? 'single_choice';

        if ($questionType === 'multiple_choice') {
            QuizAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $this->attempt->id,
                    'question_id' => $currentQuestion['id'],
                ],
                [
                    'user_answer' => $this->selectedAnswers,
                    'is_correct' => false,
                    'score_obtained' => 0,
                ]
            );
        } else {
            if ($this->selectedAnswer === null) {
                return;
            }

            QuizAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $this->attempt->id,
                    'question_id' => $currentQuestion['id'],
                ],
                [
                    'user_answer' => $this->selectedAnswer,
                    'is_correct' => false,
                    'score_obtained' => 0,
                ]
            );
        }

        $this->loadAnswers();
    }

    public function submitQuiz()
    {
        $this->saveCurrentAnswer();

        $this->attempt->update([
            'finished_at' => now(),
        ]);

        return redirect()->route('quiz.result', $this->attempt->id);
    }

    public function getCurrentQuestion(): ?array
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function getQuestionNumber(): int
    {
        return $this->currentQuestionIndex + 1;
    }

    public function getTotalQuestions(): int
    {
        return count($this->questions);
    }

    public function getAnsweredCount(): int
    {
        return count($this->answeredQuestionIds);
    }

    public function getProgressPercentage(): int
    {
        $total = $this->getTotalQuestions();
        if ($total === 0) {
            return 0;
        }

        return (int) (($this->getAnsweredCount() / $total) * 100);
    }

    public function formatTime($seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function render()
    {
        return view('livewire.quiz-attempt-view');
    }
}

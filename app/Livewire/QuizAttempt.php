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

    public $showSubmitConfirmation = false;

    public $isSubmitting = false;

    protected $isLoadingAnswers = false;

    public function mount($id): void
    {
        $this->quizId = $id;
        $this->loadQuiz();
        $this->initializeAttempt();
        // Don't load answers here - wait for localStorage data
        $this->loadAnswers();
    }

    /**
     * Load quiz data from localStorage (called by JavaScript)
     */
    #[\Livewire\Attributes\On('loadFromLocalStorage')]
    public function loadFromLocalStorage($data): void
    {
        if (!$data || !isset($data['answers'])) {
            return;
        }

        // Restore current question index from localStorage
        if (isset($data['currentQuestionIndex'])) {
            $this->currentQuestionIndex = $data['currentQuestionIndex'];
            // Update database
            if ($this->attempt) {
                $this->attempt->update([
                    'current_question_index' => $this->currentQuestionIndex,
                ]);
            }
        }

        // Update answered question IDs from localStorage (not saving to database yet)
        $this->answeredQuestionIds = array_keys($data['answers'] ?? []);

        // Load current question's answer from localStorage
        if (isset($this->questions[$this->currentQuestionIndex])) {
            $currentQuestion = $this->questions[$this->currentQuestionIndex];
            $currentQuestionId = $currentQuestion['id'];
            $questionType = $currentQuestion['type'] ?? 'single_choice';

            if (isset($data['answers'][$currentQuestionId])) {
                $answerData = $data['answers'][$currentQuestionId];
                if ($questionType === 'multiple_choice') {
                    $this->selectedAnswers = $answerData['answer'] ?? [];
                    $this->selectedAnswer = null;
                } else {
                    $this->selectedAnswer = $answerData['answer'] ?? null;
                    $this->selectedAnswers = [];
                }
            }
        }

        // Load current question's answer from localStorage
        if (isset($this->questions[$this->currentQuestionIndex])) {
            $currentQuestion = $this->questions[$this->currentQuestionIndex];
            $currentQuestionId = $currentQuestion['id'];
            $questionType = $currentQuestion['type'] ?? 'single_choice';

            if (isset($data['answers'][$currentQuestionId])) {
                $answerData = $data['answers'][$currentQuestionId];
                if ($questionType === 'multiple_choice') {
                    $this->selectedAnswers = $answerData['answer'] ?? [];
                    $this->selectedAnswer = null;
                } else {
                    $this->selectedAnswer = $answerData['answer'] ?? null;
                    $this->selectedAnswers = [];
                }
            }
        }
    }

    /**
     * Restore a single answer from localStorage data
     */
    private function restoreAnswerFromLocalStorage($questionId, $answerData): void
    {
        if (!isset($answerData['answer'])) {
            return;
        }

        $userAnswer = $answerData['answer'];
        $questionType = $answerData['questionType'] ?? 'single_choice';

        if ($questionType === 'multiple_choice') {
            QuizAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $this->attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'user_answer' => $userAnswer,
                    'is_correct' => false,
                    'score_obtained' => 0,
                ]
            );
        } else {
            if ($userAnswer === null) {
                return;
            }

            QuizAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $this->attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'user_answer' => $userAnswer,
                    'is_correct' => false,
                    'score_obtained' => 0,
                ]
            );
        }
    }



    public function loadQuiz(): void
    {
        $this->quiz = Quiz::with(['questions', 'lesson'])->findOrFail($this->quizId);
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
        // Clear selections - answers will be loaded from localStorage by JavaScript
        $this->selectedAnswer = null;
        $this->selectedAnswers = [];
        $this->answeredQuestionIds = [];
        $this->isLoadingAnswers = false;
    }

    public function goToQuestion($index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            // Set flag to prevent saving during navigation
            $this->isLoadingAnswers = true;

            // Update index
            $this->currentQuestionIndex = $index;

            // Load answers for the new question (this will clear selections and load new ones)
            $this->loadAnswers();

            // Save the current question position to database
            $this->attempt->update([
                'current_question_index' => $this->currentQuestionIndex,
            ]);

            // Dispatch event to update localStorage
            $this->dispatch('question-navigated', index: $index);
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
        $totalQuestions = count($this->questions);
        $currentIndex = $this->currentQuestionIndex;

        if ($currentIndex < $totalQuestions - 1) {
            $this->goToQuestion($currentIndex + 1);
        }
    }

    #[\Livewire\Attributes\On('nextQuestion')]
    public function onNextQuestion(): void
    {
        $this->nextQuestion();
    }

    public function saveCurrentAnswer(): void
    {
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        $questionType = $currentQuestion['type'] ?? 'single_choice';
        $questionId = $currentQuestion['id'];

        if ($questionType === 'multiple_choice') {
            QuizAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $this->attempt->id,
                    'question_id' => $questionId,
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
                    'question_id' => $questionId,
                ],
                [
                    'user_answer' => $this->selectedAnswer,
                    'is_correct' => false,
                    'score_obtained' => 0,
                ]
            );
        }

        // Update answered question IDs for progress tracking
        if (!in_array($questionId, $this->answeredQuestionIds)) {
            $this->answeredQuestionIds[] = $questionId;
        }
    }

    public function confirmSubmit(): void
    {
        $this->showSubmitConfirmation = true;
    }

    public function cancelSubmit(): void
    {
        $this->showSubmitConfirmation = false;
    }

    public function submitQuiz()
    {
        $this->showSubmitConfirmation = false;
        $this->isSubmitting = true;

        // Dispatch event to request localStorage data from JavaScript
        // JavaScript will respond by calling submitFromLocalStorage
        $this->dispatch('requestLocalStorageData');

        return;
    }

    /**
     * Finalize quiz submission after receiving localStorage data
     */
    #[\Livewire\Attributes\On('finalizeSubmission')]
    public function finalizeSubmission()
    {
        $this->attempt->update([
            'finished_at' => now(),
        ]);

        // Dispatch event to clear localStorage
        $this->dispatch('quiz-submitted');

        // Get the lesson and course information for redirect
        $lesson = $this->quiz->lesson;
        if ($lesson && $lesson->chapter && $lesson->chapter->course) {
            $courseSlug = $lesson->chapter->course->slug;
            $lessonSlug = $lesson->slug;

            return redirect()->route('student.lesson', [
                'courseSlug' => $courseSlug,
                'lessonSlug' => $lessonSlug,
            ]);
        }

        // Fallback redirect if lesson info is not available
        return redirect()->route('home');
    }

    /**
     * Submit quiz from localStorage (called by Alpine.js)
     */
    #[\Livewire\Attributes\On('submitFromLocalStorage')]
    public function submitFromLocalStorage($data): void
    {
        if (!$data || !isset($data['answers'])) {
            return;
        }

        // Save all answers from localStorage to database
        foreach ($data['answers'] as $questionId => $answerData) {
            $this->restoreAnswerFromLocalStorage($questionId, $answerData);
        }

        // Update current question index from localStorage
        if (isset($data['currentQuestionIndex'])) {
            $this->currentQuestionIndex = $data['currentQuestionIndex'];
            $this->attempt->update([
                'current_question_index' => $this->currentQuestionIndex,
            ]);
        }

        // Refresh answered question IDs
        $this->answeredQuestionIds = QuizAnswer::where('quiz_attempt_id', $this->attempt->id)
            ->pluck('question_id')
            ->toArray();
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

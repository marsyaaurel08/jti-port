<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizAttempt as QuizAttemptModel;
use App\Models\QuizAnswer;
use App\Services\QuizScoringService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.quiz')]
class QuizAttempt extends Component
{
    public $quizId;

    public $quiz;

    public $questions = [];

    public $attempt;

    public $currentQuestionIndex = 0;

    public $answers = [];

    public $multipleChoiceAnswers = [];

    public $matchingAnswers = [];

    public $dragDropAnswers = [];

    public $answeredQuestionIds = [];

    public $timeRemaining = 0;

    public $showSubmitConfirmation = false;

    public $isSubmitting = false;

    public function mount($id)
    {
        $this->quizId = $id;

        // Initialize attempt FIRST so that loadQuiz() can access started_at
        $this->initializeAttempt();

        // Then load quiz and calculate time remaining based on started_at
        $this->loadQuiz();
    }

    public function loadQuiz()
    {
        $this->quiz = Quiz::with(['questions', 'lesson.chapter.course'])
            ->findOrFail($this->quizId);

        $this->questions = $this->quiz->questions->toArray();


        if ($this->attempt && $this->attempt->started_at) {
            // Ensure both times are in the same timezone for accurate calculation
            $now = now();
            $startedAt = $this->attempt->started_at;

            // Calculate elapsed time (now - started_at) for positive value
            $elapsed = $startedAt->diffInSeconds($now);
            $limit = $this->quiz->time_limit_minutes * 60;
            $this->timeRemaining = max(0, $limit - $elapsed);
        } else {
            $this->timeRemaining = $this->quiz->time_limit_minutes * 60;
        }
    }

    public function initializeAttempt()
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

            $this->currentQuestionIndex = $this->attempt->current_question_index ?? 0;
        }
    }


    public function goToQuestion($index)
    {
        if ($index < 0 || $index >= count($this->questions)) {
            return;
        }

        $this->currentQuestionIndex = $index;

        // Load the answer for the current question
        $this->loadCurrentQuestionAnswer();

        $this->attempt->update([
            'current_question_index' => $this->currentQuestionIndex
        ]);

        $this->dispatch('question-navigated', index: $index);
    }

    /**
     * Load the answer for the current question from localStorage
     * This ensures that when navigating between questions, the correct answer is displayed
     */
    #[\Livewire\Attributes\On('loadCurrentQuestionAnswer')]
    public function loadCurrentQuestionAnswer()
    {
        $currentQuestion = $this->getCurrentQuestion();

        if (!$currentQuestion) {
            return;
        }

        $questionId = $currentQuestion['id'];
        $questionType = $currentQuestion['type'];

        // Get the storage key for this quiz
        $storageKey = 'quiz_attempt_' . $this->quizId;

        // Try to get the saved answer from localStorage via JavaScript
        // We'll dispatch an event to request the answer from localStorage
        $this->dispatch('request-answer-for-question', questionId: $questionId, questionType: $questionType);

        // Also dispatch an event to load all answered question IDs from localStorage
        $this->dispatch('request-answered-question-ids');
    }

    /**
     * Receive the answered question IDs from localStorage and update the Livewire property
     */
    #[\Livewire\Attributes\On('answeredQuestionIdsLoaded')]
    public function answeredQuestionIdsLoaded($answeredIds)
    {
        $this->answeredQuestionIds = $answeredIds;
    }

    /**
     * Receive the answer from localStorage and update the Livewire properties
     */
    #[\Livewire\Attributes\On('answer-loaded-from-storage')]
    public function answerLoadedFromStorage($questionId, $answer, $questionType)
    {
        // Only update if this is the current question
        $currentQuestion = $this->getCurrentQuestion();

        if (!$currentQuestion || $currentQuestion['id'] != $questionId) {
            return;
        }

        // Update the appropriate property based on question type
        if ($questionType === 'single_choice') {
            $this->answers[$questionId] = $answer;
        } elseif ($questionType === 'multiple_choice') {
            $this->multipleChoiceAnswers[$questionId] = is_array($answer) ? $answer : [];
        } elseif ($questionType === 'matching') {
            $this->matchingAnswers[$questionId] = is_array($answer) ? $answer : [];
        } elseif ($questionType === 'drag_drop_sorting') {
            $this->dragDropAnswers[$questionId] = is_array($answer) ? $answer : [];
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {

            $this->goToQuestion($this->currentQuestionIndex - 1);
        }
    }

    public function nextQuestion()
    {
        $total = count($this->questions);

        if ($this->currentQuestionIndex < $total - 1) {

            $this->goToQuestion($this->currentQuestionIndex + 1);
        }
    }

    #[\Livewire\Attributes\On('loadFromLocalStorage')]
    public function loadFromLocalStorage($data)
    {
        if (!$data) return;

        if (isset($data['currentQuestionIndex'])) {

            $this->currentQuestionIndex = $data['currentQuestionIndex'];
        }

        if (isset($data['answers'])) {

            $this->answeredQuestionIds = array_keys($data['answers']);
        }

        // Load the answer for the current question
        $this->loadCurrentQuestionAnswer();

        $this->dispatch('restore-question');
    }

    #[\Livewire\Attributes\On('answerSavedToLocalStorage')]
    public function answerSavedToLocalStorage($questionId)
    {
        // Add question ID to answered list if not already present
        if (!in_array($questionId, $this->answeredQuestionIds)) {
            $this->answeredQuestionIds[] = $questionId;
        }
    }

    #[\Livewire\Attributes\On('submitFromLocalStorage')]
    public function submitFromLocalStorage($data)
    {
        if (!$data || !isset($data['answers'])) {
            return;
        }

        foreach ($data['answers'] as $questionId => $answerData) {

            QuizAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $this->attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'user_answer' => $answerData['answer'],
                    'is_correct' => false,
                    'score_obtained' => 0,
                ]
            );
        }
    }

    public function confirmSubmit()
    {
        Log::info('QuizAttempt::confirmSubmit() called', [
            'quiz_id' => $this->quizId,
            'attempt_id' => $this->attempt->id,
            'current_showSubmitConfirmation' => $this->showSubmitConfirmation,
        ]);

        $this->showSubmitConfirmation = true;

        Log::info('QuizAttempt::confirmSubmit() - showSubmitConfirmation set to true', [
            'quiz_id' => $this->quizId,
            'new_showSubmitConfirmation' => $this->showSubmitConfirmation,
        ]);

        // DIAGNOSTIC: Dispatch event to check if view receives the update
        $this->dispatch('diagnostic-showSubmitConfirmation-updated', value: $this->showSubmitConfirmation);
    }

    public function cancelSubmit()
    {
        $this->showSubmitConfirmation = false;
    }

    /**
     * Handle timer warning from JavaScript
     * This is called when the timer reaches specific warning thresholds
     */
    #[\Livewire\Attributes\On('showTimerWarning')]
    public function showTimerWarning($message)
    {
        // Dispatch event to frontend to show notification
        $this->dispatch('timer-warning', message: $message);
    }

    public function submitQuiz()
    {
        Log::info('QuizAttempt::submitQuiz() called', [
            'quiz_id' => $this->quizId,
            'attempt_id' => $this->attempt->id,
            'answers' => $this->answers,
            'multipleChoiceAnswers' => $this->multipleChoiceAnswers,
            'matchingAnswers' => $this->matchingAnswers,
            'dragDropAnswers' => $this->dragDropAnswers,
            'showSubmitConfirmation' => $this->showSubmitConfirmation,
        ]);

        // DIAGNOSTIC: Check if confirmation was shown
        if (!$this->showSubmitConfirmation) {
            Log::warning('QuizAttempt::submitQuiz() - WARNING: Submit called without confirmation modal being shown!');
        }

        Log::info('QuizAttempt::submitQuiz() - About to set isSubmitting = true');
        $this->isSubmitting = true;
        Log::info('QuizAttempt::submitQuiz() - isSubmitting set to: ' . ($this->isSubmitting ? 'true' : 'false'));

        // Process single choice answers from Livewire property
        foreach ($this->answers as $questionId => $answer) {
            if ($answer !== null) {
                QuizAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $this->attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'user_answer' => $answer,
                        'is_correct' => false,
                        'score_obtained' => 0,
                    ]
                );
            }
        }

        // Process multiple choice answers from Livewire property
        foreach ($this->multipleChoiceAnswers as $questionId => $answer) {
            if (!empty($answer)) {
                QuizAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $this->attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'user_answer' => json_encode($answer),
                        'is_correct' => false,
                        'score_obtained' => 0,
                    ]
                );
            }
        }

        // Process matching answers from Livewire property
        foreach ($this->matchingAnswers as $questionId => $answer) {
            if (!empty($answer)) {
                QuizAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $this->attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'user_answer' => json_encode($answer),
                        'is_correct' => false,
                        'score_obtained' => 0,
                    ]
                );
            }
        }

        // Process drag drop sorting answers from Livewire property
        foreach ($this->dragDropAnswers as $questionId => $answer) {
            if (!empty($answer)) {
                QuizAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $this->attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'user_answer' => json_encode($answer),
                        'is_correct' => false,
                        'score_obtained' => 0,
                    ]
                );
            }
        }

        // Also try to get answers from localStorage as backup
        Log::info('QuizAttempt::submitQuiz() - About to dispatch requestLocalStorageData event');
        $this->dispatch('requestLocalStorageData');
        Log::info('QuizAttempt::submitQuiz() - requestLocalStorageData event dispatched');

        // Finalize submission
        Log::info('QuizAttempt::submitQuiz() - About to call finalizeSubmission() directly');
        $this->finalizeSubmission();
        Log::info('QuizAttempt::submitQuiz() - finalizeSubmission() returned');
    }

    #[\Livewire\Attributes\On('finalizeSubmission')]
    public function finalizeSubmission()
    {
        Log::info('QuizAttempt::finalizeSubmission() called', [
            'quiz_id' => $this->quizId,
            'attempt_id' => $this->attempt->id,
            'isSubmitting' => $this->isSubmitting,
        ]);

        // Grade the quiz attempt
        Log::info('QuizAttempt::finalizeSubmission() - About to grade the attempt');
        $scoringService = new QuizScoringService();
        $scoringService->gradeAttempt($this->attempt);
        Log::info('QuizAttempt::finalizeSubmission() - Attempt graded');

        Log::info('QuizAttempt::finalizeSubmission() - About to update attempt with finished_at');
        $this->attempt->update([
            'finished_at' => now()
        ]);
        Log::info('QuizAttempt::finalizeSubmission() - Attempt updated with finished_at');

        Log::info('QuizAttempt::finalizeSubmission() - attempt finished, dispatching quiz-submitted', [
            'quiz_id' => $this->quizId,
            'attempt_id' => $this->attempt->id,
        ]);

        Log::info('QuizAttempt::finalizeSubmission() - About to dispatch quiz-submitted event');
        $this->dispatch('quiz-submitted');
        Log::info('QuizAttempt::finalizeSubmission() - quiz-submitted event dispatched');

        Log::info('QuizAttempt::finalizeSubmission() - About to get lesson from quiz');
        $lesson = $this->quiz->lesson;
        Log::info('QuizAttempt::finalizeSubmission() - Lesson retrieved: ' . ($lesson ? 'exists' : 'null'));

        if ($lesson && $lesson->chapter && $lesson->chapter->course) {

            Log::info('QuizAttempt::finalizeSubmission() - redirecting to lesson', [
                'course_slug' => $lesson->chapter->course->slug,
                'lesson_slug' => $lesson->slug,
            ]);

            return redirect()->route('student.lesson', [
                'courseSlug' => $lesson->chapter->course->slug,
                'lessonSlug' => $lesson->slug,
            ]);
        }

        Log::info('QuizAttempt::finalizeSubmission() - redirecting to home');

        return redirect()->route('home');
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function getQuestionNumber()
    {
        return $this->currentQuestionIndex + 1;
    }

    public function getTotalQuestions()
    {
        return count($this->questions);
    }

    public function getAnsweredCount()
    {
        return count($this->answeredQuestionIds);
    }

    public function getProgressPercentage()
    {
        $total = $this->getTotalQuestions();

        if ($total === 0) return 0;

        return intval(($this->getAnsweredCount() / $total) * 100);
    }

    public function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);

        $minutes = floor(($seconds % 3600) / 60);

        $secs = (int) ($seconds % 60);

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function getLessonUrl()
    {
        $lesson = $this->quiz->lesson;

        if ($lesson && $lesson->chapter && $lesson->chapter->course) {
            return route('student.lesson', [
                'courseSlug' => $lesson->chapter->course->slug,
                'lessonSlug' => $lesson->slug,
            ]);
        }

        return route('home');
    }

    public function render()
    {
        return view('livewire.quiz-attempt-view');
    }
}

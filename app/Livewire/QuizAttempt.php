<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt as QuizAttemptModel;
use App\Services\QuizScoringService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.quiz')]
class QuizAttempt extends Component
{
    // Performance optimization: Lock immutable properties to prevent Livewire tracking
    #[\Livewire\Attributes\Locked]
    public $quizId;

    #[\Livewire\Attributes\Locked]
    public $quiz;

    #[\Livewire\Attributes\Locked]
    public $questions = [];

    public $attempt;

    public $currentQuestionIndex = 0;

    public $answers = [];

    public $multipleChoiceAnswers = [];

    public $matchingAnswers = [];

    public $dragDropAnswers = [];

    public $dropDownAnswers = [];

    public $answeredQuestionIds = [];

    #[\Livewire\Attributes\Locked]
    public $timeRemaining = 0;

    public $showSubmitConfirmation = false;

    public $isSubmitting = false;

    // Cache the resolved lesson URL so getLessonUrl() doesn't re-traverse relations
    protected ?string $lessonUrlCache = null;

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
        // Performance optimization: Select only needed columns and eager-load efficiently
        $this->quiz = Quiz::with([
            'questions' => function ($query) {
                $query->select('id', 'quiz_id', 'type', 'question_text', 'question_config');
            },
            'lesson.chapter.course'
        ])->select('id', 'lesson_id', 'time_limit_minutes')
            ->findOrFail($this->quizId);

        // Use a plain array so Livewire doesn't hydrate Eloquent models on every request
        $this->questions = $this->quiz->questions->toArray();

        if ($this->attempt && $this->attempt->started_at) {
            $elapsed = $this->attempt->started_at->diffInSeconds(now());
            $limit   = $this->quiz->time_limit_minutes * 60;
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

        if (! $this->attempt) {
            $this->attempt = QuizAttemptModel::create([
                'user_id'                => Auth::id(),
                'quiz_id'                => $this->quizId,
                'score'                  => 0,
                'started_at'             => now(),
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

        $this->loadCurrentQuestionAnswer();

        // Single update — only the index column changes
        $this->attempt->update([
            'current_question_index' => $this->currentQuestionIndex,
        ]);

        $this->dispatch('question-navigated', index: $index);
    }

    /**
     * Load the answer for the current question from localStorage.
     */
    #[\Livewire\Attributes\On('loadCurrentQuestionAnswer')]
    public function loadCurrentQuestionAnswer()
    {
        $currentQuestion = $this->getCurrentQuestion();

        if (! $currentQuestion) {
            return;
        }

        $this->dispatch(
            'request-answer-for-question',
            questionId: $currentQuestion['id'],
            questionType: $currentQuestion['type'],
        );

        $this->dispatch('request-answered-question-ids');
    }

    /**
     * Receive the answered question IDs from localStorage.
     */
    #[\Livewire\Attributes\On('answeredQuestionIdsLoaded')]
    public function answeredQuestionIdsLoaded($answeredIds)
    {
        $this->answeredQuestionIds = $answeredIds;
    }

    /**
     * Receive a single answer from localStorage.
     */
    #[\Livewire\Attributes\On('answer-loaded-from-storage')]
    public function answerLoadedFromStorage($questionId, $answer, $questionType)
    {
        $currentQuestion = $this->getCurrentQuestion();

        if (! $currentQuestion || $currentQuestion['id'] != $questionId) {
            return;
        }

        match ($questionType) {
            'single_choice', 'boolean'  => $this->answers[$questionId]              = $answer,
            'multiple_choice'           => $this->multipleChoiceAnswers[$questionId] = is_array($answer) ? $answer : [],
            'matching'                  => $this->matchingAnswers[$questionId]        = is_array($answer) ? $answer : [],
            'drag_drop_sorting'         => $this->dragDropAnswers[$questionId]        = is_array($answer) ? $answer : [],
            'drop_down'                 => $this->dropDownAnswers[$questionId]        = is_array($answer) ? $answer : [],
            default                     => null,
        };
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->goToQuestion($this->currentQuestionIndex - 1);
        }
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->goToQuestion($this->currentQuestionIndex + 1);
        }
    }

    #[\Livewire\Attributes\On('loadFromLocalStorage')]
    public function loadFromLocalStorage($data)
    {
        if (! $data) {
            return;
        }

        if (isset($data['currentQuestionIndex'])) {
            $this->currentQuestionIndex = $data['currentQuestionIndex'];
        }

        if (isset($data['answers'])) {
            $this->answeredQuestionIds = array_keys($data['answers']);

            foreach ($data['answers'] as $questionId => $answerData) {
                $this->dispatch('restore-question', [
                    'questionId'   => $questionId,
                    'questionType' => $answerData['questionType'],
                    'answer'       => $answerData['answer'],
                ]);
            }
        }

        $this->loadCurrentQuestionAnswer();
    }

    #[\Livewire\Attributes\On('answerSavedToLocalStorage')]
    public function answerSavedToLocalStorage($questionId)
    {
        if (! in_array($questionId, $this->answeredQuestionIds)) {
            $this->answeredQuestionIds[] = $questionId;
        }
    }

    #[\Livewire\Attributes\On('answerRemovedFromLocalStorage')]
    public function answerRemovedFromLocalStorage($questionId)
    {
        $this->answeredQuestionIds = array_values(array_filter(
            $this->answeredQuestionIds,
            fn($id) => $id !== $questionId
        ));
    }

    #[\Livewire\Attributes\On('submitFromLocalStorage')]
    public function submitFromLocalStorage($data)
    {
        if (! $data || ! isset($data['answers'])) {
            return;
        }

        // Batch upsert instead of one query per answer
        $rows = [];
        $now  = now();

        foreach ($data['answers'] as $questionId => $answerData) {
            $rows[] = [
                'quiz_attempt_id' => $this->attempt->id,
                'question_id'     => $questionId,
                'user_answer'     => json_encode($answerData['answer']),
                'is_correct'      => false,
                'score_obtained'  => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        if (! empty($rows)) {
            // Single upsert call; conflicts on (quiz_attempt_id, question_id) update the answer
            DB::table('quiz_answers')->upsert(
                $rows,
                ['quiz_attempt_id', 'question_id'],
                ['user_answer', 'is_correct', 'score_obtained', 'updated_at']
            );
        }
    }

    public function confirmSubmit()
    {
        $this->showSubmitConfirmation = true;

        $this->dispatch('diagnostic-showSubmitConfirmation-updated', value: $this->showSubmitConfirmation);
    }

    public function cancelSubmit()
    {
        $this->showSubmitConfirmation = false;
    }

    /**
     * Handle timer warning from JavaScript.
     */
    #[\Livewire\Attributes\On('showTimerWarning')]
    public function showTimerWarning($message)
    {
        $this->dispatch('timer-warning', message: $message);
    }

    public function submitQuiz()
    {
        $this->isSubmitting = true;

        // Performance optimization: Combine all operations in a single transaction
        DB::transaction(function () {
            $rows = [];
            $now  = now();
            $attemptId = $this->attempt->id;

            // Single-value answers (single_choice, boolean) — encoded as JSON
            foreach ($this->answers as $questionId => $answer) {
                if ($answer === null) {
                    continue;
                }
                $rows[$questionId] = [
                    'quiz_attempt_id' => $attemptId,
                    'question_id'     => $questionId,
                    'user_answer'     => json_encode($answer),
                    'is_correct'      => false,
                    'score_obtained'  => 0,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }

            // Array answers — stored as JSON
            $jsonSources = [
                $this->multipleChoiceAnswers,
                $this->matchingAnswers,
                $this->dragDropAnswers,
                $this->dropDownAnswers,
            ];

            foreach ($jsonSources as $source) {
                foreach ($source as $questionId => $answer) {
                    if (empty($answer)) {
                        continue;
                    }
                    // Later sources overwrite earlier ones for the same question ID,
                    // which is safe because each question type only appears in one map.
                    $rows[$questionId] = [
                        'quiz_attempt_id' => $attemptId,
                        'question_id'     => $questionId,
                        'user_answer'     => json_encode($answer),
                        'is_correct'      => false,
                        'score_obtained'  => 0,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
            }

            // One round-trip to the database for all answers
            if (! empty($rows)) {
                DB::table('quiz_answers')->upsert(
                    array_values($rows),
                    ['quiz_attempt_id', 'question_id'],
                    ['user_answer', 'is_correct', 'score_obtained', 'updated_at']
                );
            }

            // Request any remaining answers from localStorage as backup
            // This will trigger submitFromLocalStorage which is also optimized
            $this->dispatch('requestLocalStorageData');
        });
    }

    #[\Livewire\Attributes\On('finalizeSubmission')]
    public function finalizeSubmission()
    {
        // Performance optimization: Combine grading and finalization in transaction
        DB::transaction(function () {
            $scoringService = new QuizScoringService;
            $scoringService->gradeAttempt($this->attempt);

            $this->attempt->update(['finished_at' => now()]);
        });

        $this->dispatch('quiz-submitted');

        return redirect($this->getLessonUrl());
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

        return $total === 0 ? 0 : intval(($this->getAnsweredCount() / $total) * 100);
    }

    public function formatTime($seconds)
    {
        $hours   = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs    = (int) ($seconds % 60);

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function getLessonUrl()
    {
        // Return cached URL to avoid re-traversing relations on every call
        if ($this->lessonUrlCache !== null) {
            return $this->lessonUrlCache;
        }

        $lesson = $this->quiz->lesson;

        if ($lesson && $lesson->chapter && $lesson->chapter->course) {
            $this->lessonUrlCache = route('student.lesson', [
                'courseSlug' => $lesson->chapter->course->slug,
                'lessonSlug' => $lesson->slug,
            ]);
        } else {
            $this->lessonUrlCache = route('home');
        }

        return $this->lessonUrlCache;
    }

    public function render()
    {
        return view('livewire.quiz-attempt-view');
    }
}

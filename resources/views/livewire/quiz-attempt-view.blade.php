{{--
    QuizAttempt Livewire Component View
    File ini menangani tampilan UI untuk menjawab kuis.
    Component: app/Livewire/QuizAttempt.php
--}}
<div class="h-screen flex flex-col bg-gray-50 font-sans text-gray-800 overflow-hidden" data-quiz-id="{{ $quizId }}">
    <!-- Fixed Header -->
    <header class="shrink-0 bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center z-20 sticky top-0">
        <div class="flex items-center gap-3">
            <a href="#" class="flex items-center text-gray-700 hover:text-gray-900 transition-colors text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="hidden sm:inline">Back To Course</span>
            </a>

            <!-- Mobile Sidebar Toggle -->
            <button onclick="document.getElementById('sidebar').classList.toggle('hidden')" class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div class="bg-yellow-50 border border-jti-yellow rounded-lg px-2 py-1.5 flex items-center shadow-sm">
            <svg class="w-3.5 h-3.5 text-jti-yellow mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
            <span class="text-base sm:text-lg md:text-xl font-mono font-normal tracking-widest text-gray-700">{{ $this->formatTime($timeRemaining) }}</span>
        </div>
    </header>

    <!-- Main Content Area with Sidebar -->
    <div class="flex flex-1 overflow-hidden relative">
        <!-- Sidebar (hidden on mobile by default) -->
        <aside id="sidebar" class="absolute md:relative z-10 shrink-0 w-64 bg-white border-r border-gray-200 p-4 md:p-6 hidden md:block h-full overflow-y-auto">
            <h2 class="font-bold text-gray-900 mb-2 text-sm">Test Progress</h2>
            <p class="text-xs text-gray-500 mb-4">{{ $this->getAnsweredCount() }} of {{ $this->getTotalQuestions() }} Answered</p>

            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-6">
                <div class="bg-jti-yellow h-1.5 rounded-full" style="width: {{ $this->getProgressPercentage() }}%"></div>
            </div>

            <div class="grid grid-cols-5 gap-2 mb-6">
                @foreach($questions as $index => $question)
                    @php
                        $isCurrent = $index == $currentQuestionIndex;
                        $isAnswered = in_array($question['id'], $answeredQuestionIds);
                    @endphp
                    <button wire:click="goToQuestion({{ $index }})"
                            class="w-7 h-7 rounded text-xs font-semibold transition-all
                        {{ $isCurrent ? 'bg-jti-yellow text-white shadow-md scale-110' : '' }}
                        {{ !$isCurrent && $isAnswered ? 'bg-green-100 text-green-600 border border-green-200' : '' }}
                        {{ !$isCurrent && !$isAnswered ? 'bg-gray-100 text-gray-400 border border-gray-200' : '' }}">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="space-y-3 pt-4 border-t border-gray-100">
                <div class="flex items-center text-xs text-gray-500">
                    <span class="w-3 h-3 rounded-full bg-green-100 border border-green-300 mr-2"></span> Answered
                </div>
                <div class="flex items-center text-xs text-gray-500">
                    <span class="w-3 h-3 rounded-full bg-jti-yellow mr-2"></span> Current
                </div>
                <div class="flex items-center text-xs text-gray-500">
                    <span class="w-3 h-3 rounded-full bg-gray-200 mr-2"></span> Unanswered
                </div>
            </div>
        </aside>

        <!-- Scrollable Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 lg:p-12">
            <nav class="flex text-sm text-gray-400 mb-4 md:mb-6 font-medium">
                <span>Practice Test</span>
                <span class="mx-2">></span>
                <span class="text-jti-yellow">Question {{ $this->getQuestionNumber() }}</span>
            </nav>

            @php $currentQuestion = $this->getCurrentQuestion(); @endphp

            @if($currentQuestion)
                <h1 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-900 leading-tight mb-4 md:mb-6">
                    {{ $currentQuestion['question_text'] }}
                </h1>

                <div class="border-t-2 border-gray-200 my-4 md:my-8"></div>

                <!-- Question Options based on type -->
                @php
                    $config = $currentQuestion['question_config'] ?? [];
                    $options = $config['options'] ?? [];
                @endphp

                <div data-question-id="{{ $currentQuestion['id'] }}" data-question-type="{{ $currentQuestion['type'] }}">
                    @if($currentQuestion['type'] === 'single_choice')
                        <x-quiz.single-choice :options="$options" :selected="$answers[$currentQuestion['id']] ?? null" wireModel="answers.{{ $currentQuestion['id'] }}" :questionId="$currentQuestion['id']" :questionType="'single_choice'" />
                    @elseif($currentQuestion['type'] === 'multiple_choice')
                        <x-quiz.multiple-choice :options="$options" :selected="$multipleChoiceAnswers[$currentQuestion['id']] ?? []" wireModel="multipleChoiceAnswers.{{ $currentQuestion['id'] }}" :questionType="'multiple_choice'" :questionId="$currentQuestion['id']" />
                    @elseif($currentQuestion['type'] === 'drag_drop_sorting')
                        <x-quiz.drag-drop-sorting
                            :items="$config['items'] ?? []"
                            :selected="$dragDropAnswers[$currentQuestion['id']] ?? []"
                            wireModel="dragDropAnswers"
                            :questionId="$currentQuestion['id']"
                        />
                    @elseif($currentQuestion['type'] === 'matching')
                        <x-quiz.matching
                            :items="$config['items'] ?? []"
                            :matches="$config['matches'] ?? []"
                            :selected="$matchingAnswers[$currentQuestion['id']] ?? []"
                            wireModel="matchingAnswers"
                            :questionId="$currentQuestion['id']"
                />
                    @endif
                </div>
            @endif
        </main>
    </div>

    <!-- Fixed Footer -->
    <footer class="shrink-0 bg-white border-t border-gray-200 px-4 md:px-8 py-3 md:py-4 flex justify-between items-center z-20 sticky bottom-0">
        <button wire:click="previousQuestion"
                @disabled($currentQuestionIndex === 0) class="flex items-center px-4 md:px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors font-semibold text-sm {{ $currentQuestionIndex === 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span class="hidden sm:inline">Previous Question</span>
            <span class="sm:hidden">Previous</span>
        </button>

        @if($currentQuestionIndex < count($questions) - 1)
            <button wire:click="nextQuestion"
                    class="flex items-center px-4 md:px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors font-semibold text-sm">
                <span class="hidden sm:inline">Next Question</span>
                <span class="sm:hidden">Next</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        @else
            <button wire:click="confirmSubmit"
                    wire:key="submit-button"
                    wire:loading.attr="disabled"
                    class="flex items-center px-4 md:px-6 py-2 rounded-lg bg-jti-yellow text-white hover:bg-yellow-500 transition-colors font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="hidden sm:inline">Submit Quiz</span>
                <span class="sm:hidden">Submit</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </button>
        @endif
    </footer>

    <!-- Submit Confirmation Modal -->
    @if($showSubmitConfirmation)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 backdrop-blur-sm" wire:click="cancelSubmit"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-10">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <!-- Modal Title -->
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">
                    Submit Quiz?
                </h3>

                <!-- Modal Message -->
                <p class="text-gray-600 text-center mb-6">
                    Are you sure you want to submit your quiz? You have answered <strong>{{ $this->getAnsweredCount() }}</strong> out of <strong>{{ $this->getTotalQuestions() }}</strong> questions. This action cannot be undone.
                </p>

                <!-- Modal Actions -->
                <div class="flex gap-3">
                    <button wire:click="cancelSubmit"
                            class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors font-semibold text-sm">
                        Cancel
                    </button>
                    <button wire:click="submitQuiz"
                            wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2 rounded-lg bg-jti-yellow text-white hover:bg-yellow-500 transition-colors font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            onclick="if(window.QuizLocalStorage) window.QuizLocalStorage.setSubmitting(true)">
                        <span wire:loading wire:target="submitQuiz">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                        <span wire:loading.remove wire:target="submitQuiz">Confirm Submit</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

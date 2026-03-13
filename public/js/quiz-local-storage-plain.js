/**
 * Quiz Local Storage Manager - Plain JavaScript
 * Handles client-side data storage for quiz attempts to prevent data loss
 * This implementation doesn't use Alpine.js to avoid conflicts with Livewire
 */

(function() {
    'use strict';

    const STORAGE_KEY_PREFIX = 'quiz_attempt_';
    const STORAGE_VERSION = '1.0';

    /**
     * Get the storage key for a specific quiz attempt
     */
    function getStorageKey(quizId) {
        return `${STORAGE_KEY_PREFIX}${quizId}`;
    }

    /**
     * Get the current quiz data from localStorage
     */
    function getQuizData(quizId) {
        try {
            const key = getStorageKey(quizId);
            const data = localStorage.getItem(key);
            if (!data) return null;

            const parsed = JSON.parse(data);

            // Validate version
            if (parsed.version !== STORAGE_VERSION) {
                console.warn('Quiz data version mismatch, clearing old data');
                clearQuizData(quizId);
                return null;
            }

            return parsed;
        } catch (error) {
            console.error('Error reading quiz data from localStorage:', error);
            return null;
        }
    }

    /**
     * Save quiz data to localStorage
     */
    function saveQuizData(quizId, data) {
        try {
            const key = getStorageKey(quizId);
            const dataToSave = {
                version: STORAGE_VERSION,
                quizId: quizId,
                timestamp: Date.now(),
                ...data
            };
            localStorage.setItem(key, JSON.stringify(dataToSave));
            console.log('Quiz data saved to storage');
            return true;
        } catch (error) {
            console.error('Error saving quiz data to localStorage:', error);
            return false;
        }
    }

    /**
     * Clear quiz data from localStorage
     */
    function clearQuizData(quizId) {
        try {
            const key = getStorageKey(quizId);
            localStorage.removeItem(key);
            console.log('Quiz data cleared from storage');
            return true;
        } catch (error) {
            console.error('Error clearing quiz data from localStorage:', error);
            return false;
        }
    }

    /**
     * Update the current question index
     */
    function updateCurrentQuestionIndex(quizId, index) {
        const data = getQuizData(quizId) || {};
        data.currentQuestionIndex = index;
        return saveQuizData(quizId, data);
    }

    /**
     * Save an answer for a question
     */
    function saveAnswer(quizId, questionId, answer, questionType) {
        const data = getQuizData(quizId) || {};

        if (!data.answers) {
            data.answers = {};
        }

        data.answers[questionId] = {
            answer: answer,
            questionType: questionType,
            timestamp: Date.now()
        };

        return saveQuizData(quizId, data);
    }

    /**
     * Get all answers from localStorage
     */
    function getAllAnswers(quizId) {
        const data = getQuizData(quizId);
        return data ? data.answers : {};
    }

    /**
     * Get the current question index from localStorage
     */
    function getCurrentQuestionIndex(quizId) {
        const data = getQuizData(quizId);
        return data ? data.currentQuestionIndex : 0;
    }

    /**
     * Get answered question IDs
     */
    function getAnsweredQuestionIds(quizId) {
        const data = getQuizData(quizId);
        return data ? Object.keys(data.answers || {}) : [];
    }

    /**
     * Check if there's saved data for this quiz
     */
    function hasSavedData(quizId) {
        return getQuizData(quizId) !== null;
    }

    /**
     * Export all quiz data as JSON for submission
     */
    function exportQuizData(quizId) {
        return getQuizData(quizId);
    }

    /**
     * Dispatch quiz answer selected event
     */
    window.dispatchQuizAnswerSelected = function(questionId, answer, questionType) {
        const event = new CustomEvent('quiz-answer-selected', {
            detail: { questionId, answer, questionType }
        });
        document.dispatchEvent(event);
    };

    /**
     * Initialize quiz localStorage functionality
     */
    function initQuizLocalStorage() {
        const quizElement = document.querySelector('[data-quiz-id]');
        if (!quizElement) {
            console.log('No quiz element found');
            return;
        }

        const quizId = quizElement.dataset.quizId;
        console.log('QuizLocalStorage initialized for quiz:', quizId);

        // Load data from localStorage
        const savedData = getQuizData(quizId);

        // If there's saved data, sync with Livewire
        if (savedData && savedData.answers && Object.keys(savedData.answers).length > 0) {
            console.log('Found saved data, syncing with Livewire');

            // Find the Livewire component
            const livewireComponent = quizElement.closest('[wire\\:id]');
            if (livewireComponent && window.Livewire) {
                const component = window.Livewire.find(livewireComponent.getAttribute('wire:id'));
                if (component) {
                    component.call('loadFromLocalStorage', savedData);
                }
            }
        }

        // Add event listeners to all quiz inputs
        const quizInputs = document.querySelectorAll('.quiz-input');
        quizInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                const questionElement = this.closest('[data-question-id]');
                const questionId = questionElement?.dataset.questionId;
                const questionType = questionElement?.dataset.questionType;
                const value = parseInt(this.value);

                if (questionId) {
                    // Dispatch event to save to localStorage
                    const event = new CustomEvent('quiz-answer-selected', {
                        detail: { questionId, answer: value, questionType }
                    });
                    document.dispatchEvent(event);
                }
            });
        });

        // Listen for answer selection events
        document.addEventListener('quiz-answer-selected', function(e) {
            const { questionId, answer, questionType } = e.detail;
            saveAnswer(quizId, questionId, answer, questionType);
        });

        // Listen for question navigation events from Livewire
        document.addEventListener('question-navigated', function(e) {
            const { index } = e.detail;
            updateCurrentQuestionIndex(quizId, index);

            // Load the answer for the new question from localStorage and update UI
            const savedData = getQuizData(quizId);
            if (savedData && savedData.answers) {
                // Find the quiz element and its questions
                const quizElement = document.querySelector('[data-quiz-id]');
                if (quizElement) {
                    // Get all question elements
                    const questionElements = document.querySelectorAll('[data-question-id]');
                    if (questionElements.length > index) {
                        const questionElement = questionElements[index];
                        const questionId = questionElement.dataset.questionId;
                        const questionType = questionElement.dataset.questionType;

                        // Find the answer for this question from localStorage
                        if (savedData.answers[questionId]) {
                            const answerData = savedData.answers[questionId];

                            // Update the UI directly (bypass Livewire for performance)
                            if (questionType === 'multiple_choice') {
                                // For multiple choice, check/uncheck the appropriate checkboxes
                                const checkboxes = questionElement.querySelectorAll('input[type="checkbox"]');
                                checkboxes.forEach(checkbox => {
                                    checkbox.checked = answerData.answer.includes(parseInt(checkbox.value));
                                });
                            } else {
                                // For single choice, select the appropriate radio button
                                const radios = questionElement.querySelectorAll('input[type="radio"]');
                                radios.forEach(radio => {
                                    radio.checked = (parseInt(radio.value) === answerData.answer);
                                });
                            }

                            // Trigger change events to update the visual state
                            const inputs = questionElement.querySelectorAll('input');
                            inputs.forEach(input => {
                                input.dispatchEvent(new Event('change', { bubbles: true }));
                            });
                        }
                    }
                }
            }
        });

        // Listen for request to send localStorage data to Livewire (on submission)
        document.addEventListener('requestLocalStorageData', function() {
            const savedData = exportQuizData(quizId);
            if (savedData && savedData.answers && Object.keys(savedData.answers).length > 0) {
                // Find Livewire component
                const quizElement = document.querySelector('[data-quiz-id]');
                if (quizElement) {
                    const livewireComponent = quizElement.closest('[wire\\:id]');
                    if (livewireComponent && window.Livewire) {
                        const component = window.Livewire.find(livewireComponent.getAttribute('wire:id'));
                        if (component) {
                            // Send localStorage data to Livewire
                            component.call('submitFromLocalStorage', savedData);
                            // Then finalize the submission
                            component.call('finalizeSubmission');
                        }
                    }
                }
            } else {
                // No data in localStorage, just finalize
                const quizElement = document.querySelector('[data-quiz-id]');
                if (quizElement) {
                    const livewireComponent = quizElement.closest('[wire\\:id]');
                    if (livewireComponent && window.Livewire) {
                        const component = window.Livewire.find(livewireComponent.getAttribute('wire:id'));
                        if (component) {
                            component.call('finalizeSubmission');
                        }
                    }
                }
            }
        });

        // Listen for quiz submitted event
        document.addEventListener('quiz-submitted', function() {
            clearQuizData(quizId);
        });

        // Auto-save on visibility change (user switches tabs)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                console.log('Auto-saving quiz data before tab switch');
                // Data is already saved on each action
            }
        });

        // Warn before leaving page if there's unsaved data
        window.addEventListener('beforeunload', function(e) {
            if (hasSavedData(quizId)) {
                e.preventDefault();
                e.returnValue = 'You have unsaved quiz progress. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        // Listen for Livewire events
        if (window.Livewire) {
            window.Livewire.on('quiz-submitted', function() {
                clearQuizData(quizId);
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuizLocalStorage);
    } else {
        initQuizLocalStorage();
    }

    // Expose functions globally for manual use
    window.QuizLocalStorage = {
        getQuizData,
        saveQuizData,
        clearQuizData,
        updateCurrentQuestionIndex,
        saveAnswer,
        getAllAnswers,
        getCurrentQuestionIndex,
        getAnsweredQuestionIds,
        hasSavedData,
        exportQuizData
    };

    console.log('Quiz LocalStorage Manager initialized');
})();

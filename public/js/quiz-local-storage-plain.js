/**
 * Quiz Local Storage Manager - Plain JavaScript
 * Handles client-side data storage for quiz attempts to prevent data loss
 * This implementation doesn't use Alpine.js to avoid conflicts with Livewire
 */

(function () {
    "use strict";

    const STORAGE_KEY_PREFIX = "quiz_attempt_";
    const STORAGE_VERSION = "1.2";
    let isSubmitting = false;

    /**
     * Get storage key for a specific quiz attempt
     */
    function getStorageKey(quizId) {
        return `${STORAGE_KEY_PREFIX}${quizId}`;
    }

    /**
     * Get current quiz data from localStorage
     */
    function getQuizData(quizId) {
        try {
            const key = getStorageKey(quizId);
            const data = localStorage.getItem(key);
            if (!data) return null;

            const parsed = JSON.parse(data);

            // Validate version
            if (parsed.version !== STORAGE_VERSION) {
                clearQuizData(quizId);
                return null;
            }

            return parsed;
        } catch (error) {
            console.error("Error reading quiz data from localStorage:", error);
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
                ...data,
            };
            localStorage.setItem(key, JSON.stringify(dataToSave));
            return true;
        } catch (error) {
            console.error("Error saving quiz data to localStorage:", error);
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
            return true;
        } catch (error) {
            console.error("Error clearing quiz data from localStorage:", error);
            return false;
        }
    }

    /**
     * Save an answer for a question
     */
    function saveAnswer(quizId, questionId, answer, questionType) {
        // Don't save null or undefined answers
        if (answer === null || answer === undefined) {
            return false;
        }

        const data = getQuizData(quizId) || {};

        if (!data.answers) {
            data.answers = {};
        }

        // For matching questions, don't save empty objects
        if (
            questionType === "matching" &&
            typeof answer === "object" &&
            Object.keys(answer).length === 0
        ) {
            // Remove the question from answers if it exists
            if (data.answers && data.answers[questionId]) {
                delete data.answers[questionId];
            }

            // Update current question index when removing answer
            const questionElements =
                document.querySelectorAll("[data-question-id]");
            questionElements.forEach((element, index) => {
                if (element.dataset.questionId === questionId) {
                    data.currentQuestionIndex = index;
                }
            });

            const saved = saveQuizData(quizId, data);

            // Notify Livewire that an answer was removed to update answeredQuestionIds
            if (saved && window.Livewire) {
                const quizElement = document.querySelector("[data-quiz-id]");
                if (quizElement) {
                    const livewireComponent =
                        quizElement.closest("[wire\\:id]");
                    if (livewireComponent) {
                        const component = window.Livewire.find(
                            livewireComponent.getAttribute("wire:id"),
                        );
                        if (component) {
                            component.call(
                                "answerRemovedFromLocalStorage",
                                questionId,
                            );
                        }
                    }
                }
            }

            return saved;
        }

        data.answers[questionId] = {
            answer: answer,
            questionType: questionType,
            timestamp: Date.now(),
        };

        // Update current question index when saving answer
        const questionElements =
            document.querySelectorAll("[data-question-id]");
        questionElements.forEach((element, index) => {
            if (element.dataset.questionId === questionId) {
                data.currentQuestionIndex = index;
            }
        });

        const saved = saveQuizData(quizId, data);

        // Notify Livewire that an answer was saved to update answeredQuestionIds
        if (saved && window.Livewire) {
            const quizElement = document.querySelector("[data-quiz-id]");
            if (quizElement) {
                const livewireComponent = quizElement.closest("[wire\\:id]");
                if (livewireComponent) {
                    const component = window.Livewire.find(
                        livewireComponent.getAttribute("wire:id"),
                    );
                    if (component) {
                        component.call("answerSavedToLocalStorage", questionId);
                    }
                }
            }
        }

        return saved;
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
     * Initialize quiz localStorage functionality
     */
    function initQuizLocalStorage() {
        try {
            // Check if Livewire is available
            if (!window.Livewire) {
                setTimeout(initQuizLocalStorage, 500);
                return;
            }

            const quizElement = document.querySelector("[data-quiz-id]");
            if (!quizElement) {
                return;
            }

            const quizId = quizElement.dataset.quizId;

            // Flag to track if we're currently navigating
            let isNavigating = false;

            // Load data from localStorage
            const savedData = getQuizData(quizId);

            // If there's saved data, sync with Livewire
            if (
                savedData &&
                savedData.answers &&
                Object.keys(savedData.answers).length > 0
            ) {
                const livewireComponent = quizElement.closest("[wire\\:id]");
                if (livewireComponent && window.Livewire) {
                    const component = window.Livewire.find(
                        livewireComponent.getAttribute("wire:id"),
                    );
                    if (component) {
                        component.call("loadFromLocalStorage", savedData);
                    }
                }

                // Also restore UI directly for all questions
                setTimeout(() => {
                    const questionElements =
                        document.querySelectorAll("[data-question-id]");

                    questionElements.forEach((questionElement) => {
                        const questionId = questionElement.dataset.questionId;
                        const questionType =
                            questionElement.dataset.questionType;

                        if (savedData.answers[questionId]) {
                            const answerData = savedData.answers[questionId];

                            // Update UI directly
                            if (questionType === "multiple_choice") {
                                const checkboxes =
                                    questionElement.querySelectorAll(
                                        'input[type="checkbox"]',
                                    );
                                checkboxes.forEach((checkbox) => {
                                    checkbox.checked =
                                        answerData.answer.includes(
                                            parseInt(checkbox.value),
                                        );
                                });
                            } else if (questionType === "matching") {
                                // Restore matching dropdown values
                                const allSelects =
                                    questionElement.querySelectorAll(
                                        "select.quiz-input",
                                    );
                                allSelects.forEach((select) => {
                                    const itemId = select.name.split("_").pop();
                                    if (answerData.answer[itemId]) {
                                        select.value =
                                            answerData.answer[itemId];
                                    }
                                });
                            } else {
                                const radios = questionElement.querySelectorAll(
                                    'input[type="radio"]',
                                );
                                radios.forEach((radio) => {
                                    radio.checked =
                                        String(radio.value) ===
                                        String(answerData.answer);
                                });
                            }
                        }
                    });
                }, 300);
            }

            // Use event delegation for dynamically created inputs
            const quizContainer = document.querySelector("[data-quiz-id]");
            if (quizContainer) {
                quizContainer.addEventListener(
                    "click",
                    function (e) {
                        const input = e.target.closest(".quiz-input");
                        if (!input) return;

                        // Skip saving if we're currently navigating
                        if (isNavigating) {
                            return;
                        }

                        const questionElement =
                            input.closest("[data-question-id]");
                        const questionId = questionElement?.dataset.questionId;
                        const questionType =
                            questionElement?.dataset.questionType;

                        if (!questionId) return;

                        // For multiple choice, collect all selected checkboxes
                        if (questionType === "multiple_choice") {
                            const allCheckboxes =
                                questionElement.querySelectorAll(
                                    'input[type="checkbox"]',
                                );
                            const selectedValues = [];
                            allCheckboxes.forEach((cb) => {
                                if (cb.checked) {
                                    selectedValues.push(cb.value);
                                }
                            });

                            // Dispatch event to save to localStorage with array of selected values
                            const event = new CustomEvent(
                                "quiz-answer-selected",
                                {
                                    detail: {
                                        questionId,
                                        answer: selectedValues,
                                        questionType,
                                    },
                                },
                            );
                            document.dispatchEvent(event);
                        } else {
                            // For single choice, save the selected radio button value
                            if (input.checked) {
                                const event = new CustomEvent(
                                    "quiz-answer-selected",
                                    {
                                        detail: {
                                            questionId,
                                            answer: input.value,
                                            questionType,
                                        },
                                    },
                                );
                                document.dispatchEvent(event);
                            }
                        }
                    },
                    true,
                );

                // Add separate change event listener for select elements (matching questions)
                quizContainer.addEventListener(
                    "change",
                    function (e) {
                        const select = e.target.closest("select.quiz-input");
                        if (!select) return;

                        // Skip saving if we're currently navigating
                        if (isNavigating) {
                            return;
                        }

                        const questionElement =
                            select.closest("[data-question-id]");
                        const questionId = questionElement?.dataset.questionId;
                        const questionType =
                            questionElement?.dataset.questionType;

                        if (!questionId || questionType !== "matching") return;

                        // For matching questions, collect all select dropdown values
                        const allSelects =
                            questionElement.querySelectorAll(
                                "select.quiz-input",
                            );
                        const selectedMatches = {};
                        allSelects.forEach((select) => {
                            const itemId = select.name.split("_").pop();
                            if (select.value && select.value !== "") {
                                selectedMatches[itemId] = select.value;
                            }
                        });

                        // Dispatch event to save to localStorage with object of selected matches
                        const event = new CustomEvent("quiz-answer-selected", {
                            detail: {
                                questionId,
                                answer: selectedMatches,
                                questionType,
                            },
                        });
                        document.dispatchEvent(event);
                    },
                    true,
                );
            }

            // Listen for answer selection events
            document.addEventListener("quiz-answer-selected", function (e) {
                const { questionId, answer, questionType } = e.detail;

                // Only save if answer is not null or undefined
                if (answer !== null && answer !== undefined) {
                    saveAnswer(quizId, questionId, answer, questionType);
                }
            });

            // Listen for question navigation events from Livewire
            document.addEventListener("question-navigated", function (e) {
                const { index } = e.detail;

                // Set navigating flag to prevent overwriting answers during transition
                isNavigating = true;

                // Update current question index WITHOUT saving
                const data = getQuizData(quizId) || {};
                data.currentQuestionIndex = index;

                // Load the answer for new question from localStorage and update UI
                const savedData = getQuizData(quizId);
                if (savedData && savedData.answers) {
                    const quizElement =
                        document.querySelector("[data-quiz-id]");
                    if (quizElement) {
                        const questionElements =
                            document.querySelectorAll("[data-question-id]");
                        if (questionElements.length > index) {
                            const questionElement = questionElements[index];
                            const questionId =
                                questionElement.dataset.questionId;
                            const questionType =
                                questionElement.dataset.questionType;

                            if (savedData.answers[questionId]) {
                                const answerData =
                                    savedData.answers[questionId];

                                // Update the UI directly
                                if (questionType === "multiple_choice") {
                                    const checkboxes =
                                        questionElement.querySelectorAll(
                                            'input[type="checkbox"]',
                                        );
                                    checkboxes.forEach((checkbox) => {
                                        checkbox.checked =
                                            answerData.answer.includes(
                                                parseInt(checkbox.value),
                                            );
                                    });
                                } else if (questionType === "matching") {
                                    // Restore matching dropdown values
                                    const allSelects =
                                        questionElement.querySelectorAll(
                                            "select.quiz-input",
                                        );
                                    allSelects.forEach((select) => {
                                        const itemId = select.name
                                            .split("_")
                                            .pop();
                                        if (answerData.answer[itemId]) {
                                            select.value =
                                                answerData.answer[itemId];
                                        }
                                    });
                                } else {
                                    const radios =
                                        questionElement.querySelectorAll(
                                            'input[type="radio"]',
                                        );
                                    radios.forEach((radio) => {
                                        radio.checked =
                                            String(radio.value) ===
                                            String(answerData.answer);
                                    });
                                }
                            }
                        }
                    }
                }

                // Clear navigating flag after a short delay
                setTimeout(() => {
                    isNavigating = false;
                }, 100);
            });

            // Listen for restore-question event (from Livewire after loading from localStorage)
            if (window.Livewire) {
                window.Livewire.on("restore-question", function () {
                    // Add a delay to allow Livewire to update the DOM
                    setTimeout(() => {
                        const savedData = getQuizData(quizId);

                        if (savedData && savedData.answers) {
                            const quizElement =
                                document.querySelector("[data-quiz-id]");
                            if (quizElement) {
                                const questionElements =
                                    document.querySelectorAll(
                                        "[data-question-id]",
                                    );

                                questionElements.forEach((questionElement) => {
                                    const questionId =
                                        questionElement.dataset.questionId;
                                    const questionType =
                                        questionElement.dataset.questionType;

                                    if (savedData.answers[questionId]) {
                                        const answerData =
                                            savedData.answers[questionId];

                                        // Update the UI directly
                                        if (
                                            questionType === "multiple_choice"
                                        ) {
                                            const checkboxes =
                                                questionElement.querySelectorAll(
                                                    'input[type="checkbox"]',
                                                );
                                            checkboxes.forEach((checkbox) => {
                                                checkbox.checked =
                                                    answerData.answer.includes(
                                                        parseInt(
                                                            checkbox.value,
                                                        ),
                                                    );
                                            });
                                        } else if (
                                            questionType === "matching"
                                        ) {
                                            // Restore matching dropdown values
                                            const allSelects =
                                                questionElement.querySelectorAll(
                                                    "select.quiz-input",
                                                );
                                            allSelects.forEach((select) => {
                                                const itemId = select.name
                                                    .split("_")
                                                    .pop();
                                                if (answerData.answer[itemId]) {
                                                    select.value =
                                                        answerData.answer[
                                                            itemId
                                                        ];
                                                }
                                            });
                                        } else {
                                            const radios =
                                                questionElement.querySelectorAll(
                                                    'input[type="radio"]',
                                                );
                                            radios.forEach((radio) => {
                                                radio.checked =
                                                    String(radio.value) ===
                                                    String(answerData.answer);
                                            });
                                        }
                                    }
                                });
                            }
                        }
                    }, 300);
                });
            }

            // Listen for request to load answer for a specific question (from Livewire)
            if (window.Livewire) {
                window.Livewire.on(
                    "request-answer-for-question",
                    function (params) {
                        const questionId = params.questionId;
                        const questionType = params.questionType;

                        const savedData = getQuizData(quizId);

                        if (
                            savedData &&
                            savedData.answers &&
                            savedData.answers[questionId]
                        ) {
                            const answerData = savedData.answers[questionId];

                            const quizElement =
                                document.querySelector("[data-quiz-id]");
                            if (quizElement) {
                                const livewireComponent =
                                    quizElement.closest("[wire\\:id]");
                                if (livewireComponent && window.Livewire) {
                                    const component = window.Livewire.find(
                                        livewireComponent.getAttribute(
                                            "wire:id",
                                        ),
                                    );
                                    if (component) {
                                        component.call(
                                            "answerLoadedFromStorage",
                                            questionId,
                                            answerData.answer,
                                            questionType,
                                        );
                                    }
                                }
                            }
                        } else {
                            const quizElement =
                                document.querySelector("[data-quiz-id]");
                            if (quizElement) {
                                const livewireComponent =
                                    quizElement.closest("[wire\\:id]");
                                if (livewireComponent && window.Livewire) {
                                    const component = window.Livewire.find(
                                        livewireComponent.getAttribute(
                                            "wire:id",
                                        ),
                                    );
                                    if (component) {
                                        if (questionType === "single_choice") {
                                            component.call(
                                                "answerLoadedFromStorage",
                                                questionId,
                                                null,
                                                questionType,
                                            );
                                        } else if (
                                            questionType === "multiple_choice"
                                        ) {
                                            component.call(
                                                "answerLoadedFromStorage",
                                                questionId,
                                                [],
                                                questionType,
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    },
                );

                // Listen for request to load answered question IDs (from Livewire)
                window.Livewire.on(
                    "request-answered-question-ids",
                    function () {
                        const savedData = getQuizData(quizId);

                        if (savedData && savedData.answers) {
                            const answeredIds = Object.keys(savedData.answers);

                            const quizElement =
                                document.querySelector("[data-quiz-id]");
                            if (quizElement) {
                                const livewireComponent =
                                    quizElement.closest("[wire\\:id]");
                                if (livewireComponent && window.Livewire) {
                                    const component = window.Livewire.find(
                                        livewireComponent.getAttribute(
                                            "wire:id",
                                        ),
                                    );
                                    if (component) {
                                        component.call(
                                            "answeredQuestionIdsLoaded",
                                            answeredIds,
                                        );
                                    }
                                }
                            }
                        } else {
                            const quizElement =
                                document.querySelector("[data-quiz-id]");
                            if (quizElement) {
                                const livewireComponent =
                                    quizElement.closest("[wire\\:id]");
                                if (livewireComponent && window.Livewire) {
                                    const component = window.Livewire.find(
                                        livewireComponent.getAttribute(
                                            "wire:id",
                                        ),
                                    );
                                    if (component) {
                                        component.call(
                                            "answeredQuestionIdsLoaded",
                                            [],
                                        );
                                    }
                                }
                            }
                        }
                    },
                );
            }

            // Listen for request to send localStorage data to Livewire (on submission)
            if (window.Livewire) {
                window.Livewire.on("requestLocalStorageData", function () {
                    const savedData = exportQuizData(quizId);

                    if (
                        savedData &&
                        savedData.answers &&
                        Object.keys(savedData.answers).length > 0
                    ) {
                        const quizElement =
                            document.querySelector("[data-quiz-id]");
                        if (quizElement) {
                            const livewireComponent =
                                quizElement.closest("[wire\\:id]");
                            if (livewireComponent && window.Livewire) {
                                const wireId =
                                    livewireComponent.getAttribute("wire:id");

                                const component = window.Livewire.find(wireId);
                                if (component) {
                                    isSubmitting = true;

                                    component.call(
                                        "submitFromLocalStorage",
                                        savedData,
                                    );

                                    component.call("finalizeSubmission");
                                }
                            }
                        }
                    } else {
                        const quizElement =
                            document.querySelector("[data-quiz-id]");
                        if (quizElement) {
                            const livewireComponent =
                                quizElement.closest("[wire\\:id]");
                            if (livewireComponent && window.Livewire) {
                                const wireId =
                                    livewireComponent.getAttribute("wire:id");

                                const component = window.Livewire.find(wireId);
                                if (component) {
                                    isSubmitting = true;

                                    component.call("finalizeSubmission");
                                }
                            }
                        }
                    }
                });
            }

            // Listen for quiz submitted event
            document.addEventListener("quiz-submitted", function () {
                clearQuizData(quizId);
            });

            // Auto-save on visibility change (user switches tabs)
            document.addEventListener("visibilitychange", function () {
                if (document.visibilityState === "hidden") {
                    // Data is already saved on each action
                }
            });

            // Warn before leaving page if there's unsaved data
            window.addEventListener("beforeunload", function (e) {
                if (isSubmitting) {
                    return;
                }

                if (hasSavedData(quizId)) {
                    e.preventDefault();
                    e.returnValue =
                        "You have unsaved quiz progress. Are you sure you want to leave?";
                    return e.returnValue;
                }
            });

            // Listen for Livewire events
            if (window.Livewire) {
                window.Livewire.on("quiz-submitted", function () {
                    clearQuizData(quizId);
                });
            }
        } catch (error) {
            console.error("Error initializing quiz localStorage:", error);
        }
    }

    // Initialize when DOM is ready and Livewire is available
    function tryInit() {
        if (typeof window.Livewire !== "undefined") {
            if (document.readyState === "loading") {
                document.addEventListener(
                    "DOMContentLoaded",
                    initQuizLocalStorage,
                );
            } else {
                initQuizLocalStorage();
            }
        } else {
            setTimeout(tryInit, 100);
        }
    }
    tryInit();
})();

/**
 * Quiz Local Storage Manager - Plain JavaScript
 * Handles client-side data storage for quiz attempts to prevent data loss
 * This implementation doesn't use Alpine.js to avoid conflicts with Livewire
 */

(function () {
    "use strict";

    console.log("DIAGNOSTIC: quiz-local-storage-plain.js loaded");

    const STORAGE_KEY_PREFIX = "quiz_attempt_";
    const STORAGE_VERSION = "1.2"; // Incremented to force browser reload
    let isSubmitting = false; // Flag to prevent beforeunload alert during submit

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
                console.warn("Quiz data version mismatch, clearing old data");
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
            console.log("Quiz data saved to storage");
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
            console.log("Quiz data cleared from storage");
            return true;
        } catch (error) {
            console.error("Error clearing quiz data from localStorage:", error);
            return false;
        }
    }

    /**
     * Update the current question index
     */
    function updateCurrentQuestionIndex(quizId, index) {
        const data = getQuizData(quizId) || {};
        data.currentQuestionIndex = index;
        // Only save if we have data to avoid overwriting with empty object
        if (Object.keys(data).length > 1) {
            // More than just currentQuestionIndex
            return saveQuizData(quizId, data);
        }
        return false;
    }

    /**
     * Save an answer for a question
     */
    function saveAnswer(quizId, questionId, answer, questionType) {
        console.log("DIAGNOSTIC: saveAnswer called:", {
            quizId,
            questionId,
            answer,
            questionType,
        });

        // Don't save null or undefined answers (prevents overwriting with null)
        if (answer === null || answer === undefined) {
            console.log(
                "DIAGNOSTIC: saveAnswer: Skipping null/undefined answer for question:",
                questionId,
            );
            return false;
        }

        const data = getQuizData(quizId) || {};

        if (!data.answers) {
            data.answers = {};
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
                console.log(
                    "saveAnswer: Updated current question index to:",
                    index,
                );
            }
        });

        console.log(
            "DIAGNOSTIC: saveAnswer: Saving answer for question:",
            questionId,
            "value:",
            answer,
        );
        console.log("DIAGNOSTIC: Current answers in data:", data.answers);
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
                        console.log(
                            "Notified Livewire about saved answer for question:",
                            questionId,
                        );
                    }
                }
            }
        }

        return saved;
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
    window.dispatchQuizAnswerSelected = function (
        questionId,
        answer,
        questionType,
    ) {
        const event = new CustomEvent("quiz-answer-selected", {
            detail: { questionId, answer, questionType },
        });
        document.dispatchEvent(event);
    };

    /**
     * Initialize quiz localStorage functionality
     */
    function initQuizLocalStorage() {
        console.log("DIAGNOSTIC: initQuizLocalStorage() called");

        try {
            // Check if Livewire is available
            console.log(
                "DIAGNOSTIC: window.Livewire exists:",
                !!window.Livewire,
            );
            if (!window.Livewire) {
                console.error(
                    "DIAGNOSTIC: Livewire not found! Retrying in 500ms...",
                );
                setTimeout(initQuizLocalStorage, 500);
                return;
            }

            console.log(
                "DIAGNOSTIC: Livewire version:",
                window.Livewire.version || "unknown",
            );

            const quizElement = document.querySelector("[data-quiz-id]");
            if (!quizElement) {
                console.log("DIAGNOSTIC: No quiz element found!");
                return;
            }

            const quizId = quizElement.dataset.quizId;
            console.log(
                "DIAGNOSTIC: QuizLocalStorage initialized for quiz:",
                quizId,
            );

            // Check if the Livewire component is available
            const livewireComponent = quizElement.closest("[wire\\:id]");
            if (livewireComponent) {
                const wireId = livewireComponent.getAttribute("wire:id");
                console.log(
                    "DIAGNOSTIC: Found Livewire component with wire:id:",
                    wireId,
                );

                if (window.Livewire) {
                    const component = window.Livewire.find(wireId);
                    console.log(
                        "DIAGNOSTIC: Livewire component found:",
                        !!component,
                    );
                    if (component) {
                        console.log(
                            "DIAGNOSTIC: Livewire component name:",
                            component.name || "unknown",
                        );
                    }
                }
            } else {
                console.error(
                    "DIAGNOSTIC: No Livewire component found with wire:id attribute!",
                );
            }

            // Flag to track if we're currently navigating (to prevent overwriting answers)
            let isNavigating = false;

            // Load data from localStorage
            const savedData = getQuizData(quizId);

            // If there's saved data, sync with Livewire
            if (
                savedData &&
                savedData.answers &&
                Object.keys(savedData.answers).length > 0
            ) {
                console.log(
                    "DIAGNOSTIC: Found saved data, syncing with Livewire",
                );
                console.log("DIAGNOSTIC: Saved data:", savedData);

                // Find the Livewire component
                const livewireComponent = quizElement.closest("[wire\\:id]");
                if (livewireComponent && window.Livewire) {
                    const component = window.Livewire.find(
                        livewireComponent.getAttribute("wire:id"),
                    );
                    if (component) {
                        console.log(
                            "DIAGNOSTIC: Calling loadFromLocalStorage on Livewire component",
                        );
                        component.call("loadFromLocalStorage", savedData);
                    }
                }
            }

            // Add event listeners to all quiz inputs
            const quizInputs = document.querySelectorAll(".quiz-input");
            console.log("Found quiz inputs:", quizInputs.length);
            quizInputs.forEach((input) => {
                console.log("Adding event listener to input:", input);
            });

            // DIAGNOSTIC LOG: Check if inputs have listeners after Livewire updates
            console.log(
                "DIAGNOSTIC: Initial quiz inputs found:",
                quizInputs.length,
            );

            // Use event delegation instead of attaching to each input
            // This ensures dynamically created inputs also trigger the event
            const quizContainer = document.querySelector("[data-quiz-id]");
            if (quizContainer) {
                quizContainer.addEventListener(
                    "click",
                    function (e) {
                        const input = e.target.closest(".quiz-input");
                        if (!input) return;

                        console.log("click event fired (via delegation):", {
                            questionId:
                                input.closest("[data-question-id]")?.dataset
                                    .questionId,
                            value: input.value,
                            checked: input.checked,
                            isNavigating: isNavigating,
                        });

                        // Skip saving if we're currently navigating (prevents overwriting answers)
                        if (isNavigating) {
                            console.log("Skipping save during navigation");
                            return;
                        }

                        const questionElement =
                            input.closest("[data-question-id]");
                        const questionId = questionElement?.dataset.questionId;
                        const questionType =
                            questionElement?.dataset.questionType;

                        // Don't convert to Number - keep original value (could be string like "A", "B", "C" or numeric ID)
                        const value = input.value;

                        // Only save if input is actually checked (prevents saving null on uncheck)
                        if (questionId && input.checked) {
                            console.log(
                                "Dispatching quiz-answer-selected event for question:",
                                questionId,
                                "value:",
                                value,
                            );
                            // Dispatch event to save to localStorage
                            const event = new CustomEvent(
                                "quiz-answer-selected",
                                {
                                    detail: {
                                        questionId,
                                        answer: value,
                                        questionType,
                                    },
                                },
                            );
                            document.dispatchEvent(event);
                        } else if (questionId) {
                            console.log(
                                "Skipping save for unchecked input:",
                                questionId,
                            );
                        }
                    },
                    true,
                ); // Use capture phase to ensure we catch the event before Livewire
                console.log(
                    "DIAGNOSTIC: Event delegation listener attached to quiz container",
                );
            } else {
                console.error("DIAGNOSTIC: Quiz container not found!");
            }

            // Listen for answer selection events
            document.addEventListener("quiz-answer-selected", function (e) {
                const { questionId, answer, questionType } = e.detail;
                console.log(
                    "DIAGNOSTIC: quiz-answer-selected event received:",
                    {
                        questionId,
                        answer,
                        questionType,
                    },
                );

                // Only save if answer is not null or undefined (prevents overwriting with null)
                if (answer !== null && answer !== undefined) {
                    saveAnswer(quizId, questionId, answer, questionType);
                } else {
                    console.log(
                        "DIAGNOSTIC: Skipping save for null/undefined answer:",
                        questionId,
                    );
                }
            });

            // Listen for question navigation events from Livewire
            document.addEventListener("question-navigated", function (e) {
                const { index } = e.detail;

                // DIAGNOSTIC: Log navigation event
                console.log(
                    "DIAGNOSTIC: question-navigated event received, index:",
                    index,
                );
                console.log(
                    "DIAGNOSTIC: Current quiz inputs count:",
                    document.querySelectorAll(".quiz-input").length,
                );

                // Set navigating flag to prevent overwriting answers during transition
                isNavigating = true;

                // Update current question index WITHOUT saving (to avoid overwriting answers)
                const data = getQuizData(quizId) || {};
                data.currentQuestionIndex = index;
                // Don't save here - just update in memory
                // saveQuizData will be called when user actually answers a question

                // Load the answer for the new question from localStorage and update UI
                const savedData = getQuizData(quizId);
                if (savedData && savedData.answers) {
                    // Find the quiz element and its questions
                    const quizElement =
                        document.querySelector("[data-quiz-id]");
                    if (quizElement) {
                        // Get all question elements
                        const questionElements =
                            document.querySelectorAll("[data-question-id]");
                        if (questionElements.length > index) {
                            const questionElement = questionElements[index];
                            const questionId =
                                questionElement.dataset.questionId;
                            const questionType =
                                questionElement.dataset.questionType;

                            // Find the answer for this question from localStorage
                            if (savedData.answers[questionId]) {
                                const answerData =
                                    savedData.answers[questionId];

                                // Update the UI directly (bypass Livewire for performance)
                                if (questionType === "multiple_choice") {
                                    // For multiple choice, check/uncheck the appropriate checkboxes
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
                                } else {
                                    // For single choice, select the appropriate radio button
                                    const radios =
                                        questionElement.querySelectorAll(
                                            'input[type="radio"]',
                                        );
                                    radios.forEach((radio) => {
                                        // Compare as strings to handle both numeric and string values
                                        radio.checked =
                                            String(radio.value) ===
                                            String(answerData.answer);
                                    });
                                }
                            }
                        }
                    }
                }

                // Clear navigating flag after a short delay to allow DOM updates to complete
                setTimeout(() => {
                    isNavigating = false;
                    console.log(
                        "DIAGNOSTIC: Navigation complete, re-enabled answer saving",
                    );
                    console.log(
                        "DIAGNOSTIC: Quiz inputs after navigation:",
                        document.querySelectorAll(".quiz-input").length,
                    );
                }, 100);
            });

            // Listen for restore-question event (from Livewire after loading from localStorage)
            if (window.Livewire) {
                window.Livewire.on("restore-question", function () {
                    console.log(
                        "DIAGNOSTIC: restore-question event received, restoring UI from localStorage",
                    );
                    console.log(
                        "DIAGNOSTIC: Current quiz inputs count:",
                        document.querySelectorAll(".quiz-input").length,
                    );

                    // Add a delay to allow Livewire to update the DOM
                    setTimeout(() => {
                        console.log("DIAGNOSTIC: Restoring UI after delay");

                        // Get all saved data from localStorage
                        const savedData = getQuizData(quizId);

                        if (savedData && savedData.answers) {
                            console.log(
                                "DIAGNOSTIC: Found saved answers:",
                                savedData.answers,
                            );

                            // Find the quiz element and its questions
                            const quizElement =
                                document.querySelector("[data-quiz-id]");
                            if (quizElement) {
                                // Get all question elements
                                const questionElements =
                                    document.querySelectorAll(
                                        "[data-question-id]",
                                    );

                                console.log(
                                    "DIAGNOSTIC: Found question elements:",
                                    questionElements.length,
                                );

                                questionElements.forEach((questionElement) => {
                                    const questionId =
                                        questionElement.dataset.questionId;
                                    const questionType =
                                        questionElement.dataset.questionType;

                                    // Find the answer for this question from localStorage
                                    if (savedData.answers[questionId]) {
                                        const answerData =
                                            savedData.answers[questionId];

                                        console.log(
                                            "DIAGNOSTIC: Restoring answer for question:",
                                            questionId,
                                            "type:",
                                            questionType,
                                            "answer:",
                                            answerData.answer,
                                        );

                                        // Update the UI directly (bypass Livewire for performance)
                                        if (
                                            questionType === "multiple_choice"
                                        ) {
                                            // For multiple choice, check/uncheck the appropriate checkboxes
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
                                        } else {
                                            // For single choice, select the appropriate radio button
                                            const radios =
                                                questionElement.querySelectorAll(
                                                    'input[type="radio"]',
                                                );
                                            radios.forEach((radio) => {
                                                // Compare as strings to handle both numeric and string values
                                                radio.checked =
                                                    String(radio.value) ===
                                                    String(answerData.answer);
                                            });
                                        }
                                    }
                                });
                            }
                        } else {
                            console.log(
                                "DIAGNOSTIC: No saved answers found to restore",
                            );
                        }
                    }, 300); // 300ms delay to allow Livewire to update the DOM
                });
            }

            // Listen for request to load answer for a specific question (from Livewire)
            if (window.Livewire) {
                window.Livewire.on(
                    "request-answer-for-question",
                    function (params) {
                        // Livewire passes named parameters as an object
                        const questionId = params.questionId;
                        const questionType = params.questionType;

                        console.log(
                            "DIAGNOSTIC: request-answer-for-question received:",
                            {
                                questionId,
                                questionType,
                                params,
                            },
                        );

                        // Get the saved data from localStorage
                        const savedData = getQuizData(quizId);

                        if (
                            savedData &&
                            savedData.answers &&
                            savedData.answers[questionId]
                        ) {
                            const answerData = savedData.answers[questionId];
                            console.log(
                                "DIAGNOSTIC: Found saved answer for question:",
                                questionId,
                                answerData,
                            );

                            // Send the answer back to Livewire
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
                                        console.log(
                                            "DIAGNOSTIC: Sent answer to Livewire for question:",
                                            questionId,
                                        );
                                    }
                                }
                            }
                        } else {
                            console.log(
                                "DIAGNOSTIC: No saved answer found for question:",
                                questionId,
                            );

                            // Clear the Livewire properties if no answer exists
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
                                        // Send null/empty values to clear the selection
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
                                        console.log(
                                            "Cleared answer in Livewire for question:",
                                            questionId,
                                        );
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
                        console.log("request-answered-question-ids received");

                        // Get the saved data from localStorage
                        const savedData = getQuizData(quizId);

                        if (savedData && savedData.answers) {
                            const answeredIds = Object.keys(savedData.answers);
                            console.log(
                                "Found answered question IDs:",
                                answeredIds,
                            );

                            // Send the answered IDs back to Livewire
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
                                        console.log(
                                            "Sent answered question IDs to Livewire:",
                                            answeredIds,
                                        );
                                    }
                                }
                            }
                        } else {
                            console.log(
                                "No saved answers found, sending empty array",
                            );
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
                    console.log(
                        "DIAGNOSTIC: requestLocalStorageData received - submitQuiz was called",
                    );
                    console.log(
                        "DIAGNOSTIC: requestLocalStorageData event received",
                        {
                            quizId: quizId,
                        },
                    );

                    const savedData = exportQuizData(quizId);
                    console.log(
                        "DIAGNOSTIC: Exported quiz data from localStorage:",
                        savedData,
                    );

                    if (
                        savedData &&
                        savedData.answers &&
                        Object.keys(savedData.answers).length > 0
                    ) {
                        // Find Livewire component
                        const quizElement =
                            document.querySelector("[data-quiz-id]");
                        if (quizElement) {
                            const livewireComponent =
                                quizElement.closest("[wire\\:id]");
                            if (livewireComponent && window.Livewire) {
                                const wireId =
                                    livewireComponent.getAttribute("wire:id");
                                console.log(
                                    "DIAGNOSTIC: Found Livewire component with wire:id:",
                                    wireId,
                                );

                                const component = window.Livewire.find(wireId);
                                if (component) {
                                    console.log(
                                        "DIAGNOSTIC: Calling submitFromLocalStorage with data:",
                                        savedData,
                                    );
                                    // Set submitting flag to prevent beforeunload alert
                                    isSubmitting = true;
                                    console.log(
                                        "DIAGNOSTIC: isSubmitting set to true",
                                    );

                                    // Send localStorage data to Livewire
                                    component.call(
                                        "submitFromLocalStorage",
                                        savedData,
                                    );
                                    console.log(
                                        "DIAGNOSTIC: submitFromLocalStorage called successfully",
                                    );
                                    // Then finalize the submission
                                    console.log(
                                        "DIAGNOSTIC: Calling finalizeSubmission",
                                    );
                                    component.call("finalizeSubmission");
                                }
                            }
                        }
                    } else {
                        console.log(
                            "DIAGNOSTIC: No data in localStorage, just finalizing submission",
                        );
                        // No data in localStorage, just finalize
                        const quizElement =
                            document.querySelector("[data-quiz-id]");
                        if (quizElement) {
                            const livewireComponent =
                                quizElement.closest("[wire\\:id]");
                            if (livewireComponent && window.Livewire) {
                                const wireId =
                                    livewireComponent.getAttribute("wire:id");
                                console.log(
                                    "DIAGNOSTIC: Found Livewire component with wire:id:",
                                    wireId,
                                );

                                const component = window.Livewire.find(wireId);
                                if (component) {
                                    // Set submitting flag to prevent beforeunload alert
                                    isSubmitting = true;
                                    console.log(
                                        "DIAGNOSTIC: isSubmitting set to true (no data)",
                                    );

                                    console.log(
                                        "DIAGNOSTIC: Calling finalizeSubmission (no data)",
                                    );
                                    component.call("finalizeSubmission");
                                }
                            }
                        }
                    }
                });
            }

            // Listen for quiz submitted event
            document.addEventListener("quiz-submitted", function () {
                console.log(
                    "DIAGNOSTIC: quiz-submitted event received in document listener",
                );
                clearQuizData(quizId);
            });

            // Add diagnostic logging for submit button clicks
            document.addEventListener(
                "click",
                function (e) {
                    const submitButton = e.target.closest(
                        'button[wire\\:click="submitQuiz"]',
                    );
                    if (submitButton) {
                        console.log("DIAGNOSTIC: Submit button clicked!", {
                            element: submitButton,
                            disabled: submitButton.disabled,
                            wireClick: submitButton.getAttribute("wire:click"),
                        });
                    }
                },
                true,
            );

            // Auto-save on visibility change (user switches tabs)
            document.addEventListener("visibilitychange", function () {
                if (document.visibilityState === "hidden") {
                    console.log("Auto-saving quiz data before tab switch");
                    // Data is already saved on each action
                }
            });

            // Warn before leaving page if there's unsaved data
            window.addEventListener("beforeunload", function (e) {
                // Don't show alert if quiz is being submitted
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
            console.error(
                "DIAGNOSTIC: Error initializing quiz localStorage:",
                error,
            );
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
            // Livewire not ready, try again in 100ms
            setTimeout(tryInit, 100);
        }
    }
    tryInit();

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
        exportQuizData,
        setSubmitting: (value) => {
            isSubmitting = value;
            console.log("DIAGNOSTIC: isSubmitting set to:", value);
        },
    };

    console.log("Quiz LocalStorage Manager initialized");
})();

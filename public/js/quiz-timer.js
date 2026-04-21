/**
 * Quiz Timer Module
 * Handles countdown timer for quiz attempts
 *
 * Features:
 * - Real-time countdown display
 * - Auto-submit when time expires
 * - Warning notifications at specific thresholds
 * - Visual urgency indicators (color changes)
 * - Handles page refresh scenarios
 */

(function () {
    "use strict";

    const QuizTimer = {
        timerInterval: null,
        timeRemaining: 0,
        quizId: null,
        warningThreshold: 60, // 1 minute warning
        criticalThreshold: 30, // 30 seconds critical
        isSubmitting: false,
        notificationElement: null,

        /**
         * Initialize the timer
         * @param {number} initialTimeRemaining - Initial remaining time in seconds
         * @param {number} quizId - Quiz ID
         */
        init: function (initialTimeRemaining, quizId) {

            this.timeRemaining = initialTimeRemaining;
            this.quizId = quizId;
            this.createNotificationElement();


            // Only start timer if there's time remaining
            if (this.timeRemaining > 0) {
                this.startTimer();
                this.updateDisplay();
            } else {

                // Time already expired, submit immediately
                this.handleTimeExpired();
            }
        },

        /**
         * Create notification element for timer warnings
         */
        createNotificationElement: function () {
            // Check if notification element already exists
            if (document.getElementById("quiz-timer-notification")) {
                this.notificationElement = document.getElementById(
                    "quiz-timer-notification",
                );
                return;
            }

            // Create notification element
            const notification = document.createElement("div");
            notification.id = "quiz-timer-notification";
            notification.className =
                "fixed top-20 right-4 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg shadow-lg z-50 hidden transition-all duration-300";
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span id="quiz-timer-notification-message"></span>
                </div>
            `;
            document.body.appendChild(notification);
            this.notificationElement = notification;
        },

        /**
         * Start the countdown timer
         */
        startTimer: function () {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
            }

            this.timerInterval = setInterval(() => {
                this.tick();
            }, 1000);
        },

        /**
         * Timer tick - called every second
         */
        tick: function () {
            if (this.timeRemaining <= 0) {
                this.handleTimeExpired();
                return;
            }

            this.timeRemaining--;
            this.updateDisplay();
            this.checkWarnings();
        },

        /**
         * Update the timer display
         */
        updateDisplay: function () {
            const timerElement = document.querySelector("[data-quiz-timer]");
            if (timerElement) {
                timerElement.textContent = this.formatTime(this.timeRemaining);
                this.updateTimerColor(timerElement);
            } else {
                console.error("[QuizTimer] Timer element not found!");
            }
        },

        /**
         * Format time as HH:MM:SS
         * @param {number} seconds - Total seconds
         * @returns {string} Formatted time string
         */
        formatTime: function (seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60); // Use Math.floor to ensure integer without decimals

            // Pad with leading zeros
            const hoursStr = hours.toString().padStart(2, "0");
            const minutesStr = minutes.toString().padStart(2, "0");
            const secsStr = secs.toString().padStart(2, "0");

            return `${hoursStr}:${minutesStr}:${secsStr}`;
        },

        /**
         * Update timer color based on remaining time
         * @param {HTMLElement} element - Timer element
         */
        updateTimerColor: function (element) {
            // Remove all color classes
            element.classList.remove(
                "text-yellow-600",
                "text-red-600",
                "animate-pulse",
            );

            if (this.timeRemaining > 300) {
                // Normal state - yellow (more than 5 minutes)
                element.classList.add("text-yellow-600");
            } else {
                // Critical state - red with pulse animation (5 minutes or less)
                element.classList.add("text-red-600", "animate-pulse");
            }
        },

        /**
         * Check for warning thresholds and show notifications
         */
        checkWarnings: function () {
            if (this.timeRemaining === this.warningThreshold) {
                this.showWarning("1 minute remaining!");
            } else if (this.timeRemaining === this.criticalThreshold) {
                this.showWarning("30 seconds remaining!");
            } else if (this.timeRemaining === 10) {
                this.showWarning("10 seconds remaining!");
            } else if (this.timeRemaining === 5) {
                this.showWarning("5 seconds remaining!");
            }
        },

        /**
         * Show warning notification
         * @param {string} message - Warning message
         */
        showWarning: function (message) {
            if (this.notificationElement) {
                const messageElement = this.notificationElement.querySelector(
                    "#quiz-timer-notification-message",
                );
                if (messageElement) {
                    messageElement.textContent = message;
                }

                // Show notification
                this.notificationElement.classList.remove("hidden");

                // Auto-hide after 3 seconds
                setTimeout(() => {
                    this.hideNotification();
                }, 3000);
            }

            // Also dispatch event to Livewire for server-side handling
            if (window.Livewire) {
                const quizElement = document.querySelector("[data-quiz-id]");
                if (quizElement) {
                    const livewireComponent =
                        quizElement.closest("[wire\\:id]");
                    if (livewireComponent) {
                        const component = window.Livewire.find(
                            livewireComponent.getAttribute("wire:id"),
                        );
                        if (component) {
                            component.call("showTimerWarning", message);
                        }
                    }
                }
            }
        },

        /**
         * Hide notification
         */
        hideNotification: function () {
            if (this.notificationElement) {
                this.notificationElement.classList.add("hidden");
            }
        },

        /**
         * Handle timer expiration
         */
        handleTimeExpired: function () {
            this.stopTimer();

            if (!this.isSubmitting) {
                this.isSubmitting = true;

                // Show final warning
                this.showWarning("Time is up! Submitting your quiz...");

                // Auto-submit the quiz after a short delay
                setTimeout(() => {
                    this.autoSubmit();
                }, 1000);
            }
        },

        /**
         * Auto-submit the quiz
         */
        autoSubmit: function () {
            if (window.Livewire) {
                const quizElement = document.querySelector("[data-quiz-id]");
                if (quizElement) {
                    const livewireComponent =
                        quizElement.closest("[wire\\:id]");
                    if (livewireComponent) {
                        const component = window.Livewire.find(
                            livewireComponent.getAttribute("wire:id"),
                        );
                        if (component) {
                            component.call("submitQuiz");
                        }
                    }
                }
            }
        },

        /**
         * Stop the timer
         */
        stopTimer: function () {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }
        },

        /**
         * Get current time remaining
         * @returns {number} Remaining seconds
         */
        getTimeRemaining: function () {
            return this.timeRemaining;
        },

        /**
         * Set time remaining (useful for syncing)
         * @param {number} seconds - Remaining seconds
         */
        setTimeRemaining: function (seconds) {
            this.timeRemaining = seconds;
            this.updateDisplay();
        },
    };

    // Expose to global scope
    window.QuizTimer = QuizTimer;
})();

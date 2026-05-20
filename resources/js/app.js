// import Alpine from "alpinejs";
// import intersect from "@alpinejs/intersect";
// import collapse from "@alpinejs/collapse";
import AOS from "aos";
import "aos/dist/aos.css";
import { createIcons, icons } from "lucide";
import "@splidejs/splide/css";

// ── Alpine ───────────────────────────────────────────────────────────────────
// Alpine.plugin(collapse);
// Alpine.plugin(intersect);
// window.Alpine = Alpine;
// Alpine.start();

// ── Icons ────────────────────────────────────────────────────────────────────
createIcons({ icons });

// ── Quiz Alpine Components ────────────────────────────────────────────────
window.dragDropComponent = function ({
    initialItems,
    shuffledItems,
    wireModel,
    questionId,
    shuffleOnInit,
    hasSavedAnswer,
}) {
    return {
        items: initialItems,
        dragIndex: null,
        questionId: questionId,

        /**
         * Check localStorage directly for saved answer
         * This ensures we detect saved answers even if Livewire state is reset
         */
        getSavedAnswerFromLocalStorage() {
            try {
                const STORAGE_KEY_PREFIX = "quiz_attempt_";
                const STORAGE_VERSION = "1.2";
                const quizElement = document.querySelector("[data-quiz-id]");
                if (!quizElement) return null;

                const quizId = quizElement.dataset.quizId;
                if (!quizId) return null;

                const key = `${STORAGE_KEY_PREFIX}${quizId}`;
                const data = localStorage.getItem(key);
                if (!data) return null;

                const parsed = JSON.parse(data);

                // Validate version
                if (parsed.version !== STORAGE_VERSION) {
                    localStorage.removeItem(key);
                    return null;
                }

                // Check if there's a saved answer for this question
                if (parsed.answers && parsed.answers[questionId]) {
                    const answerData = parsed.answers[questionId];
                    if (
                        answerData.questionType === "drag_drop_sorting" &&
                        answerData.answer
                    ) {
                        return answerData.answer;
                    }
                }

                return null;
            } catch (error) {
                console.error(
                    "[dragDropComponent] Error reading from localStorage:",
                    error,
                );
                return null;
            }
        },

        init() {
            console.log(
                "[dragDropComponent] init called for questionId:",
                questionId,
            );
            console.log(
                "[dragDropComponent] hasSavedAnswer (from prop):",
                hasSavedAnswer,
                "shuffleOnInit:",
                shuffleOnInit,
            );
            console.log("[dragDropComponent] initialItems:", initialItems);
            console.log("[dragDropComponent] shuffledItems:", shuffledItems);

            // Check localStorage directly for saved answer
            const savedAnswer = this.getSavedAnswerFromLocalStorage();
            console.log(
                "[dragDropComponent] savedAnswer from localStorage:",
                savedAnswer,
            );

            // If shuffleOnInit is true AND no saved answer (from prop or localStorage), use shuffled order
            if (shuffleOnInit && !hasSavedAnswer && !savedAnswer) {
                console.log(
                    "[dragDropComponent] Shuffling items because shuffleOnInit is true and no saved answer",
                );
                this.items = shuffledItems;
            } else {
                console.log(
                    "[dragDropComponent] Using initial items (no shuffle or has saved answer)",
                );

                // If there's a saved answer from localStorage, restore it immediately
                if (savedAnswer && Array.isArray(savedAnswer)) {
                    console.log(
                        "[dragDropComponent] Restoring items from localStorage immediately:",
                        savedAnswer,
                    );
                    const itemsMap = {};
                    initialItems.forEach((item) => {
                        itemsMap[item.id] = item;
                    });
                    const restoredItems = savedAnswer
                        .map((id) => itemsMap[id])
                        .filter((item) => item);
                    console.log(
                        "[dragDropComponent] restoredItems:",
                        restoredItems,
                    );
                    this.items = restoredItems;
                }
            }

            console.log("[dragDropComponent] items after init:", this.items);

            // Listen for restore-question event (for restoring from localStorage on reload)
            document.addEventListener("restore-question", (e) => {
                console.log(
                    "[dragDropComponent] restore-question event received:",
                    e.detail,
                );
                if (
                    e.detail.questionId === questionId &&
                    e.detail.questionType === "drag_drop_sorting"
                ) {
                    console.log(
                        "[dragDropComponent] Event matches this question. Restoring items...",
                    );
                    if (e.detail.answer && Array.isArray(e.detail.answer)) {
                        const orderedIds = e.detail.answer;
                        console.log(
                            "[dragDropComponent] orderedIds from saved answer:",
                            orderedIds,
                        );
                        const itemsMap = {};
                        initialItems.forEach((item) => {
                            itemsMap[item.id] = item;
                        });
                        console.log("[dragDropComponent] itemsMap:", itemsMap);
                        const restoredItems = orderedIds
                            .map((id) => itemsMap[id])
                            .filter((item) => item);
                        console.log(
                            "[dragDropComponent] restoredItems:",
                            restoredItems,
                        );
                        this.items = restoredItems;
                        console.log(
                            "[dragDropComponent] items after restoration:",
                            this.items,
                        );
                    } else {
                        console.log(
                            "[dragDropComponent] No valid answer to restore",
                        );
                    }
                } else {
                    console.log(
                        "[dragDropComponent] Event does not match this question (questionId:",
                        questionId,
                        ", event.questionId:",
                        e.detail.questionId,
                        ")",
                    );
                }
            });
        },

        dragStart(index) {
            this.dragIndex = index;
        },

        drop(index) {
            const temp = this.items[this.dragIndex];
            this.items.splice(this.dragIndex, 1);
            this.items.splice(index, 0, temp);
            this.updateState();
        },

        reset() {
            this.items = [...initialItems];
            this.updateState();
        },

        updateState() {
            const orderedIds = this.items.map((i) => i.id);
            const livewireEl = this.$root.closest("[wire\\:id]");
            if (livewireEl && window.Livewire) {
                const component = window.Livewire.find(
                    livewireEl.getAttribute("wire:id"),
                );
                if (component) {
                    component.set(`${wireModel}.${questionId}`, orderedIds);
                }
            }
            const event = new CustomEvent("quiz-answer-selected", {
                detail: {
                    questionId: this.questionId,
                    answer: orderedIds,
                    questionType: "drag_drop_sorting",
                },
            });
            document.dispatchEvent(event);
        },
    };
};

window.matchingComponent = function ({
    items,
    matches,
    selected,
    wireModel,
    questionId,
}) {
    return {
        items: items,
        matches: matches,
        selected: Array.isArray(selected) ? {} : selected || {},
        wireModel: wireModel,
        questionId: questionId,
        draggedItemId: null,
        hoveredMatchId: null,

        /**
         * Helper function to read quiz data from localStorage
         * Similar to quiz-local-storage-plain.js implementation
         */
        getQuizDataFromLocalStorage(quizId) {
            try {
                const STORAGE_KEY_PREFIX = "quiz_attempt_";
                const STORAGE_VERSION = "1.2";
                const key = `${STORAGE_KEY_PREFIX}${quizId}`;
                const data = localStorage.getItem(key);
                if (!data) return null;

                const parsed = JSON.parse(data);

                // Validate version
                if (parsed.version !== STORAGE_VERSION) {
                    localStorage.removeItem(key);
                    return null;
                }

                return parsed;
            } catch (error) {
                return null;
            }
        },

        init() {
            // Get quizId from DOM element with data-quiz-id attribute
            const quizElement = document.querySelector("[data-quiz-id]");
            const quizId = quizElement ? quizElement.dataset.quizId : null;

            // Check localStorage directly for saved data (proactive check)
            if (quizId) {
                const savedData = this.getQuizDataFromLocalStorage(quizId);
                if (
                    savedData &&
                    savedData.answers &&
                    savedData.answers[this.questionId]
                ) {
                    const answerData = savedData.answers[this.questionId];
                    if (
                        answerData.questionType === "matching" &&
                        answerData.answer
                    ) {
                        this.selected = answerData.answer;
                    }
                }
            }

            // Keep the restore-question event listener as a fallback for other scenarios
            document.addEventListener("restore-question", (e) => {
                if (
                    e.detail.questionId === this.questionId &&
                    e.detail.questionType === "matching"
                ) {
                    this.selected = e.detail.answer || {};
                }
            });
        },

        isItemMatched(itemId) {
            return this.selected && this.selected.hasOwnProperty(itemId);
        },

        getMatchedItem(matchId) {
            const itemId = Object.keys(this.selected).find(
                (key) => this.selected[key] === matchId,
            );
            if (!itemId) return null;
            return (
                this.items.find((item) => item && item.id === itemId) || null
            );
        },

        getItemNumber(itemId) {
            const index = this.items.findIndex(
                (item) => item && item.id === itemId,
            );
            return index >= 0 ? index + 1 : "";
        },

        getMatchLetter(matchId) {
            const index = this.matches.findIndex(
                (match) => match.id === matchId,
            );
            return String.fromCharCode(97 + index);
        },

        getMatchZoneClass(matchId) {
            const hasMatch = !!this.getMatchedItem(matchId);
            const isHovered = this.hoveredMatchId === matchId;
            if (hasMatch) return "border-[#ffc107] bg-yellow-50/30";
            else if (isHovered)
                return "border-[#ffc107] border-dashed bg-yellow-50/50";
            else return "border-gray-200 bg-jti-yellow/5";
        },

        dragStart(event) {
            const itemId = event.target.getAttribute("data-item-id");
            this.draggedItemId = itemId;
            event.dataTransfer.effectAllowed = "move";
            event.dataTransfer.setData("text/plain", itemId);
            event.target.classList.add("opacity-50");
        },

        dragEnd(event) {
            this.draggedItemId = null;
            this.hoveredMatchId = null;
            event.target.classList.remove("opacity-50");
        },

        dragOver(event, matchId) {
            const matchedItem = this.getMatchedItem(matchId);
            if (!matchedItem) {
                this.hoveredMatchId = matchId;
                event.dataTransfer.dropEffect = "move";
            }
        },

        dragLeave(event, matchId) {
            this.hoveredMatchId = null;
        },

        drop(event, matchId) {
            event.preventDefault();
            this.hoveredMatchId = null;
            const itemId = event.dataTransfer.getData("text/plain");

            if (!itemId || !this.draggedItemId) {
                return;
            }

            const existingMatchId = this.selected[itemId];
            if (existingMatchId) delete this.selected[itemId];
            this.selected[itemId] = matchId;

            this.updateState();
        },

        removeMatch(matchId) {
            const itemId = Object.keys(this.selected).find(
                (key) => this.selected[key] === matchId,
            );
            if (itemId) {
                delete this.selected[itemId];
                this.updateState();
            }
        },

        resetAll() {
            this.selected = {};
            this.updateState();
        },

        updateState() {
            const livewireEl = this.$root.closest("[wire\\:id]");
            if (livewireEl && window.Livewire) {
                const component = window.Livewire.find(
                    livewireEl.getAttribute("wire:id"),
                );
                if (component) {
                    component.set(
                        `${this.wireModel}.${this.questionId}`,
                        this.selected,
                    );
                }
            }

            const event = new CustomEvent("quiz-answer-selected", {
                detail: {
                    questionId: this.questionId,
                    answer: this.selected,
                    questionType: "matching",
                },
            });
            document.dispatchEvent(event);
        },
    };
};

// ── Drop Down Component (Alpine) ─────────────────────────────────────────
window.dropDownComponent = function ({
    template,
    blanksMap,
    selected,
    wireModel,
    questionId,
}) {
    return {
        template: template,
        blanksMap: blanksMap,
        selected: Array.isArray(selected) ? {} : selected || {},
        wireModel: wireModel,
        questionId: questionId,
        renderedTemplate: "",

        getQuizDataFromLocalStorage(quizId) {
            try {
                const STORAGE_KEY_PREFIX = "quiz_attempt_";
                const STORAGE_VERSION = "1.2";
                const key = `${STORAGE_KEY_PREFIX}${quizId}`;
                const data = localStorage.getItem(key);
                if (!data) return null;

                const parsed = JSON.parse(data);
                if (parsed.version !== STORAGE_VERSION) {
                    localStorage.removeItem(key);
                    return null;
                }
                return parsed;
            } catch (error) {
                return null;
            }
        },

        init() {
            // Get quizId from DOM and check localStorage directly
            const quizElement = document.querySelector("[data-quiz-id]");
            const quizId = quizElement ? quizElement.dataset.quizId : null;

            if (quizId) {
                const savedData = this.getQuizDataFromLocalStorage(quizId);
                if (
                    savedData &&
                    savedData.answers &&
                    savedData.answers[this.questionId]
                ) {
                    const answerData = savedData.answers[this.questionId];
                    if (
                        answerData.questionType === "drop_down" &&
                        answerData.answer
                    ) {
                        this.selected = Array.isArray(answerData.answer)
                            ? {}
                            : answerData.answer;
                    }
                }
            }

            this.renderedTemplate = this.renderTemplate();

            const self = this;
            this.$el.addEventListener("change", (e) => {
                const select = e.target.closest("select[data-blank-id]");
                if (select) {
                    self.selectDropdown(select.dataset.blankId, select.value);
                }
            });

            // Listen for restore-question event
            document.addEventListener("restore-question", (e) => {
                if (
                    e.detail.questionId === this.questionId &&
                    e.detail.questionType === "drop_down"
                ) {
                    if (
                        e.detail.answer &&
                        typeof e.detail.answer === "object"
                    ) {
                        this.selected = Array.isArray(e.detail.answer)
                            ? {}
                            : e.detail.answer;
                        this.renderedTemplate = this.renderTemplate();
                    }
                }
            });

            // Check Livewire value when navigating to this question
            document.addEventListener("question-navigated", (e) => {
                if (e.detail.index !== undefined) {
                    const quizElement =
                        document.querySelector("[data-quiz-id]");
                    if (quizElement) {
                        const questionElements =
                            quizElement.querySelectorAll("[data-question-id]");
                        const currentQuestionElement =
                            questionElements[e.detail.index];
                        if (
                            currentQuestionElement &&
                            String(
                                currentQuestionElement.dataset.questionId,
                            ) === String(this.questionId)
                        ) {
                            setTimeout(() => this.checkLivewireValue(), 100);
                        }
                    }
                }
            });

            // Also check after a short delay to handle initial load scenarios
            setTimeout(() => this.checkLivewireValue(), 500);
        },

        checkLivewireValue() {
            const livewireEl = this.$root?.closest("[wire\\:id]");
            if (livewireEl && window.Livewire) {
                const component = window.Livewire.find(
                    livewireEl.getAttribute("wire:id"),
                );
                if (component) {
                    const livewireValue = component.get(
                        `${this.wireModel}.${this.questionId}`,
                    );
                    const normalizedValue = Array.isArray(livewireValue)
                        ? {}
                        : livewireValue || {};
                    if (
                        JSON.stringify(normalizedValue) !==
                        JSON.stringify(this.selected)
                    ) {
                        this.selected = normalizedValue;
                        this.renderedTemplate = this.renderTemplate();
                    }
                }
            }
        },

        getBlankIds() {
            const regex = /\{(blank\d+)\}/g;
            const ids = [];
            let match;
            while ((match = regex.exec(this.template)) !== null) {
                if (!ids.includes(match[1])) {
                    ids.push(match[1]);
                }
            }
            return ids;
        },

        renderTemplate() {
            let result = this.template;
            const blankIds = this.getBlankIds();

            blankIds.forEach((blankId) => {
                const options = this.blanksMap[blankId] || [];
                const selectedValue = this.selected[blankId] || "";

                // Build dropdown HTML
                let dropdownHtml = `<select
                    class="quiz-input bg-gray-800 text-yellow-400 px-2 py-1 rounded border border-yellow-600 focus:border-yellow-400 focus:outline-none cursor-pointer text-xs md:text-sm font-mono"
                    data-blank-id="${blankId}"
                >
                    <option value="" ${selectedValue === "" ? "selected" : ""}>Select...</option>`;

                options.forEach((opt) => {
                    dropdownHtml += `<option value="${opt.id}" ${selectedValue === opt.id ? "selected" : ""}>${opt.text}</option>`;
                });

                dropdownHtml += `</select>`;

                // Replace {blankId} with the dropdown
                result = result.replace(
                    new RegExp(`\\{${blankId}\\}`, "g"),
                    dropdownHtml,
                );
            });

            // Escape HTML for display while keeping our select elements
            return result;
        },

        selectDropdown(blankId, value) {
            if (value) {
                this.selected[blankId] = value;
            } else {
                delete this.selected[blankId];
            }
            this.renderedTemplate = this.renderTemplate();
            this.updateState();
        },

        updateState() {
            // Update Livewire
            const livewireEl = this.$root.closest("[wire\\:id]");
            if (livewireEl && window.Livewire) {
                const component = window.Livewire.find(
                    livewireEl.getAttribute("wire:id"),
                );
                if (component) {
                    component.set(`${wireModel}.${questionId}`, this.selected);
                }
            }

            // Dispatch event for localStorage
            const event = new CustomEvent("quiz-answer-selected", {
                detail: {
                    questionId: this.questionId,
                    answer: this.selected,
                    questionType: "drop_down",
                },
            });
            document.dispatchEvent(event);
        },
    };
};

// ── Matching JS Component (Vanilla JS) ─────────────────────────────────────
window.initMatchingComponent = function (
    componentId,
    items,
    matches,
    selected,
    wireModel,
    questionId,
) {
    const container = document.getElementById(componentId);
    if (!container) return;

    let draggedItemId = null;
    let hoveredMatchId = null;

    // Get available items
    function getAvailableItems() {
        const matchedItemIds = Object.values(selected);
        return items.filter((item) => !matchedItemIds.includes(item.id));
    }

    // Get matched item
    function getMatchedItem(matchId) {
        const itemId = Object.keys(selected).find(
            (key) => selected[key] === matchId,
        );
        if (!itemId) return null;
        return items.find((item) => item.id === itemId) || null;
    }

    // Get item number
    function getItemNumber(itemId) {
        const index = items.findIndex((item) => item.id === itemId);
        return index + 1;
    }

    // Get match letter
    function getMatchLetter(matchId) {
        const index = matches.findIndex((match) => match.id === matchId);
        return String.fromCharCode(97 + index);
    }

    // Update state
    function updateState() {
        const livewireEl = container.closest("[wire\\:id]");
        if (livewireEl && window.Livewire) {
            const component = window.Livewire.find(
                livewireEl.getAttribute("wire:id"),
            );
            if (component) {
                component.set(`${wireModel}.${questionId}`, selected);
            }
        }
        const event = new CustomEvent("quiz-answer-selected", {
            detail: {
                questionId: questionId,
                answer: selected,
                questionType: "matching",
            },
        });
        document.dispatchEvent(event);
    }

    // Render items
    function renderItems() {
        const itemsContainer = document.getElementById(`${componentId}-items`);
        if (!itemsContainer) return;

        const availableItems = getAvailableItems();
        itemsContainer.innerHTML = availableItems
            .map(
                (item) => `
            <div
                class="matching-item p-3 md:p-4 rounded-lg border-2 border-gray-200 bg-white hover:border-[#ffc107] hover:bg-yellow-50/30 cursor-grab active:cursor-grabbing transition-all shadow-sm hover:shadow-md ${draggedItemId === item.id ? "opacity-50" : ""}"
                draggable="true"
                data-item-id="${item.id}"
                data-item-text="${item.text}"
            >
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-sm font-semibold mr-3">
                        <span>${getItemNumber(item.id)}</span>
                    </div>
                    <span class="font-medium text-gray-700 text-sm md:text-base">${item.text}</span>
                </div>
            </div>
        `,
            )
            .join("");

        // Add event listeners to items
        itemsContainer.querySelectorAll(".matching-item").forEach((item) => {
            item.addEventListener("dragstart", handleDragStart);
            item.addEventListener("dragend", handleDragEnd);
        });
    }

    // Render matches
    function renderMatches() {
        const matchesContainer = document.getElementById(
            `${componentId}-matches`,
        );
        if (!matchesContainer) return;

        matchesContainer.innerHTML = matches
            .map((match) => {
                const matchedItem = getMatchedItem(match.id);
                const matchLetter = getMatchLetter(match.id);
                const hasMatch = !!matchedItem;
                const isHovered = hoveredMatchId === match.id;

                let borderClass = "border-gray-200 bg-white";
                if (hasMatch) borderClass = "border-[#ffc107] bg-yellow-50/30";
                else if (isHovered)
                    borderClass =
                        "border-[#ffc107] border-dashed bg-yellow-50/50";

                return `
                <div
                    class="match-zone p-3 md:p-4 rounded-lg border-2 transition-all min-h-[60px] flex items-center ${borderClass}"
                    data-match-id="${match.id}"
                    data-match-text="${match.text}"
                >
                    <div class="w-full flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-sm font-semibold mr-3">
                            <span>${matchLetter}</span>
                        </div>
                        <div class="flex-1">
                            ${
                                matchedItem
                                    ? `
                                <div class="flex items-center justify-between bg-[#ffc107] text-white rounded-md px-3 py-2 matched-item" data-match-id="${match.id}">
                                    <span class="font-medium text-sm">${matchedItem.text}</span>
                                    <button
                                        class="remove-match ml-2 hover:bg-white/20 rounded-full p-1 transition-colors"
                                        title="Remove match"
                                        data-match-id="${match.id}"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            `
                                    : `
                                <div class="text-gray-400 text-sm empty-state ${isHovered ? "text-[#ffc107]" : ""}" data-match-id="${match.id}">
                                    <span>${isHovered ? "Drop here" : match.text}</span>
                                </div>
                            `
                            }
                        </div>
                    </div>
                </div>
            `;
            })
            .join("");

        // Add event listeners to match zones
        matchesContainer.querySelectorAll(".match-zone").forEach((zone) => {
            zone.addEventListener("dragover", handleDragOver);
            zone.addEventListener("dragleave", handleDragLeave);
            zone.addEventListener("drop", handleDrop);
        });

        // Add event listeners to remove buttons
        matchesContainer.querySelectorAll(".remove-match").forEach((button) => {
            button.addEventListener("click", handleRemoveMatch);
        });
    }

    // Drag start handler
    function handleDragStart(e) {
        draggedItemId = e.target.getAttribute("data-item-id");
        e.dataTransfer.effectAllowed = "move";
        e.dataTransfer.setData("text/plain", draggedItemId);
        e.target.classList.add("opacity-50");
    }

    // Drag end handler
    function handleDragEnd(e) {
        draggedItemId = null;
        hoveredMatchId = null;
        e.target.classList.remove("opacity-50");
        renderItems();
        renderMatches();
    }

    // Drag over handler
    function handleDragOver(e) {
        e.preventDefault();
        const matchId = e.currentTarget.getAttribute("data-match-id");
        const matchedItem = getMatchedItem(matchId);
        if (!matchedItem) {
            hoveredMatchId = matchId;
            e.dataTransfer.dropEffect = "move";
            renderMatches();
        }
    }

    // Drag leave handler
    function handleDragLeave(e) {
        hoveredMatchId = null;
        renderMatches();
    }

    // Drop handler
    function handleDrop(e) {
        e.preventDefault();
        hoveredMatchId = null;
        const matchId = e.currentTarget.getAttribute("data-match-id");
        const itemId = e.dataTransfer.getData("text/plain");

        if (!itemId || !draggedItemId) return;

        const existingMatchId = selected[itemId];
        if (existingMatchId) delete selected[itemId];

        selected[itemId] = matchId;
        updateState();
        renderItems();
        renderMatches();
    }

    // Remove match handler
    function handleRemoveMatch(e) {
        e.stopPropagation();
        const matchId = e.currentTarget.getAttribute("data-match-id");
        const itemId = Object.keys(selected).find(
            (key) => selected[key] === matchId,
        );

        if (itemId) {
            delete selected[itemId];
            updateState();
            renderItems();
            renderMatches();
        }
    }

    // Reset all handler
    function handleResetAll() {
        selected = {};
        updateState();
        renderItems();
        renderMatches();
    }

    // Listen for restore-question event
    document.addEventListener("restore-question", (e) => {
        if (
            e.detail.questionId === questionId &&
            e.detail.questionType === "matching"
        ) {
            selected = e.detail.answer || {};
            renderItems();
            renderMatches();
        }
    });

    // Initialize
    renderItems();
    renderMatches();

    // Add reset button listener
    const resetButton = document.getElementById(`${componentId}-reset`);
    if (resetButton) {
        resetButton.addEventListener("click", handleResetAll);
    }
};

// ── AOS ──────────────────────────────────────────────────────────────────────
AOS.init({
    duration: 600,
    easing: "ease-in-out",
    delay: 100,
    once: true,
});

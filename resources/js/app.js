import Alpine from "alpinejs";
import intersect from "@alpinejs/intersect";
import collapse from "@alpinejs/collapse";
import AOS from "aos";
import "aos/dist/aos.css";
import { createIcons, icons } from "lucide";
import Splide from "@splidejs/splide";
import "@splidejs/splide/css";
import "./cart.js"; // ← Import cart functionality

// ── Icons ────────────────────────────────────────────────────────────────────
createIcons({ icons });

// ── Quiz Alpine Components ────────────────────────────────────────────────
window.dragDropComponent = function({ initialItems, wireModel, questionId }) {
    return {
        items: initialItems,
        dragIndex: null,

        init() {
            document.addEventListener('restore-question', (e) => {
                if (e.detail.questionId === this.questionId && e.detail.questionType === 'drag_drop_sorting') {
                    if (e.detail.answer && Array.isArray(e.detail.answer)) {
                        const orderedIds = e.detail.answer;
                        const itemsMap = {};
                        initialItems.forEach(item => {
                            itemsMap[item.id] = item;
                        });
                        this.items = orderedIds.map(id => itemsMap[id]).filter(item => item);
                    }
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
            const orderedIds = this.items.map(i => parseInt(i.id));
            const livewireEl = this.$root.closest('[wire\\:id]');
            if (livewireEl && window.Livewire) {
                const component = window.Livewire.find(livewireEl.getAttribute('wire:id'));
                if (component) {
                    component.set(`${wireModel}.${questionId}`, orderedIds);
                }
            }
            const event = new CustomEvent('quiz-answer-selected', {
                detail: {
                    questionId: this.questionId,
                    answer: orderedIds,
                    questionType: 'drag_drop_sorting'
                }
            });
            document.dispatchEvent(event);
        }
    };
};

window.matchingComponent = function({ items, matches, selected, wireModel, questionId }) {
    return {
        items: items,
        matches: matches,
        selected: Array.isArray(selected) ? {} : (selected || {}),
        wireModel: wireModel,
        questionId: questionId,
        draggedItemId: null,
        hoveredMatchId: null,

        init() {
            document.addEventListener('restore-question', (e) => {
                if (e.detail.questionId === this.questionId && e.detail.questionType === 'matching') {
                    this.selected = e.detail.answer || {};
                }
            });
        },

        get availableItems() {
            const matchedItemIds = Object.values(this.selected);
            return this.items.filter(item => !matchedItemIds.includes(item.id));
        },

        getMatchedItem(matchId) {
            const itemId = Object.keys(this.selected).find(key => this.selected[key] === matchId);
            if (!itemId) return null;
            return this.items.find(item => item.id === itemId);
        },

        getItemNumber(itemId) {
            const index = this.items.findIndex(item => item.id === itemId);
            return index + 1;
        },

        getMatchLetter(matchId) {
            const index = this.matches.findIndex(match => match.id === matchId);
            return String.fromCharCode(97 + index);
        },

        getMatchZoneClass(matchId) {
            const hasMatch = !!this.getMatchedItem(matchId);
            const isHovered = this.hoveredMatchId === matchId;
            if (hasMatch) return 'border-[#ffc107] bg-yellow-50/30';
            else if (isHovered) return 'border-[#ffc107] border-dashed bg-yellow-50/50';
            else return 'border-gray-200 bg-white';
        },

        dragStart(event, itemId) {
            this.draggedItemId = itemId;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', itemId);
            event.target.classList.add('opacity-50');
        },

        dragEnd(event) {
            this.draggedItemId = null;
            this.hoveredMatchId = null;
            event.target.classList.remove('opacity-50');
        },

        dragOver(event, matchId) {
            const matchedItem = this.getMatchedItem(matchId);
            if (!matchedItem) {
                this.hoveredMatchId = matchId;
                event.dataTransfer.dropEffect = 'move';
            }
        },

        dragLeave(event, matchId) {
            this.hoveredMatchId = null;
        },

        drop(event, matchId) {
            event.preventDefault();
            this.hoveredMatchId = null;
            const itemId = event.dataTransfer.getData('text/plain');
            if (!itemId || !this.draggedItemId) return;
            const existingMatchId = this.selected[itemId];
            if (existingMatchId) delete this.selected[itemId];
            this.selected[itemId] = matchId;
            this.updateState();
        },

        removeMatch(matchId) {
            const itemId = Object.keys(this.selected).find(key => this.selected[key] === matchId);
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
            const livewireEl = this.$root.closest('[wire\\:id]');
            if (livewireEl && window.Livewire) {
                const component = window.Livewire.find(livewireEl.getAttribute('wire:id'));
                if (component) {
                    component.set(`${this.wireModel}.${this.questionId}`, this.selected);
                }
            }
            const event = new CustomEvent('quiz-answer-selected', {
                detail: {
                    questionId: this.questionId,
                    answer: this.selected,
                    questionType: 'matching'
                }
            });
            document.dispatchEvent(event);
        }
    };
};

// ── Alpine ───────────────────────────────────────────────────────────────────
Alpine.plugin(collapse);
Alpine.plugin(intersect);
window.Alpine = Alpine;
Alpine.start();

// ── AOS ──────────────────────────────────────────────────────────────────────
AOS.init({
    duration: 600,
    easing: "ease-in-out",
    delay: 100,
    once: true,
});

// ── Splide ───────────────────────────────────────────────────────────────────
/**
 * Inisialisasi semua carousel Splide di halaman.
 * Cukup tambahkan atribut data-splide="..." pada elemen HTML,
 * dan fungsi ini akan otomatis menginisialisasinya.
 *
 * Contoh penggunaan di Blade:
 *   <div id="instructor-carousel" class="splide" data-splide-config>
 *
 * Untuk konfigurasi custom per-carousel, gunakan data attribute:
 *   <div class="splide"
 *        data-splide-per-page="3"
 *        data-splide-type="loop"
 *        data-splide-autoplay="true">
 */
function initSplideCarousels() {
    document
        .querySelectorAll(".splide:not([data-splide-initialized])")
        .forEach((el) => {
            // Baca konfigurasi dari data attributes (opsional, override default)
            const perPage = parseInt(el.dataset.splidePerPage) || 1;
            const type = el.dataset.splideType || "loop";
            const autoplay = el.dataset.splideAutoplay !== "false";
            const interval = parseInt(el.dataset.splideInterval) || 3500;
            const gap = el.dataset.splideGap || "1.5rem";

            const splide = new Splide(el, {
                type: type,
                perPage: perPage,
                gap: gap,
                autoplay: autoplay,
                interval: interval,
                pauseOnHover: true,
                arrows: false,
                pagination: false,
                breakpoints: {
                    1280: { perPage: Math.min(perPage, 3) },
                    1024: { perPage: Math.min(perPage, 2) },
                    640: { perPage: 1, padding: { right: "12%" } },
                },
            });

            splide.mount();

            // Tandai sudah di-init agar tidak double-init
            el.dataset.splideInitialized = "true";

            // Re-render Lucide icons karena Splide mengklon slide
            createIcons({ icons });
        });
}

// Jalankan saat DOM siap
document.addEventListener("DOMContentLoaded", initSplideCarousels);

// Support navigasi Livewire v3 (SPA-like navigation)
document.addEventListener("livewire:navigated", () => {
    // Reset flag agar carousel di halaman baru bisa di-init ulang
    document
        .querySelectorAll(".splide[data-splide-initialized]")
        .forEach((el) => {
            delete el.dataset.splideInitialized;
        });
    initSplideCarousels();
    AOS.refreshHard();
});

// Expose ke window jika perlu dipanggil manual dari komponen lain
window.initSplideCarousels = initSplideCarousels;

// ── Toast ─────────────────────────────────────────────────────────────────────
window.showToast = function (message, type = "success", duration = 4000) {
    const container = document.getElementById("toast-container");
    if (!container) return;

    const toastId = `toast-${Date.now()}`;

    const styles = {
        success: {
            bg: "bg-green-50 border-green-200",
            iconColor: "text-green-600",
            textColor: "text-green-800",
            buttonColor: "text-green-600 hover:text-green-800",
            icon: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
        },
        error: {
            bg: "bg-red-50 border-red-200",
            iconColor: "text-red-600",
            textColor: "text-red-800",
            buttonColor: "text-red-600 hover:text-red-800",
            icon: "M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
        },
        info: {
            bg: "bg-blue-50 border-blue-200",
            iconColor: "text-blue-600",
            textColor: "text-blue-800",
            buttonColor: "text-blue-600 hover:text-blue-800",
            icon: "M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
        },
    };

    const style = styles[type] || styles.info;

    const toast = document.createElement("div");
    toast.id = toastId;
    toast.className = `flex items-center p-4 min-w-[300px] border rounded-xl shadow-xl ${style.bg}`;
    toast.setAttribute("data-aos", "fade-left");
    toast.setAttribute("data-aos-duration", "400");

    toast.innerHTML = `
        <svg class="w-6 h-6 ${style.iconColor} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="${style.icon}" />
        </svg>
        <div class="ml-3 flex-1">
            <p class="font-semibold ${style.textColor} text-sm leading-tight">${message}</p>
        </div>
        <button onclick="document.getElementById('${toastId}').remove()" class="${style.buttonColor} ml-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;

    container.appendChild(toast);

    if (window.AOS) AOS.refresh();

    if (duration > 0) {
        setTimeout(() => {
            const el = document.getElementById(toastId);
            if (el) {
                el.style.opacity = "0";
                el.style.transform = "translateX(120%)";
                el.style.transition = "all 400ms ease-out";
                setTimeout(() => el.remove(), 400);
            }
        }, duration);
    }
};

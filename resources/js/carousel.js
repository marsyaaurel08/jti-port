import Splide from "@splidejs/splide";
import { createIcons, icons } from "lucide";
import "./cart.js"; // ← Import cart functionality
import "./toast.js"; // ← Import toast functionality

/**
 * Inisialisasi semua carousel Splide di halaman.
 * Cukup tambahkan atribut data-splide="..." pada elemen HTML,
 * dan fungsi ini akan otomatis menginisialisasinya.
 */
export function initSplideCarousels() {
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
    if (window.AOS) window.AOS.refreshHard();
});

// Expose ke window jika perlu dipanggil manual dari komponen lain
window.initSplideCarousels = initSplideCarousels;

import "./bootstrap";
import Alpine from "alpinejs";
import intersect from "@alpinejs/intersect";
import collapse from "@alpinejs/collapse";
import AOS from "aos";
import "aos/dist/aos.css";

Alpine.plugin(collapse);
Alpine.plugin(intersect);

window.Alpine = Alpine;
Alpine.start();

// Initialize AOS for animations
AOS.init({
    duration: 600,
    easing: "ease-in-out",
    delay: 100,
    once: false,
});
/**
 * Global Handler untuk menangani Response Fetch
 * Fokus pada penampilan Toast tanpa pengalihan halaman (Redirect)
 */
async function handleFetchResponse(response) {
    // Jika user belum login (Unauthorized)
    if (response.status === 401) {
        window.showToast(
            "Silahkan login terlebih dahulu untuk menambah ke keranjang",
            "info",
        );
        return null;
    }

    // Jika terjadi pengalihan (biasanya ke halaman login)
    if (response.redirected) {
        window.showToast("Akses ditolak. Silahkan login kembali.", "info");
        return null;
    }

    // Jika terjadi error server (500, 404, dll)
    if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        window.showToast(
            errorData.message || "Terjadi kesalahan pada sistem",
            "error",
        );
        return null;
    }

    return response.json();
}

/**
 * Fungsi Utama: Tambah Kursus ke Keranjang
 */
window.addToCart = async function (courseId, callback) {
    try {
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]',
        )?.content;

        const response = await fetch(`/student/cart/add/${courseId}`, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-Token": csrfToken || "",
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "include",
        });

        const data = await handleFetchResponse(response);

        if (data) {
            window.showToast(data.message, data.success ? "success" : "info");
            if (data.success && window.Livewire) {
                window.Livewire.dispatch("cartUpdated");
            }
        }

        // Call callback to reset loading state
        if (callback) callback();
    } catch (error) {
        window.showToast("Koneksi bermasalah atau Anda belum login", "error");
        if (callback) callback();
    }
};

/**
 * Fungsi Utama: Tambah Kelas ke Keranjang
 */
window.addToCartClass = async function (classId, callback) {
    try {
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]',
        )?.content;

        const response = await fetch(`/student/cart/add-class/${classId}`, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-Token": csrfToken || "",
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "include",
        });

        const data = await handleFetchResponse(response);

        if (data) {
            window.showToast(data.message, data.success ? "success" : "info");
            if (data.success && window.Livewire) {
                window.Livewire.dispatch("cartUpdated");
            }
        }

        // Call callback to reset loading state
        if (callback) callback();
    } catch (error) {
        window.showToast("Koneksi bermasalah atau Anda belum login", "error");
        if (callback) callback();
    }
};

/**
 * UI Component: Show Toast dengan Font yang Lebih Jelas & Besar
 */
window.showToast = function (message, type = "success", duration = 4000) {
    const container = document.getElementById("toast-container");
    if (!container) return;

    const toast = document.createElement("div");

    const styles = {
        success: {
            bg: "border-emerald-100 bg-white text-emerald-500",
            icon: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
        },
        error: {
            bg: "border-red-100 bg-white text-red-500",
            icon: "M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z",
        },
        info: {
            bg: "border-blue-100 bg-white text-blue-500",
            icon: "M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
        },
    };

    const currentStyle = styles[type] || styles.info;

    // AOS Attribute
    toast.setAttribute("data-aos", "fade-left");

    // Perubahan: Menggunakan text-sm (lebih besar dari xs) dan font-bold
    toast.className = `flex items-center p-5 min-w-[320px] border rounded-2xl shadow-2xl shadow-gray-200/60 ${currentStyle.bg}`;

    toast.innerHTML = `
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="${currentStyle.icon}" />
        </svg>
        <div class="ml-4 text-sm font-bold tracking-normal text-gray-700 leading-snug">${message}</div>
    `;

    container.appendChild(toast);

    if (window.AOS) {
        window.AOS.refresh();
    }

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateX(30px)";
        toast.style.transition = "all 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
        setTimeout(() => toast.remove(), 500);
    }, duration);
};

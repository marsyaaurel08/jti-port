import "./cart.js"; // ← Import cart functionality
import "./carousel.js"; // ← Import splide carousel

export function showToast(message, type = "success", duration = 4000) {
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

    if (window.AOS) window.AOS.refresh();

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
}

window.showToast = showToast;

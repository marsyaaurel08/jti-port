/**
 * Cart Module - Handle semua fungsi keranjang belanja untuk Course & Class
 * File ini mengelola penambahan item ke keranjang baik course maupun class
 * & cross-tab synchronization
 */

window.isAddingToCart = false;

/**
 * Handle response dari fetch API
 * @param {Response} response
 * @returns {Promise<Object|null>}
 */
async function handleFetchResponse(response) {
    if (response.status === 401) {
        window.showToast(
            "Silahkan login terlebih dahulu untuk menambah ke keranjang",
            "info",
        );
        return null;
    }

    if (response.redirected) {
        window.showToast("Akses ditolak. Silahkan login kembali.", "info");
        return null;
    }

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
 * Broadcast cart update ke semua tabs menggunakan localStorage
 * @param {number} count - Jumlah items di cart
 */
function broadcastCartUpdate(count) {
    // Simpan ke localStorage untuk cross-tab communication
    localStorage.setItem(
        "cart_updated",
        JSON.stringify({
            timestamp: Date.now(),
            count: count,
        }),
    );

    // Trigger storage event listener di tabs lain
    window.dispatchEvent(
        new StorageEvent("storage", {
            key: "cart_updated",
            newValue: JSON.stringify({
                timestamp: Date.now(),
                count: count,
            }),
        }),
    );
}

/**
 * Generic function untuk tambah ke keranjang
 * @param {string} endpoint - URL endpoint untuk tambah ke keranjang
 * @param {Function|null} callback - Callback function setelah selesai
 * @returns {Promise<void>}
 */
async function addToCartGeneric(endpoint, callback = null) {
    if (window.isAddingToCart) return;
    window.isAddingToCart = true;

    try {
        window.showToast("Menambahkan ke keranjang...", "info", 2500);

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]',
        )?.content;

        const response = await fetch(endpoint, {
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

            if (data.success) {
                // Dispatch event untuk tab/window ini
                window.dispatchEvent(
                    new CustomEvent("cart-updated", {
                        detail: { count: data.cart_count },
                    }),
                );

                // Broadcast ke tabs lain via localStorage
                broadcastCartUpdate(data.cart_count);

                // Trigger Livewire event
                if (window.Livewire) {
                    window.Livewire.dispatch("cartUpdated", {
                        count: data.cart_count,
                    });
                }
            }
        }
    } catch (error) {
        window.showToast("Koneksi bermasalah atau Anda belum login", "error");
        console.error("Add to cart error:", error);
    } finally {
        if (callback) callback();
        window.isAddingToCart = false;
    }
}

/**
 * Tambah Course ke keranjang
 * @param {number} courseId - ID dari course
 * @param {Function|null} callback - Callback function
 * @returns {Promise<void>}
 */
window.addToCart = async function (courseId, callback) {
    await addToCartGeneric(`/student/cart/add/${courseId}`, callback);
};

/**
 * Tambah Class ke keranjang
 * @param {number} classId - ID dari class
 * @param {Function|null} callback - Callback function
 * @returns {Promise<void>}
 */
window.addToCartClass = async function (classId, callback) {
    await addToCartGeneric(`/student/cart/add-class/${classId}`, callback);
};

/**
 * Initialize cross-tab cart synchronization
 * Listen ke perubahan di tabs lain via localStorage event
 */
function initCrossTabSync() {
    // Listen untuk storage changes dari tabs lain
    window.addEventListener("storage", (event) => {
        if (event.key === "cart_updated" && event.newValue) {
            try {
                const data = JSON.parse(event.newValue);

                // Dispatch event untuk re-load cart data
                window.dispatchEvent(
                    new CustomEvent("cart-updated-from-tab", {
                        detail: { count: data.count },
                    }),
                );

                // Trigger Livewire event untuk reload
                if (window.Livewire) {
                    window.Livewire.dispatch("refreshCart");
                }
            } catch (e) {
                console.error("Error parsing cart update:", e);
            }
        }
    });
}

/**
 * Initialize page visibility handler untuk reload cart saat tab aktif
 */
function initPageVisibilityHandler() {
    document.addEventListener("visibilitychange", () => {
        if (!document.hidden) {
            // Tab baru saja menjadi visible/active
            // Reload cart data untuk memastikan data terbaru
            if (window.Livewire) {
                window.Livewire.dispatch("refreshCart");
            }
        }
    });
}

// Auto-initialize saat DOM ready
document.addEventListener("DOMContentLoaded", () => {
    initCrossTabSync();
    initPageVisibilityHandler();
});

export { addToCartGeneric, handleFetchResponse, broadcastCartUpdate };

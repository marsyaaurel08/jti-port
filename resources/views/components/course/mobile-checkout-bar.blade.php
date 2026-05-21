@props(['course'])

{{-- Modern Full-Width Bottom Bar with Compact Buttons --}}
<div class="fixed bottom-0 left-0 right-0 lg:hidden z-50 bg-white/95 backdrop-blur-md border-t border-gray-100 pb-safe">
    <div class="w-full px-5 py-4 flex items-center justify-between gap-4">

        {{-- Price & Info --}}
        <div class="flex-shrink-0">
            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Harga</span>
            <span class="text-xl font-bold text-slate-900 tracking-tight">
                Rp{{ number_format($course->price, 0, ',', '.') }}
            </span>
            <p class="text-[10px] text-gray-500 font-medium leading-none mt-1">
                Sisa <span
                    class="text-slate-900 font-bold">{{ $course->max_capacity - $course->current_enrolled }}</span>
                slot
            </p>
        </div>

        {{-- Actions Group (Now align right for compact look) --}}
        <div class="flex items-center gap-2">
            {{-- Add to Cart Button (Secondary, Compact) --}}
            <button type="button" x-data="{ isLoading: false }"
                @click="addToCartClass({{ $course->id }}, () => { isLoading = false }); isLoading = true;"
                :disabled="isLoading" title="Tambah ke Keranjang"
                class="flex items-center justify-center p-3.5 bg-slate-50 text-slate-700 rounded-xl transition-all duration-300 active:scale-90 border border-slate-100 hover:bg-slate-100"
                aria-label="Tambah ke Keranjang">
                <svg x-show="!isLoading" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="8" cy="21" r="1" />
                    <circle cx="19" cy="21" r="1" />
                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                </svg>
                <!-- Loading Spinner -->
                <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </button>

            {{-- Checkout Button (Primary, Fixed Compact Width) --}}
            <a href="{{ auth()->check() ? route('student.checkout.course', $course->slug) : route('filament.user.auth.login') }}"
                class="inline-flex items-center justify-center py-3.5 px-6 bg-slate-900 text-white rounded-xl font-bold text-sm transition-all duration-300 active:scale-95 shadow-lg shadow-slate-200 whitespace-nowrap min-w-[140px]">
                Daftar Sekarang
            </a>
        </div>
    </div>
</div>

<style>
    /* Support for iPhone Notch */
    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom);
    }

    @media (max-width: 1024px) {
        main {
            padding-bottom: calc(96px + env(safe-area-inset-bottom));
        }
    }

    /* Smooth interaction */
    button,
    a {
        -webkit-tap-highlight-color: transparent;
    }
</style>

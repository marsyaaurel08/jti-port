@props(['course', 'is_user_enrolled' => false])

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
        <div class="flex items-center gap-2" x-data="{ isLoading: false }">
            @php
                $targetUrl = auth()->check()
                    ? ($is_user_enrolled
                        ? route('student.lesson', [
                            'courseSlug' => $course->slug,
                            'lessonSlug' => $course->chapters->first()->lessons->first()->slug ?? 'first-lesson',
                        ])
                        : route('student.checkout.course', $course->slug))
                    : route('filament.user.auth.login');
            @endphp

            @if (
                !$is_user_enrolled &&
                    (!auth()->check() ||
                        !auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar'])))
                {{-- Add to Cart Button (Secondary, Compact) --}}
                <button type="button"
                    @click="addToCartClass({{ $course->id }}, () => { isLoading = false }); isLoading = true;"
                    :disabled="isLoading" title="Tambah ke Keranjang"
                    class="flex items-center justify-center p-3.5 bg-slate-50 text-slate-700 rounded-xl transition-all duration-300 active:scale-90 border border-slate-100 hover:bg-slate-100"
                    aria-label="Tambah ke Keranjang">
                    <svg x-show="!isLoading" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
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
            @endif

            {{-- Primary Button --}}
            @if ($is_user_enrolled)
                {{-- Lanjutkan Belajar Button (Green Theme) --}}
                <a href="{{ $targetUrl }}"
                    class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-green-600 px-6 py-4 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:bg-green-700 hover:shadow-lg active:scale-[0.98]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Lanjutkan Belajar</span>
                </a>
            @else
                {{-- Checkout Button (Primary, Fixed Compact Width) --}}
                <button type="button"
                    onclick="const modal = document.querySelector('[data-pending-order]'); if (modal) { const evt = new Event('show-pending-modal'); window.dispatchEvent(evt); } else { window.location.href = '{{ $targetUrl }}'; }"
                    class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-jti-yellow px-6 py-4 text-sm font-semibold text-jti-primary shadow-sm transition-all duration-200 hover:bg-amber-400 hover:shadow-md active:scale-[0.98]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="capitalize">Checkout Sekarang</span>
                </button>
            @endif
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

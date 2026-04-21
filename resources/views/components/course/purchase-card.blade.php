@props(['course', 'is_user_enrolled' => false])
{{-- @dd($course) --}}

{{-- Container Card: Menggunakan gaya panel gelap yang solid --}}
<div class="overflow-hidden rounded-sm border bg-[#0a0f1d] shadow-2xl backdrop-blur-md">

    {{-- Video Preview Section --}}
    <div class="relative group cursor-pointer overflow-hidden aspect-video">
        <img src="{{ $course->thumbnail_url }}"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Thumbnail">

        {{-- Glass Overlay --}}
        <div
            class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
            <div
                class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center border border-white/20 group-hover:scale-110 group-hover:bg-jti-yellow group-hover:text-jti-primary transition-all duration-300">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="p-6">
        {{-- Price Section --}}
        <div class="flex items-end justify-between mb-8">
            <div class="flex flex-col gap-1">
                @if ($course->old_price)
                    <span class="text-xs font-medium text-gray-500 line-through leading-none">
                        Rp{{ number_format($course->old_price, 0, ',', '.') }}
                    </span>
                @endif
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold tracking-tighter text-white">
                        Rp{{ number_format($course->price, 0, ',', '.') }}
                    </span>
                    @if ($course->old_price)
                        <span
                            class="px-2 py-0.5 bg-jti-yellow/10 border border-jti-yellow/20 text-jti-yellow text-[10px] font-bold rounded">
                            85% OFF
                        </span>
                    @endif
                </div>
            </div>

            <button title="Bagikan"
                class="p-2.5 text-gray-500 hover:text-jti-yellow bg-white/5 hover:bg-white/10 rounded-xl border border-white/5 transition-all">
                <i data-lucide="share-2" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col gap-3 mb-8 capitalize" x-data="{ isLoading: false }">
            @php
                // Menyiapkan URL agar logika di dalam atribut href tidak terlalu panjang
                $targetUrl = auth()->check()
                    ? ($is_user_enrolled
                        ? route('student.lesson', [
                            'courseSlug' => $course->slug,
                            'lessonSlug' => $course->chapters->first()->lessons->first()->slug ?? 'first-lesson',
                        ])
                        : route('student.checkout.course', $course->slug))
                    : route('filament.user.auth.login');
            @endphp

            <div class="w-full">
                @if ($is_user_enrolled)
                    {{-- Tombol Lanjutkan Belajar (Green Theme) --}}
                    <a href="{{ $targetUrl }}"
                        class="group relative flex w-full items-center justify-center gap-2 overflow-hidden border border-white/10 rounded-xl bg-green-600 px-6 py-4 text-sm font-bold text-white shadow-md transition-all duration-200 hover:bg-green-700 hover:shadow-lg active:scale-[0.98]">
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
                    {{-- Tombol Checkout (Yellow Theme) --}}
                    <a href="{{ $targetUrl }}"
                        class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-jti-yellow px-6 py-4 text-sm font-semibold text-jti-primary shadow-sm transition-all duration-200 hover:bg-amber-400 hover:shadow-md active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="capitalize">Checkout Sekarang</span>
                    </a>
                @endif
            </div>
            @if (
                !auth()->check() ||
                    !auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar']))
                <button type="button"
                    @click="addToCart({{ $course->id }}, () => { isLoading = false; }); isLoading = true;"
                    :disabled="isLoading"
                    class="w-full py-4 bg-white/5 border border-white/10 text-gray-300 font-semibold rounded-xl hover:bg-white/10 transition-all text-sm disabled:opacity-50 flex items-center justify-center gap-2 group">

                    <i data-lucide="shopping-cart" x-show="!isLoading"
                        class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors"></i>

                    {{-- Loading Spinner --}}
                    <svg x-show="isLoading" x-cloak class="w-4 h-4 animate-spin text-jti-primary" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <span>Tambah ke Keranjang</span>
                </button>
            @endif
        </div>

        {{-- Features List --}}
        <div class="border-t border-white/5 pt-6">
            <h5 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-4">Termasuk di dalamnya:</h5>
            <ul class="space-y-4">
                <li class="flex items-center gap-3 text-sm text-gray-400 group">
                    <i data-lucide="play-circle"
                        class="w-4 h-4 text-gray-600 group-hover:text-jti-yellow transition-colors"></i>
                    <span>{{ $course->total_duration }} Durasi Video</span>
                </li>
                <li class="flex items-center gap-3 text-sm text-gray-400 group">
                    <i data-lucide="book-open"
                        class="w-4 h-4 text-gray-600 group-hover:text-jti-yellow transition-colors"></i>
                    <span>{{ $course->chapters_count }} Bab Pembelajaran</span>
                </li>
                <li class="flex items-center gap-3 text-sm text-gray-300 group">
                    <div class="w-4 h-4 flex items-center justify-center">
                        <div
                            class="w-1.5 h-1.5 rounded-full bg-jti-yellow shadow-[0_0_8px_rgba(252,211,77,0.6)] animate-pulse">
                        </div>
                    </div>
                    <span class="font-medium">Akses seumur hidup</span>
                </li>
            </ul>
        </div>
    </div>
</div>

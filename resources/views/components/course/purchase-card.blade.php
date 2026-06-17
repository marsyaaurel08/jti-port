@props(['course', 'is_user_enrolled' => false])
{{-- @dd($course) --}}

{{-- Container Card: Menggunakan gaya panel gelap yang solid --}}
<div class="overflow-hidden rounded-sm border bg-[#0a0f1d] shadow-2xl backdrop-blur-md">

    {{-- Video Preview Section --}}
    <div class="relative group cursor-pointer overflow-hidden aspect-video">
        <img src="{{ asset('storage/' . $course->thumbnail_url) }}"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Thumbnail">

        {{-- Glass Overlay --}}
        <div
            class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
        </div>
    </div>

    <div class="p-6">
        {{-- Price Section --}}
        <div class="flex items-end justify-between mb-8">
            <div class="flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500">Harga Kelas</span>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold tracking-tighter text-white">
                        @if ($course->price == 0)
                            Gratis
                        @else
                            Rp{{ number_format($course->price, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col gap-3 mb-8 capitalize" x-data="{ isLoading: false }">
            @php
                // Menyiapkan URL agar logika di dalam atribut href tidak terlalu panjang
                $targetUrl = auth()->check() && auth()->user()->hasVerifiedEmail()
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
                        <x-lucide-play-circle class="w-4 h-4" />
                        <span>Lanjutkan Belajar</span>
                    </a>
                @else
                    {{-- Tombol Checkout (Yellow Theme) --}}
                    <button type="button"
                        onclick="const modal = document.querySelector('[data-pending-order]'); if (modal) { const evt = new Event('show-pending-modal'); window.dispatchEvent(evt); } else { window.location.href = '{{ $targetUrl }}'; }"
                        class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-jti-yellow px-6 py-4 text-sm font-semibold text-jti-primary shadow-sm transition-all duration-200 hover:bg-amber-400 hover:shadow-md active:scale-[0.98]">
                        <x-lucide-handbag class="w-5 h-5" />
                        <span class="capitalize">Checkout Sekarang</span>
                    </button>
                @endif
            </div>
            @if (
                !$is_user_enrolled &&
                    (!auth()->check() ||
                        !auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar'])))
                <button type="button"
                    @click="addToCart({{ $course->id }}, () => { isLoading = false; }); isLoading = true;"
                    :disabled="isLoading"
                    class="w-full py-4 bg-white/5 border border-white/10 text-gray-300 font-semibold rounded-xl hover:bg-white/10 transition-all text-sm disabled:opacity-50 flex items-center justify-center gap-2 group">

                    <x-lucide-shopping-cart x-show="!isLoading"
                        class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors"></x-lucide-shopping-cart>

                    {{-- Loading Spinner --}}
                    <x-lucide-loader-2 x-show="isLoading" x-cloak class="w-4 h-4 animate-spin text-jti-yellow" />
                    <span>Tambah ke Keranjang</span>
                </button>
            @endif
        </div>

        {{-- Features List --}}
        <div class="border-t border-white/5 pt-6">
            <h5 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-4">Termasuk di dalamnya:</h5>
            <ul class="space-y-4">
                <li class="flex items-center gap-3 text-sm text-gray-400 group">
                    <x-lucide-play-circle class="w-4 h-4 text-gray-600 group-hover:text-jti-yellow transition-colors" />
                    <span>{{ $course->total_duration }} Durasi Video</span>
                </li>
                <li class="flex items-center gap-3 text-sm text-gray-400 group">
                    <x-lucide-book-open class="w-4 h-4 text-gray-600 group-hover:text-jti-yellow transition-colors" />
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

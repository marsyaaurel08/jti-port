@props(['class', 'is_user_enrolled' => false])

{{-- Container Card: Menggunakan gaya panel gelap yang solid --}}
<div class="overflow-hidden rounded-sm border bg-[#0a0f1d] shadow-2xl backdrop-blur-md">

    {{-- Thumbnail Preview Section --}}
    <div class="relative group cursor-pointer overflow-hidden aspect-video">
        <img src="{{ asset('storage/' . $class->thumbnail_url) }}"
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
                        @if ($class->price == 0)
                            Gratis
                        @else
                            Rp{{ number_format($class->price, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Kapasitas & Enrolled Info --}}
        <div class="mb-8 space-y-3 pb-8 border-b border-white/10">
            {{-- Kapasitas Kelas --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-jti-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10h.01M11 10h.01M7 10h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Kapasitas Kelas
                </span>
                <span class="text-sm text-white font-semibold">
                    {{ $class->current_enrolled }}/{{ $class->max_capacity }}
                </span>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-jti-yellow to-jti-primary h-full rounded-full transition-all"
                    style="width: {{ ($class->current_enrolled / $class->max_capacity) * 100 }}%">
                </div>
            </div>

            {{-- Remaining Seats --}}
            <p class="text-xs text-gray-500">
                {{ $class->max_capacity - $class->current_enrolled }} kursi tersisa
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col gap-3 mb-8" x-data="{ isLoading: false }">
            @php
                // Menyiapkan URL agar logika di dalam atribut href tidak terlalu panjang
                $targetUrl =
                    auth()->check() && auth()->user()->hasVerifiedEmail()
                        ? ($is_user_enrolled
                            ? route('student.classLesson', [
                                'classSlug' => $class->course->slug,
                                'lessonSlug' =>
                                    $class->course->chapters->first()->lessons->first()->slug ?? 'first-lesson',
                            ])
                            : route('student.checkout.class', $class->slug))
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
            @if (
                !$is_user_enrolled &&
                    (!auth()->check() ||
                        !auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar'])))
                <button type="button"
                    @click="addToCartClass({{ $class->id }}, () => { isLoading = false; }); isLoading = true;"
                    :disabled="isLoading"
                    class="w-full py-4 bg-white/5 border border-white/10 text-gray-300 font-semibold rounded-xl hover:bg-white/10 transition-all text-sm disabled:opacity-50 flex items-center justify-center gap-2 group">
                    <x-lucide-shopping-cart class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors"
                        x-show="!isLoading" />

                    {{-- Loading Spinner --}}
                    <svg x-show="isLoading" x-cloak class="w-4 h-4 animate-spin text-jti-primary" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <span>tambah ke keranjang</span>
                </button>
            @endif
        </div>

        {{-- Yang Akan Anda Dapatkan Section --}}
        <section class="mb-8">
            <p class="text-sm font-bold text-gray-300 mb-4">Yang Akan Anda Dapatkan</p>

            <ul class="space-y-3">
                {{-- Akses Materi --}}
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-jti-yellow flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-gray-400">Akses Penuh ke Semua Materi Kelas</span>
                </li>

                {{-- Video Pembelajaran --}}
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-jti-yellow flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-gray-400">{{ $class->course->total_lessons ?? 'Puluhan' }} Video
                        Pembelajaran</span>
                </li>

                {{-- Link Meeting Live --}}
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-jti-yellow flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-gray-400">Link Meeting Langsung dengan Instruktur</span>
                </li>

                {{-- Sertifikat --}}
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-jti-yellow flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-gray-400">Sertifikat Setelah Menyelesaikan Kelas</span>
                </li>

                {{-- Dukungan Instruktur --}}
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-jti-yellow flex-shrink-0 mt-0.5" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-gray-400">Dukungan Penuh dari Instruktur</span>
                </li>
            </ul>
        </section>

        {{-- Status Kelas --}}
        <div class="p-4 rounded-lg bg-white/5 border border-white/10 text-center">
            @if ($class->is_active)
                @if ($class->start_date->isFuture())
                    <p class="text-xs font-bold text-amber-400 uppercase tracking-wider">
                        ⏰ Kelas dimulai {{ $class->start_date->diffForHumans() }}
                    </p>
                @elseif ($class->end_date->isFuture())
                    <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">
                        ✓ Kelas sedang berlangsung
                    </p>
                @else
                    <p class="text-xs font-bold text-red-400 uppercase tracking-wider">
                        ✗ Kelas telah selesai
                    </p>
                @endif
            @else
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                    Kelas tidak aktif
                </p>
            @endif
        </div>
    </div>
</div>

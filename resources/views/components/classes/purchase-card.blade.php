@props(['class'])

{{-- Container Card: Menggunakan gaya panel gelap yang solid --}}
<div class="overflow-hidden rounded-sm border bg-[#0a0f1d] shadow-2xl backdrop-blur-md">

    {{-- Thumbnail Preview Section --}}
    <div class="relative group cursor-pointer overflow-hidden aspect-video">
        <img src="{{ $class->thumbnail_url }}"
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
                <span class="text-xs font-medium text-gray-500">Harga Kelas</span>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold tracking-tighter text-white">
                        Rp{{ number_format($class->price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <button title="Bagikan"
                class="p-2.5 text-gray-500 hover:text-jti-yellow bg-white/5 hover:bg-white/10 rounded-xl border border-white/5 transition-all">
                <i data-lucide="share-2" class="w-4 h-4"></i>
            </button>
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
            {{-- Checkout Link --}}
            <a href="{{ auth()->check() ? route('student.checkout.class', $class->slug) : route('filament.user.auth.login') }}"
                class="w-full py-3.5 bg-jti-yellow text-jti-primary font-semibold rounded-xl hover:bg-amber-400 transition-all text-sm flex items-center justify-center active:scale-[0.98] shadow-sm">
                daftar sekarang
            </a>

            @if (
                !auth()->check() ||
                    !auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar']))
                <button type="button"
                    @click="addToCartClass({{ $class->id }}, () => { isLoading = false; }); isLoading = true;"
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
                    <svg class="w-5 h-5 text-jti-yellow flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
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

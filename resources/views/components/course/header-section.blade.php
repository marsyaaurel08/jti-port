@props([
    'total' => 0,
    'selectedCategories' => [],
    'selectedRatings' => [],
    'selectedLevels' => [],
    'isFree' => false,
    'isPaid' => false,
])

@if (!empty($selectedRatings) || !empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories))
    {{-- Header Container --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-10 gap-6 pb-8 border-b border-slate-50">

        {{-- Left Side: Info & Filter Status --}}
        <div class="space-y-5">
            <div class="flex items-center gap-5">
                {{-- Decorative Icon - Floating Style --}}
                <div
                    class="hidden sm:flex w-14 h-14 items-center justify-center bg-jti-yellow/10 rounded-[1.5rem] border border-jti-yellow/10 shadow-sm text-jti-primary">
                    <i data-lucide="book-open" class="w-6 h-6"></i>
                </div>

                <div class="space-y-1">
                    <h1 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight">
                        {{ number_format($total, 0, ',', '.') }}
                        <span class="text-slate-400 font-medium text-lg md:text-xl ml-1">Kursus ditemukan</span>
                    </h1>
                    <p class="text-xs text-slate-500 font-medium flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Menampilkan modul sertifikasi terbaik sesuai preferensi anda
                    </p>
                </div>
            </div>

            {{-- Active Filter Badges --}}
            @if (!empty($selectedRatings) || !empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories))
                <div class="flex flex-wrap items-center gap-2.5 pt-1 ">
                    {{-- Reset Button: No Uppercase, Soft Red --}}
                    <button wire:click="resetFilters"
                        class="group flex items-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-all duration-300 border border-rose-100/50 active:scale-95 capitalize">
                        <i data-lucide="x" class="w-3.5 h-3.5 transition-transform group-hover:rotate-90"></i>
                        <span class="text-xs font-semibold">reset filter</span>
                    </button>

                    {{-- Visual Indicator for active filters --}}
                    @if ($isFree)
                        <span
                            class="px-4 py-2 bg-slate-50 text-slate-600 text-[11px] font-semibold rounded-xl border border-slate-100">Gratis</span>
                    @endif
                    @if ($isPaid)
                        <span
                            class="px-4 py-2 bg-slate-50 text-slate-600 text-[11px] font-semibold rounded-xl border border-slate-100">Berbayar</span>
                    @endif

                    {{-- Level Indicators --}}
                    @foreach ($selectedLevels as $level)
                        <span
                            class="px-4 py-2 bg-jti-yellow/10 text-jti-primary text-[11px] font-semibold rounded-xl border border-jti-yellow/20 capitalize">
                            {{ strtolower($level) }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

@props([
    'total' => 0,
    'selectedCategories' => [],
    'selectedLevels' => [],
    'isFree' => false,
    'isPaid' => false,
])

@if (!empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories))
    {{-- Header Container --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-10 gap-6 pb-8 border-b border-slate-50">

        {{-- Left Side: Info & Filter Status --}}
        <div class="space-y-5">
            <div class="flex items-center gap-5">
                {{-- Decorative Icon - Glassmorphism style --}}
                <div
                    class="hidden sm:flex w-14 h-14 items-center justify-center bg-jti-yellow/10 rounded-[1.5rem] border border-jti-yellow/10 shadow-sm text-jti-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-6 h-6">
                        <path d="M12 7v14" />
                        <path
                            d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                    </svg>
                </div>

                <div class="space-y-1">
                    <h1 class="text-2xl md:text-3xl font-semibold text-slate-800 tracking-tight">
                        {{ number_format($total, 0, ',', '.') }}
                        <span class="text-slate-400 font-medium text-lg md:text-xl ml-1">kelas tersedia</span>
                    </h1>
                    <p class="text-xs text-slate-500 font-medium flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        mengeksplorasi materi pembelajaran terbaik untuk anda
                    </p>
                </div>
            </div>

            {{-- Active Filter Badges --}}
            @if (!empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories))
                <div class="flex flex-wrap items-center gap-2.5 pt-1">
                    {{-- Reset Button --}}
                    <button wire:click="resetFilters"
                        class="group flex items-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-all duration-300 border border-rose-100/50 active:scale-95 shadow-sm capitalize">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="w-3.5 h-3.5 transition-transform group-hover:rotate-90">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                        <span class="text-xs font-semibold">reset filter</span>
                    </button>

                    {{-- Visual Indicator for active filters --}}
                    @if ($isFree)
                        <span
                            class="px-4 py-2 bg-slate-50 text-slate-600 text-[11px] font-semibold rounded-xl border border-slate-100 shadow-sm">Gratis</span>
                    @endif
                    @if ($isPaid)
                        <span
                            class="px-4 py-2 bg-slate-50 text-slate-600 text-[11px] font-semibold rounded-xl border border-slate-100 shadow-sm">Berbayar</span>
                    @endif

                    {{-- Contoh untuk Categories atau Levels yang terpilih --}}
                    @foreach ($selectedLevels as $level)
                        <span
                            class="px-4 py-2 bg-jti-yellow/10 text-jti-primary text-[11px] font-semibold rounded-xl border border-jti-yellow/20 shadow-sm capitalize">
                            {{ strtolower($level) }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

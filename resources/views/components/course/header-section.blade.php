@props([
    'total' => 0,
    'selectedCategories' => [],
    'selectedRatings' => [],
    'selectedLevels' => [],
    'isFree' => false,
    'isPaid' => false,
    'sort' => 'latest',
])

{{-- Header Container --}}
<div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-6 pb-8 border-b border-gray-100/80">

    {{-- Left Side: Info & Filter Status --}}
    <div class="space-y-4">
        <div class="flex items-center gap-4">
            {{-- Decorative Icon - Glassmorphism style --}}
            <div class="hidden sm:flex w-12 h-12 items-center justify-center bg-jti-primary/10 rounded-2xl border border-jti-primary/10 shadow-sm">
                <svg class="w-6 h-6 text-jti-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.246 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>

            <div class="space-y-1">
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
                    {{ number_format($total, 0, ',', '.') }}
                    <span class="text-gray-400 font-medium text-lg md:text-xl ml-1">Kursus Tersedia</span>
                </h1>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Mengeksplorasi modul sertifikasi terbaik untuk Anda
                </p>
            </div>
        </div>

        {{-- Active Filter Badges --}}
        @if (!empty($selectedRatings) || !empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories))
            <div class="flex flex-wrap items-center gap-2 pt-2">
                {{-- Reset Button --}}
                <button wire:click="resetFilters"
                    class="group flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-all duration-300 border border-red-100 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider">Reset Semua</span>
                </button>

                {{-- Visual Indicator for active filters (Contoh) --}}
                @if($isFree)
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-600 text-xs font-medium rounded-lg border border-gray-200">Gratis</span>
                @endif
                @if($isPaid)
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-600 text-xs font-medium rounded-lg border border-gray-200">Berbayar</span>
                @endif
            </div>
        @endif
    </div>

    {{-- Right Side: Sorting --}}
    <div class="flex flex-col sm:flex-row items-start lg:items-center gap-4">
        <div class="flex flex-col gap-1">
            <div class="relative min-w-[200px]">
                <x-course.sort-dropdown :sort="$sort" />
            </div>
        </div>
    </div>
</div>

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
<div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6 pb-6 border-b border-gray-100">

    {{-- Left Side: Info & Filter Status --}}
    <div class="space-y-2">
        <div class="flex items-center gap-3">
            {{-- Icon Dekoratif agar lebih modern --}}
            <div class="hidden sm:flex w-10 h-10 items-center justify-center bg-jti-primary/5 rounded-xl">
                <svg class="w-5 h-5 text-jti-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>

            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800 tracking-tight">
                    {{ number_format($total, 0, ',', '.') }} <span class="text-gray-400 font-medium">Kursus Ditemukan</span>
                </h1>
                <p class="text-xs text-gray-500 font-medium">Menampilkan materi terbaik untuk "Semua Kursus"</p>
            </div>
        </div>

        {{-- Active Filter Badges / Reset Button --}}
        @if (!empty($selectedRatings) || !empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories))
            <div class="flex items-center gap-3 pt-1">
                <button wire:click="resetFilters"
                    class="group flex items-center gap-2 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-jti-orange rounded-full transition-all">
                    <svg class="w-3 h-3 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Hapus Filter</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Right Side: Sorting Component --}}
    <div class="flex flex-col items-start md:items-end gap-2">
        <x-course.sort-dropdown :sort="$sort" />
    </div>
</div>

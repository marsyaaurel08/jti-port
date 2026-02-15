@props([
    'total' => 0,
    'selectedCategories' => [],
    'selectedRatings' => [],
    'selectedLevels' => [],
    'isFree' => false,
    'isPaid' => false,
    'sort' => 'latest',
])

{{-- Header dengan Title, Total Results, dan Sorting --}}
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-black text-jti-primary">
            {{ $total }} hasil untuk "Semua Kursus"
        </h1>
        {{-- Tombol Reset Filter: Hanya muncul jika ada filter aktif --}}
        @if (!empty($selectedRatings) || !empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories))
            <button wire:click="resetFilters"
                class="text-left text-xs font-bold text-jti-orange hover:underline uppercase tracking-widest">
                Hapus Semua Filter
            </button>
        @endif
    </div>

    <x-course.sort-dropdown :sort="$sort" />
</div>

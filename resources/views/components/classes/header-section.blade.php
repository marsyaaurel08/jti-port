@props(['total' => 0, 'selectedCategories' => [], 'selectedLevels' => [], 'isFree' => false, 'isPaid' => false, 'sort' => 'latest'])

{{-- Header dengan Title, Total Results, dan Sorting --}}
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-black text-jti-primary uppercase tracking-tight">
            {{ $total }} Kelas Tersedia
        </h1>
        {{-- Tombol Reset Filter: Hanya muncul jika ada filter aktif --}}
        @if (!empty($selectedLevels) || !empty($selectedCategories) || $isFree || $isPaid)
            <button wire:click="resetFilters"
                class="text-left text-[10px] font-black text-jti-orange hover:underline uppercase tracking-widest">
                Hapus Semua Filter
            </button>
        @endif
    </div>

    <x-classes.sort-dropdown :sort="$sort" />
</div>

{{-- Header: Title dan Reset Filter --}}
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-black text-jti-primary uppercase tracking-tight">
            {{ $totalClasses }} Kelas Tersedia
        </h1>

        {{-- Tombol Reset Filter: Hanya muncul jika ada filter aktif --}}
        @if ($hasActiveFilters)
            <button wire:click="resetFilters"
                class="text-left text-[10px] font-black text-jti-orange hover:underline uppercase tracking-widest">
                Hapus Semua Filter
            </button>
        @endif
    </div>

    <div class="w-full flex items-center justify-between gap-4 bg-gray-50/50 p-2 rounded-xl border border-gray-100/50 ml-auto"
        x-data="{}">
        {{-- Button Filter Mobile --}}
        <button @click="$dispatch('toggle-filter')"
            class="md:hidden inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-jti-primary to-jti-primary/90 text-white font-bold shadow-lg hover:shadow-xl hover:scale-105 rounded-xl transition-all active:scale-95 duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M3 4.5h18m-18 5h18m-18 5h18m-18 5h18" />
            </svg>
            <span class="text-sm font-semibold">Filter</span>
        </button>

        {{-- Sorting Dropdown --}}
        <x-classes.sort-dropdown :currentSort="$currentSort" />
    </div>
</div>

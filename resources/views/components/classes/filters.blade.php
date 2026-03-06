@props([
    'categories' => null,
    'selectedCategories' => [],
    'selectedLevels' => [],
    'isFree' => false,
    'isPaid' => false,
])

<aside class="w-full md:w-72 shrink-0 space-y-4" wire:key="sidebar-container">

    {{-- Header Sidebar (Optional: untuk Reset All) --}}
    <div class="flex items-center justify-between px-1 mb-6">
        <h2 class="text-lg font-bold text-slate-800">Filter</h2>
        <button wire:click="resetFilters" class="text-xs font-semibold text-jti-primary hover:underline">
            Reset Semua
        </button>
    </div>

    {{-- 1. Group Kategori --}}
    <div class="bg-white border border-slate-200 rounded-md p-5 shadow-sm overflow-hidden"
        wire:key="filter-group-categories">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Kategori</h3>
        <div class="space-y-3 max-h-52 overflow-y-auto pr-2 custom-scrollbar">
            @foreach ($categories ?? [] as $category)
                <label wire:key="cat-{{ $category->id }}" class="flex items-center group cursor-pointer">
                    <div class="relative flex items-center">
                        <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}"
                            class="w-4.5 h-4.5 rounded border-slate-300 text-jti-primary focus:ring-jti-primary/20 transition-all cursor-pointer">
                    </div>
                    <span class="ml-3 text-sm text-slate-600 group-hover:text-slate-900 transition-colors">
                        {{ $category->name }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- 2. Group Level & 3. Group Harga (Digabung dalam satu Card untuk efisiensi) --}}
    <div class="bg-white border border-slate-200 rounded-md divide-y divide-slate-100 shadow-sm">
        {{-- Section Level --}}
        <div class="p-5" wire:key="filter-group-levels">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Level</h3>
            <div class="space-y-3">
                @foreach (['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $value => $label)
                    <label wire:key="level-{{ $value }}" class="flex items-center group cursor-pointer">
                        <input type="checkbox" wire:model.live="selectedLevels" value="{{ $value }}"
                            class="w-4.5 h-4.5 rounded border-slate-300 text-jti-primary focus:ring-jti-primary/20 transition-all cursor-pointer">
                        <span
                            class="ml-3 text-sm text-slate-600 group-hover:text-slate-900 transition-colors">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Section Harga --}}
        <div class="p-5 bg-slate-50/50" wire:key="filter-group-price">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Tipe Akses</h3>
            <div class="grid grid-cols-2 gap-2">
                <label class="cursor-pointer">
                    <input type="checkbox" wire:model.live="isFree" class="hidden peer">
                    <div
                        class="px-3 py-2 text-center text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-500 peer-checked:border-jti-primary peer-checked:bg-jti-primary peer-checked:text-white transition-all">
                        Gratis
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="checkbox" wire:model.live="isPaid" class="hidden peer">
                    <div
                        class="px-3 py-2 text-center text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-500 peer-checked:border-jti-primary peer-checked:bg-jti-primary peer-checked:text-white transition-all">
                        Berbayar
                    </div>
                </label>
            </div>
        </div>
    </div>

</aside>

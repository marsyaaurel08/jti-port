@props([
    'categories' => null,
    'selectedCategories' => [],
    'selectedRatings' => [],
    'selectedLevels' => [],
    'isFree' => false,
    'isPaid' => false,
    'sort' => 'latest',
])

<aside class="w-full shrink-0 space-y-4" wire:key="sidebar-container">

    {{-- Header Sidebar (Optional: untuk Reset All) --}}
    <div class="flex items-center justify-between px-1 mb-6">
        <h2 class="text-lg font-bold text-slate-800">Filter</h2>
        <x-course.sort-dropdown :sort="$sort" />
    </div>

    {{-- 1. Group Tipe Akses (Paling Atas) --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm" wire:key="filter-group-price">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Tipe Akses</h3>
        <div class="space-y-3">
            <label class="flex items-center group cursor-pointer">
                <input type="checkbox" wire:model.live="isFree"
                    class="w-4.5 h-4.5 rounded border-slate-300 text-jti-primary focus:ring-jti-primary/20 transition-all cursor-pointer">
                <span class="ml-3 text-sm text-slate-600 group-hover:text-slate-900 transition-colors">Gratis</span>
            </label>
            <label class="flex items-center group cursor-pointer">
                <input type="checkbox" wire:model.live="isPaid"
                    class="w-4.5 h-4.5 rounded border-slate-300 text-jti-primary focus:ring-jti-primary/20 transition-all cursor-pointer">
                <span class="ml-3 text-sm text-slate-600 group-hover:text-slate-900 transition-colors">Berbayar</span>
            </label>
        </div>
    </div>

    {{-- 2. Group Kategori --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm overflow-hidden"
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

    {{-- 3. Group Rating (Radio Button) --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm" wire:key="filter-group-ratings">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Rating Minimal</h3>
        <div class="space-y-3">
            @for ($i = 4; $i >= 1; $i--)
                <label wire:key="rating-{{ $i }}" class="flex items-center group cursor-pointer">
                    <input type="radio" name="selectedRatings" {{-- ← TAMBAHKAN INI --}} wire:model.live="selectedRatings"
                        value="{{ $i }}"
                        class="w-4.5 h-4.5 rounded-full border-slate-300 text-jti-primary focus:ring-jti-primary/20 transition-all cursor-pointer">
                    <div class="ml-3 flex items-center gap-2">
                        <x-course.stars :rating="$i" />
                    </div>
                </label>
            @endfor
        </div>
    </div>

    {{-- 4. Group Level --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm" wire:key="filter-group-levels">
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
</aside>

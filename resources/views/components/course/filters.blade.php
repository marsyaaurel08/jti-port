@props([
    'categories' => null,
    'selectedCategories' => [],
    'selectedRatings' => [],
    'selectedLevels' => [],
    'isFree' => false,
    'isPaid' => false,
])

<aside class="w-full md:w-64 shrink-0 space-y-8" wire:key="sidebar-container">

    {{-- Filter Kategori --}}
    <div wire:key="filter-group-categories">
        <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
            Kategori
        </h3>
        <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
            @foreach ($categories ?? [] as $category)
                <label
                    wire:key="cat-{{ $category->id }}-{{ in_array($category->id, $selectedCategories) ? 'on' : 'off' }}"
                    class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}"
                        class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange transition-all cursor-pointer">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">
                        {{ $category->name }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Filter Rating --}}
    <div wire:key="filter-group-ratings">
        <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
            Rating
        </h3>
        <div class="space-y-3">
            @for ($i = 4; $i >= 1; $i--)
                <label
                    wire:key="rating-{{ $i }}-{{ in_array((string) $i, $selectedRatings) ? 'on' : 'off' }}"
                    class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="selectedRatings" value="{{ $i }}"
                        class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange transition-all cursor-pointer">
                    <div class="flex gap-0.5">
                        <x-course.stars :rating="$i" />
                    </div>
                    <span class="text-xs font-bold text-gray-600 group-hover:text-jti-primary transition-colors">
                        {{ $i }}.0 & Keatas
                    </span>
                </label>
            @endfor
        </div>
    </div>

    {{-- Filter Level Kesulitan --}}
    <div wire:key="filter-group-levels">
        <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
            Level Kesulitan
        </h3>
        <div class="space-y-3">
            @php
                $levels = [
                    'beginner' => 'Beginner',
                    'intermediate' => 'Intermediate',
                    'advanced' => 'Advanced',
                ];
            @endphp
            @foreach ($levels as $value => $label)
                <label wire:key="level-{{ $value }}-{{ in_array($value, $selectedLevels) ? 'on' : 'off' }}"
                    class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="selectedLevels" value="{{ $value }}"
                        class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange transition-all cursor-pointer">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">
                        {{ $label }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Filter Harga --}}
    <div wire:key="filter-group-price">
        <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
            Harga
        </h3>
        <div class="space-y-3">
            <label wire:key="price-free-{{ $isFree ? 'on' : 'off' }}"
                class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" wire:model.live="isFree"
                    class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange transition-all cursor-pointer">
                <span
                    class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">Gratis</span>
            </label>

            <label wire:key="price-paid-{{ $isPaid ? 'on' : 'off' }}"
                class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" wire:model.live="isPaid"
                    class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange transition-all cursor-pointer">
                <span
                    class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">Berbayar</span>
            </label>
        </div>
    </div>

</aside>

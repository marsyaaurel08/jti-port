@props(['categories' => null, 'selectedCategories' => [], 'selectedLevels' => []])

<aside class="w-full md:w-64 shrink-0 space-y-8" wire:key="sidebar-container">
    {{-- Filter Kategori/Bidang Studi --}}
    <div>
        <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">Bidang Studi</h3>
        <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
            @foreach($categories ?? [] as $category)
                <label wire:key="cat-{{ $category->id }}-{{ in_array($category->id, $selectedCategories) ? 'on' : 'off' }}" class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}" class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange cursor-pointer">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Filter Level --}}
    <div>
        <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">Level</h3>
        <div class="space-y-3">
            @php
                $levels = [
                    'beginner' => 'Beginner',
                    'intermediate' => 'Intermediate',
                    'advanced' => 'Advanced'
                ];
            @endphp
            @foreach ($levels as $value => $label)
                <label wire:key="level-{{ $value }}-{{ in_array($value, $selectedLevels) ? 'on' : 'off' }}" class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="selectedLevels" value="{{ $value }}" class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange cursor-pointer">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>
</aside>

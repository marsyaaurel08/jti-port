{{-- Sidebar Filter --}}
{{-- Overlay Backdrop (Mobile) --}}
<div x-show="showMobileFilter" @click.self="showMobileFilter = false" @toggle-filter="showMobileFilter = !showMobileFilter"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="md:hidden fixed inset-0 bg-black/40 z-30 backdrop-blur-sm"></div>

{{-- Filter Drawer (Mobile) --}}
<aside x-show="showMobileFilter" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="-translate-x-full opacity-0"
    class="md:hidden fixed left-0 top-0 h-screen w-80 bg-white z-40 overflow-y-auto shadow-2xl pt-8 pb-6">

    {{-- Close Button --}}
    <button @click="showMobileFilter = false"
        class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    {{-- Filter Title --}}
    <h2 class="text-xl font-black text-jti-primary px-6 mb-6">Filter Pencarian</h2>

    {{-- Mobile Filter Content --}}
    <div class="space-y-6 px-6">
        {{-- Bidang Studi / Kategori --}}
        <div wire:key="filter-group-categories">
            <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
                Bidang Studi</h3>
            <div class="space-y-3 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                @foreach ($categories as $category)
                    <label
                        wire:key="cat-{{ $category->id }}-{{ in_array($category->id, $selectedCategories) ? 'on' : 'off' }}"
                        class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}"
                            class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange cursor-pointer">
                        <span
                            class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Level Kesulitan --}}
        <div wire:key="filter-group-levels">
            <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
                Level</h3>
            <div class="space-y-3">
                @foreach (['Beginner', 'Intermediate', 'Advanced'] as $lv)
                    <label wire:key="lv-{{ $lv }}-{{ in_array($lv, $selectedLevels) ? 'on' : 'off' }}"
                        class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model.live="selectedLevels" value="{{ $lv }}"
                            class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange cursor-pointer">
                        <span
                            class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">{{ $lv }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</aside>

{{-- Desktop Sidebar Filter --}}
<aside class="hidden md:block w-full md:w-64 shrink-0 space-y-8" wire:key="sidebar-container">

    {{-- Bidang Studi / Kategori --}}
    <div wire:key="filter-group-categories">
        <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
            Bidang Studi</h3>
        <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
            @foreach ($categories as $category)
                <label
                    wire:key="cat-{{ $category->id }}-{{ in_array($category->id, $selectedCategories) ? 'on' : 'off' }}"
                    class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}"
                        class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange cursor-pointer">
                    <span
                        class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Level Kesulitan --}}
    <div wire:key="filter-group-levels">
        <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
            Level</h3>
        <div class="space-y-3">
            @foreach (['Beginner', 'Intermediate', 'Advanced'] as $lv)
                <label wire:key="lv-{{ $lv }}-{{ in_array($lv, $selectedLevels) ? 'on' : 'off' }}"
                    class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" wire:model.live="selectedLevels" value="{{ $lv }}"
                        class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange cursor-pointer">
                    <span
                        class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">{{ $lv }}</span>
                </label>
            @endforeach
        </div>
    </div>

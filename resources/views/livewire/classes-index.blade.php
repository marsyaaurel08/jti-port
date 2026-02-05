<div class="max-w-[1440px] mx-auto px-10 lg:px-72 py-12" wire:key="classes-index-root">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-black text-jti-primary uppercase tracking-tight">
                {{ $classes->total() }} Kelas Tersedia
            </h1>
            @if(!empty($selectedCategories) || !empty($selectedRatings) || !empty($selectedLevels) || $isFree || $isPaid)
                <button wire:click="resetFilters" class="text-left text-[10px] font-black text-jti-orange hover:underline uppercase tracking-widest">
                    Hapus Semua Filter
                </button>
            @endif
        </div>

        <div class="flex items-center gap-3 bg-gray-50/50 p-2 rounded-xl border border-gray-100/50 ml-auto">
            <label class="hidden sm:block text-[10px] font-black text-gray-400 uppercase tracking-widest">Urutkan:</label>
            <div class="relative group">
                <select wire:model.live="sort" class="appearance-none pl-4 pr-10 py-2 bg-white border border-gray-200 rounded-xl text-xs font-black text-jti-primary focus:ring-2 focus:ring-jti-primary/20 cursor-pointer shadow-sm">
                    <option value="latest">Terbaru</option>
                    <option value="price_low">Investasi Terendah</option>
                    <option value="price_high">Investasi Tertinggi</option>
                </select>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-jti-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-10">
        {{-- Sidebar Filter Statis --}}
        <aside class="w-full md:w-64 shrink-0 space-y-8" wire:key="sidebar-container">
            {{-- Kategori --}}
            <div>
                <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">Bidang Studi</h3>
                <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($categories as $category)
                        <label wire:key="cat-{{ $category->id }}-{{ in_array($category->id, $selectedCategories) ? 'on' : 'off' }}" class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}" class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange cursor-pointer">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Level --}}
            <div>
                <h3 class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">Level</h3>
                <div class="space-y-3">
                    @foreach(['Beginner', 'Intermediate', 'Advanced'] as $lv)
                        <label wire:key="lv-{{ $lv }}-{{ in_array($lv, $selectedLevels) ? 'on' : 'off' }}" class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" wire:model.live="selectedLevels" value="{{ $lv }}" class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange cursor-pointer">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">{{ $lv }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="grow space-y-5">
            @forelse($classes as $class)
                <x-class.class-card-horizontal :class="$class" />
            @empty
                <div class="py-24 text-center border-2 border-dashed border-gray-100 rounded-3xl bg-gray-50/50">
                    <h3 class="text-lg font-bold text-jti-primary mb-1">Kelas Belum Tersedia</h3>
                    <p class="text-sm text-gray-400">Coba sesuaikan filter pencarian Anda.</p>
                </div>
            @endforelse

            <div class="mt-12 flex justify-center">
                {{ $classes->links('components.pagination.custom-tailwind') }}
            </div>
        </div>
    </div>
</div>

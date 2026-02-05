<div class="max-w-[1440px] mx-auto px-10 lg:px-72 py-12">
    {{-- Header: Info Hasil & Sorting --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-black text-jti-primary">
                {{ $courses->total() }} hasil untuk "Semua Kursus"
            </h1>
            {{-- Tombol Reset Filter: Hanya muncul jika ada filter aktif --}}
            @if (!empty($selectedRatings) || !empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories))
                <button wire:click="resetFilters"
                    class="text-left text-xs font-bold text-jti-orange hover:underline uppercase tracking-widest">
                    Hapus Semua Filter
                </button>
            @endif
        </div>

        <div class="flex items-center gap-4 bg-gray-50/50 p-2 rounded-xl border border-gray-100/50">
            {{-- Button Filter Mobile: Ditingkatkan dengan efek hover --}}
            <button @click="$dispatch('open-filter')"
                class="md:hidden flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-jti-primary font-bold shadow-sm hover:border-jti-primary hover:bg-jti-light transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 4.5h18m-18 5h18m-18 5h18m-18 5h18" stroke-width="2.5" />
                </svg>
                <span class="text-sm">Filter</span>
            </button>

            {{-- Dropdown Sort: Dibuat lebih robust --}}
            <div class="flex items-center gap-3 ml-auto">
                <label for="sort-select"
                    class="hidden sm:block text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    Urutkan Berdasarkan
                </label>

                <div class="relative group">
                    <select wire:model.live="sort" id="sort-select"
                        class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-black text-jti-primary focus:ring-2 focus:ring-jti-primary/20 focus:border-jti-primary shadow-sm cursor-pointer transition-all hover:border-jti-primary">
                        <option value="latest">Materi Terbaru</option>
                        <option value="price_low">Harga Termurah</option>
                        <option value="price_high">Harga Termahal</option>
                    </select>

                    {{-- Custom Chevron Icon --}}
                    <div
                        class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-jti-primary group-hover:text-jti-orange transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-10">
        {{-- Sidebar Filter --}}
        {{-- Sidebar Filter --}}
        <aside class="w-full md:w-64 shrink-0 space-y-8" wire:key="sidebar-container">

            {{-- Filter Kategori --}}
            <div wire:key="filter-group-categories">
                <h3
                    class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
                    Kategori
                </h3>
                <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach ($categories as $category)
                        <label
                            wire:key="cat-{{ $category->id }}-{{ in_array($category->id, $selectedCategories) ? 'on' : 'off' }}"
                            class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}"
                                class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange transition-all cursor-pointer">
                            <span
                                class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">
                                {{ $category->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Filter Rating --}}
            <div wire:key="filter-group-ratings">
                <h3
                    class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
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
                            <span
                                class="text-xs font-bold text-gray-600 group-hover:text-jti-primary transition-colors">
                                {{ $i }}.0 & Keatas
                            </span>
                        </label>
                    @endfor
                </div>
            </div>

            {{-- Filter Level Kesulitan --}}
            <div wire:key="filter-group-levels">
                <h3
                    class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
                    Level Kesulitan
                </h3>
                <div class="space-y-3">
                    @foreach (['Beginner', 'Intermediate', 'Advanced'] as $lv)
                        <label wire:key="level-{{ $lv }}-{{ in_array($lv, $selectedLevels) ? 'on' : 'off' }}"
                            class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" wire:model.live="selectedLevels" value="{{ $lv }}"
                                class="w-4 h-4 rounded border-gray-300 text-jti-orange focus:ring-jti-orange transition-all cursor-pointer">
                            <span
                                class="text-sm font-medium text-gray-700 group-hover:text-jti-primary transition-colors">
                                {{ $lv }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Filter Harga --}}
            <div wire:key="filter-group-price">
                <h3
                    class="font-black text-sm text-jti-primary uppercase tracking-tight mb-4 pb-2 border-b border-gray-100">
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
        {{-- List Hasil --}}
        <div class="grow">
            <div class="space-y-5">
                @forelse($courses as $course)
                    <x-course.list-card-horizontal :course="$course" />
                @empty
                    <div class="py-32 text-center border-2 border-dashed border-gray-100 rounded-3xl bg-gray-50/50">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white shadow-sm mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-jti-primary mb-1">Tidak ada hasil ditemukan</h3>
                        <p class="text-sm text-gray-400">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginasi Custom --}}
            <div class="mt-16 flex justify-center">
                <div class="inline-flex items-center p-1 bg-white border border-gray-100 rounded-2xl shadow-sm">
                    {{ $courses->links('components.pagination.custom-tailwind') }}
                </div>
            </div>
        </div>
    </div>
</div>

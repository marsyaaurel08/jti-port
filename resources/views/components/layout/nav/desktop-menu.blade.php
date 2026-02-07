@props(['categories'])

<div class="hidden md:block relative" @mouseenter="openExplore = true" @mouseleave="openExplore = false">
    <button :class="openExplore ? 'text-jti-maroon' : 'text-gray-700'"
        class="text-sm font-semibold hover:text-jti-maroon px-2 py-7 transition-colors duration-200 flex items-center gap-1">
        Explore
        <svg :class="openExplore ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="openExplore" x-transition
        class="absolute left-0 w-72 bg-white border border-gray-100 shadow-2xl py-3 z-50 rounded-b-xl">
        <div class="px-4 py-2 text-[10px] text-jti-maroon/50 tracking-widest font-semibold">Semua Kategori</div>
        @foreach ($categories as $category)
            <a href="/courses?category={{ $category->slug }}"
               class="flex justify-between items-center px-4 py-3 text-sm font-medium text-jti-primary hover:bg-jti-light hover:text-jti-orange transition-all group">
                <span>{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</div>

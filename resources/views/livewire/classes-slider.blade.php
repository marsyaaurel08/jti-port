{{-- HAPUS class 'group' dari div ini --}}
<div class="relative" x-data="{ canLoadMore: @entangle('perPage') < @entangle('maxItems') }">

    {{-- Container Slider --}}
    <div class="flex overflow-x-auto gap-5 px-4 lg:px-20 py-4 scrollbar-hide scroll-smooth snap-x snap-mandatory items-stretch"
        id="classes-slider">
        @foreach ($classes as $class)
            <div class="snap-start shrink-0 w-[260px] h-full">
                <x-class.card :class="$class" />
            </div>
        @endforeach

        {{-- Infinite Scroll Trigger --}}
        @if ($perPage < $maxItems)
            <div x-intersect.margin.500px="$wire.loadMore()"
                class="snap-start shrink-0 w-20 flex items-center justify-center">
                <div class="w-8 h-8 border-3 border-jti-maroon border-t-transparent rounded-full animate-spin"></div>
            </div>
        @endif
    </div>

    {{-- Navigasi Button --}}
    <button onclick="document.getElementById('classes-slider').scrollBy({left: -520, behavior: 'smooth'})"
        class="absolute left-4 lg:-left-8 top-1/2 -translate-y-1/2 bg-white text-jti-primary p-3.5 rounded-full z-50 hidden md:flex items-center justify-center border border-gray-100 shadow-[0_10px_30px_rgba(20,33,61,0.15)] hover:bg-jti-primary hover:text-white hover:scale-110 transition-all duration-300 active:scale-90 group">
        <svg class="w-6 h-6 transform transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <button onclick="document.getElementById('classes-slider').scrollBy({left: 520, behavior: 'smooth'})"
        class="absolute right-4 lg:-right-8 top-1/2 -translate-y-1/2 bg-white text-jti-primary p-3.5 rounded-full z-50 hidden md:flex items-center justify-center border border-gray-100 shadow-[0_10px_30px_rgba(20,33,61,0.15)] hover:bg-jti-primary hover:text-white hover:scale-110 transition-all duration-300 active:scale-90 group">
        <svg class="w-6 h-6 transform transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
        </svg>
    </button>
</div>

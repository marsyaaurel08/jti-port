<div class="relative group" x-data="{ canLoadMore: @entangle('perPage') < @entangle('maxItems') }">

    {{-- Container Slider: Ditambahkan items-stretch --}}
    <div
        class="flex overflow-x-auto gap-5 pb-12 pt-4 scrollbar-hide scroll-smooth snap-x snap-mandatory items-stretch"
        id="course-slider"
    >
        @foreach ($courses as $course)
            {{-- Wrapper Card: Pastikan h-full agar stretch bekerja --}}
            <div class="snap-start shrink-0 w-[260px] h-full">
                <x-course.card :course="$course" />
            </div>
        @endforeach

        @if($perPage < $maxItems)
            <div
                x-intersect.margin.500px="$wire.loadMore()"
                class="snap-start shrink-0 w-20 flex items-center justify-center"
            >
                <div class="w-8 h-8 border-3 border-jti-orange border-t-transparent rounded-full animate-spin"></div>
            </div>
        @endif
    </div>

    {{-- Navigasi Button: Posisi diperbaiki (Luar Container) --}}
    <button
        onclick="document.getElementById('course-slider').scrollBy({left: -520, behavior: 'smooth'})"
        class="absolute left-4 lg:-left-6 top-1/2 -translate-y-1/2 bg-white p-3 rounded-full shadow-2xl border border-gray-100 z-50 hidden md:flex hover:scale-110 transition-transform active:scale-95 text-jti-primary"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>

    <button
        onclick="document.getElementById('course-slider').scrollBy({left: 520, behavior: 'smooth'})"
        class="absolute right-4 lg:-right-6 top-1/2 -translate-y-1/2 bg-white p-3 rounded-full shadow-2xl border border-gray-100 z-50 hidden md:flex hover:scale-110 transition-transform active:scale-95 text-jti-primary"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
</div>

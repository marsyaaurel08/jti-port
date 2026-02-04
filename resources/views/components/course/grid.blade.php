{{-- resources/views/components/course/slider.blade.php --}}
<div class="relative group">
    {{-- Container Slider --}}
    <div class="flex overflow-x-auto gap-6 px-10 lg:px-72 pb-10 scrollbar-hide scroll-smooth transition-all"
        id="course-slider">
        {{ $slot }}
    </div>

    {{-- Tombol Navigasi (Opsional) --}}
    <button onclick="document.getElementById('course-slider').scrollBy({left: -400, behavior: 'smooth'})"
        class="absolute left-10 top-1/2 -translate-y-12 bg-white p-3 rounded-full shadow-xl border z-40 hidden md:block">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button onclick="document.getElementById('course-slider').scrollBy({left: 400, behavior: 'smooth'})"
        class="absolute right-10 top-1/2 -translate-y-12 bg-white p-3 rounded-full shadow-xl border z-40 hidden md:block">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>
</div>

<div class="relative" x-data="{ canLoadMore: @entangle('perPage') < @entangle('maxItems') }">
    @if ($courses->count() > 0)
        {{-- Container Slider --}}
        <div class="flex overflow-x-auto gap-5 px-4 lg:px-20 py-4 scrollbar-hide scroll-smooth snap-x snap-mandatory items-stretch"
            id="course-slider">
            @foreach ($courses as $index => $course)
                {{-- Tambahkan AOS di sini --}}
                {{-- Delay dihitung agar kartu muncul bergantian (100ms, 200ms, dst) --}}
                <div class="snap-start shrink-0 w-[260px] h-full"
                     data-aos="fade-up"
                     data-aos-delay="{{ ($index % 4) * 100 }}"
                     data-aos-duration="800">
                    <x-course.card :course="$course" />
                </div>
            @endforeach

            @if ($perPage < $maxItems)
                <div x-intersect.margin.500px="$wire.loadMore()"
                    class="snap-start shrink-0 w-20 flex items-center justify-center">
                    <div class="w-8 h-8 border-3 border-jti-maroon border-t-transparent rounded-full animate-spin"></div>
                </div>
            @endif
        </div>

        {{-- Navigasi Button dengan AOS Zoom-in --}}
        <button onclick="document.getElementById('course-slider').scrollBy({left: -520, behavior: 'smooth'})"
            data-aos="zoom-in"
            data-aos-delay="1000"
            class="absolute left-4 lg:-left-8 top-1/2 -translate-y-1/2 bg-white text-jti-primary p-3.5 rounded-full z-50 hidden md:flex items-center justify-center border border-gray-100 shadow-[0_10px_30px_rgba(20,33,61,0.15)] hover:bg-jti-primary hover:text-white hover:scale-110 transition-all duration-300 active:scale-90 group">
            <svg class="w-6 h-6 transform transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <button onclick="document.getElementById('course-slider').scrollBy({left: 520, behavior: 'smooth'})"
            data-aos="zoom-in"
            data-aos-delay="1000"
            class="absolute right-4 lg:-right-8 top-1/2 -translate-y-1/2 bg-white text-jti-primary p-3.5 rounded-full z-50 hidden md:flex items-center justify-center border border-gray-100 shadow-[0_10px_30px_rgba(20,33,61,0.15)] hover:bg-jti-primary hover:text-white hover:scale-110 transition-all duration-300 active:scale-90 group">
            <svg class="w-6 h-6 transform transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    @else
        {{-- Empty State dengan AOS Fade --}}
        <div class="py-16 px-4 lg:px-20 text-center" data-aos="fade-in">
            <h3 class="text-lg font-bold text-jti-primary mb-2">Kursus Kosong</h3>
            <p class="text-sm text-gray-400">Belum ada kursus yang tersedia saat ini.</p>
        </div>
    @endif
</div>

<x-layouts.app>
    <x-home.hero />

    <main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Course Section --}}
        <section id="course-list" class="pt-16 md:pt-24 bg-white">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 border-b border-gray-100 pb-6">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-8 bg-jti-maroon rounded-full"></div>
                        <h2 class="text-2xl md:text-3xl font-bold text-jti-primary tracking-tight">Kursus Terbaru</h2>
                    </div>
                    <p class="text-gray-500 text-sm md:text-base ml-4">Eksplorasi materi terupdate untuk meningkatkan keahlian Anda.</p>
                </div>

                <a href="{{ route('courses.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-jti-primary group rounded-xl transition-all duration-300">
                    <span class="text-xs md:text-sm font-bold capitalize tracking-widest text-jti-primary group-hover:text-white">Lihat Semua</span>
                    <svg class="w-4 h-4 text-jti-primary group-hover:text-white transform group-hover:translate-x-1 transition-transform"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

            {{-- Slider Container dengan padding negatif untuk mobile (agar scroll mentok layar) --}}
            <div class="-mx-4 sm:mx-0">
                <livewire:course-slider />
            </div>
        </section>

        {{-- Classes Section --}}
        <section id="classes-list" class="pt-16 md:pt-20 pb-20 bg-white">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 border-b border-gray-100 pb-6">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-8 bg-jti-orange rounded-full"></div>
                        <h2 class="text-2xl md:text-3xl font-bold text-jti-primary tracking-tight">Kelas Terbaru</h2>
                    </div>
                    <p class="text-gray-500 text-sm md:text-base ml-4">Bergabunglah dengan kelas intensif bersama instruktur berpengalaman.</p>
                </div>

                <a href="{{ route('classes.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-jti-primary group rounded-xl transition-all duration-300">
                    <span class="text-xs md:text-sm font-bold capitalize tracking-widest text-jti-primary group-hover:text-white">Lihat Semua</span>
                    <svg class="w-4 h-4 text-jti-primary group-hover:text-white transform group-hover:translate-x-1 transition-transform"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

            <div class="-mx-4 sm:mx-0">
                <livewire:classes-slider />
            </div>
        </section>
    </main>
</x-layouts.app>

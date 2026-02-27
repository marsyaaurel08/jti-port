<x-layouts.app>
    {{-- 1. Hero & 2. Intro: Biasanya dibatasi internal komponennya agar background tetap full --}}
    <x-home.hero />
    <x-home.introduction />

    <main class="bg-white">
        {{-- 3. Course Section --}}
        {{-- Gunakan xl:px-48 untuk layar besar agar sesuai dengan keinginan Anda --}}
        <section id="course-list" class="pt-24 md:pt-24 max-w-[1440px] mx-auto px-6 md:px-48">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 border-b border-gray-100 pb-8">
                <div class="space-y-2">
                    <span class="text-jti-yellow font-bold text-[10px] md:text-xs uppercase tracking-[0.2em] ml-1">Certification Programs</span>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-8 bg-jti-yellow rounded-full"></div>
                        <h2 class="text-2xl md:text-4xl font-black text-jti-primary tracking-tight uppercase">Kursus Terbaru</h2>
                    </div>
                    <p class="text-gray-500 text-sm md:text-base ml-5">Tingkatkan kompetensi standar global melalui materi terkurasi.</p>
                </div>

                <a href="{{ route('courses.index') }}"
                    class="w-fit inline-flex items-center gap-3 px-6 py-3 bg-white border border-gray-200 hover:border-jti-yellow group rounded-sm transition-all duration-300 shadow-sm">
                    <span class="text-[10px] md:text-xs font-bold uppercase tracking-widest text-jti-primary">Katalog Kursus</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-jti-yellow transform group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="-mx-6 sm:mx-0 p-6 md:p-0">
                <livewire:course-slider />
            </div>
        </section>

        {{-- 4 & 5. Benefit & Instructors: Biasanya punya background sendiri --}}
        <x-home.benefit />
        <x-home.instructors />

        {{-- 6. Classes Section --}}
        <section id="classes-list" class="pt-24 md:pt-24 pb-24 max-w-[1440px] mx-auto px-6 md:px-48">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 border-b border-gray-100 pb-8">
                <div class="space-y-2">
                    <span class="text-jti-yellow font-bold text-[10px] md:text-xs uppercase tracking-[0.2em] ml-1">Intensive Learning</span>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-8 bg-jti-yellow rounded-full"></div>
                        <h2 class="text-2xl md:text-4xl font-black text-jti-primary tracking-tight uppercase">Kelas Terbaru</h2>
                    </div>
                    <p class="text-gray-500 text-sm md:text-base ml-5">Bergabunglah dalam bimbingan intensif bersama para ahli.</p>
                </div>

                <a href="{{ route('classes.index') }}"
                    class="w-fit inline-flex items-center gap-3 px-6 py-3 bg-white border border-gray-200 hover:border-jti-yellow group rounded-sm transition-all duration-300 shadow-sm">
                    <span class="text-[10px] md:text-xs font-bold uppercase tracking-widest text-jti-primary">Lihat Semua</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-jti-yellow transform group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="-mx-6 sm:mx-0 p-6 md:p-0">
                <livewire:classes-slider />
            </div>
        </section>
    </main>
    <x-home.cta />
</x-layouts.app>

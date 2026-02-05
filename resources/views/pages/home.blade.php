<x-layouts.app>
    <x-home.hero />

    <main class="max-w-[1440px] mx-auto px-6">
        <section id="course-list" class="pt-24 bg-white">
            {{-- Header Section: Menggunakan Flex untuk menyejajarkan Judul dan Tombol --}}
            <div class="flex items-end justify-between mb-8 border-b border-gray-50 pb-4">
                <div>
                    <h2 class="text-2xl font-black text-jti-primary">Kursus Terbaru</h2>
                    <p class="text-gray-500 text-sm">Materi terupdate untuk pelatihan Anda.</p>
                </div>

                {{-- Tombol Tampilkan Semua --}}
                <a href="{{ route('courses.index') }}"
                    class="group flex items-center gap-2 text-jti-primary hover:text-jti-maroon transition-colors duration-300">
                    <span class="text-sm font-black uppercase tracking-wider">Tampilkan Semua</span>
                    <div class="p-2 bg-gray-50 group-hover:bg-jti-light rounded-lg transition-colors">
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </a>
            </div>

            <livewire:course-slider />
        </section>
        <section id="classes-list" class=" bg-white">
            {{-- Header Section: Menggunakan Flex untuk menyejajarkan Judul dan Tombol --}}
            <div class="flex items-end justify-between mb-8 border-b border-gray-50 pb-4">
                <div>
                    <h2 class="text-2xl font-black text-jti-primary">Kelas Terbaru</h2>
                    <p class="text-gray-500 text-sm">Materi terupdate untuk persiapan pelatihan Anda.</p>
                </div>

                {{-- Tombol Tampilkan Semua --}}
                <a href="{{ route('classes.index') }}"
                    class="group flex items-center gap-2 text-jti-primary hover:text-jti-maroon transition-colors duration-300">
                    <span class="text-sm font-black uppercase tracking-wider">Tampilkan Semua</span>
                    <div class="p-2 bg-gray-50 group-hover:bg-jti-light rounded-lg transition-colors">
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </a>
            </div>

            <livewire:classes-slider />
        </section>
    </main>
</x-layouts.app>

<x-layouts.app>
    {{-- Header / Hero Section (Pastikan komponen ini juga bertema terang) --}}
    <x-classes.details-header :class="$class" />

    <div class="bg-[#FAFBFC] min-h-screen">
        {{-- Padding disesuaikan agar simetris dengan header sebelumnya --}}
        <div class="max-w-[1440px] mx-auto px-6 lg:px-48 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 lg:gap-16">

                {{-- Kolom Kiri: Konten Utama --}}
                <div class="lg:col-span-3 space-y-16">

                    {{-- Deskripsi Kelas --}}
                    <section class="relative">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-2 h-6 bg-jti-yellow rounded-full"></span>
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Tentang Kelas</h2>
                        </div>

                        {{-- Typography yang bersih dengan line-height yang lega --}}
                        <div
                            class="prose max-w-none prose-p:text-gray-600 prose-headings:text-gray-900 prose-strong:text-jti-primary leading-relaxed">
                            {!! $class->description !!}
                        </div>
                    </section>

                    {{-- Informasi Instruktur --}}
                    <section class="relative">
                        <div class="flex items-center gap-3 mb-8">
                            <span class="w-2 h-6 bg-jti-yellow rounded-full"></span>
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Instruktur</h2>
                        </div>

                        <div class="p-8 bg-white border border-gray-200 rounded-2xl">
                            <div class="flex items-center gap-6">
                                {{-- Avatar --}}
                                <div
                                    class="w-24 h-24 rounded-full bg-gradient-to-br from-jti-primary to-jti-yellow flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-3xl font-bold text-white">{{ strtoupper(substr($class->instructor->name, 0, 1)) }}</span>
                                </div>

                                {{-- Info Instruktur --}}
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                                        {{ $class->instructor->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-3">
                                        {{ $class->instructor->email }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Pengajar Berpengalaman di JTI Port
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Kurikulum (Accordion) --}}
                    @if ($class->course->chapters->count() > 0)
                        <section class="relative">
                            <div class="flex items-center justify-between mb-8">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-6 bg-jti-yellow rounded-full"></span>
                                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Kurikulum Pembelajaran
                                    </h2>
                                </div>
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.15em]">
                                    {{ $class->course->chapters->count() }} Modul
                                </span>
                            </div>

                            {{-- Accordion Container: Menggunakan pembungkus minimalis --}}
                            <div class="space-y-3">
                                @foreach ($class->course->chapters as $chapter)
                                    <x-course.chapter-accordion :chapter="$chapter" />
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Persyaratan Kelas --}}
                    @if ($class->course->requirements)
                        <section class="p-8 bg-white border border-gray-100 rounded-2xl">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Persyaratan Pembelajaran</h2>
                            <div class="text-gray-600 text-sm">
                                {!! $class->course->requirements !!}
                            </div>
                        </section>
                    @endif

                    {{-- Jadwal & Lokasi Kelas --}}
                    <section class="p-8 bg-white border border-gray-100 rounded-2xl">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Jadwal & Lokasi</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Tanggal Mulai --}}
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-jti-yellow/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-jti-yellow" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Tanggal Mulai
                                    </p>
                                    <p class="text-lg font-bold text-gray-900 mt-1">
                                        {{ $class->start_date->format('d M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $class->start_date->format('H:i') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Tanggal Berakhir --}}
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-jti-yellow/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-jti-yellow" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Tanggal
                                        Berakhir</p>
                                    <p class="text-lg font-bold text-gray-900 mt-1">
                                        {{ $class->end_date->format('d M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $class->end_date->format('H:i') }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </section>

                </div>

                {{-- Kolom Kanan: Sidebar Floating Card --}}
                <div class="relative hidden lg:block">
                    <div class="sticky top-24">
                        {{-- Soft shadow untuk efek kedalaman --}}
                        <div class="absolute -inset-4 bg-gray-200/20 blur-2xl rounded-[32px] -z-10"></div>
                        <x-classes.enrollment-card :class="$class" />
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Mobile Checkout Bar --}}
    <x-classes.mobile-checkout-bar :class="$class" />
</x-layouts.app>

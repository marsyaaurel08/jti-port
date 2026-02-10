{{-- resources/views/pages/user/enrolled.blade.php --}}
<x-user.layout.index :user="$user">
    <div class="space-y-5">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <div class="space-y-1">
                <h1 class="text-3xl font-semibold text-jti-primary tracking-tight">Kursus Saya</h1>
            </div>

            {{-- Filter Sederhana --}}
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-gray-400">Urutkan:</span>
                <select
                    class="bg-white border-none shadow-sm ring-1 ring-gray-100 rounded-2xl px-4 py-2.5 text-xs font-bold text-jti-primary focus:ring-2 focus:ring-jti-primary/20 outline-none transition-all cursor-pointer">
                    <option>Terbaru Dipelajari</option>
                    <option>Abjad (A - Z)</option>
                    <option>Progres Terendah</option>
                </select>
            </div>
        </div>

        {{-- Grid Kursus --}}
        @if ($enrolled->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach ($enrolled as $item)
                    <div
                        class="group bg-white rounded-lg border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgb(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-500 overflow-hidden flex flex-col h-full">

                        {{-- Thumbnail Section --}}
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $item->course->thumbnail_url }}" alt="{{ $item->course->title }}"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">

                            {{-- Badge Kategori --}}
                            <div class="absolute top-4 left-4">
                                <span
                                    class="bg-white/90 backdrop-blur-md text-jti-primary text-[10px] font-semibold px-3 py-1.5 rounded-xl shadow-sm uppercase tracking-wider">
                                    {{ $item->course->category->name }}
                                </span>
                            </div>

                            {{-- Play Overlay --}}
                            <div
                                class="absolute inset-0 bg-jti-primary/20 opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center backdrop-blur-[2px]">
                                <a href="#"
                                    class="w-14 h-14 bg-white text-jti-primary rounded-2xl flex items-center justify-center shadow-2xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                                    <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4.5 3L15.5 10L4.5 17V3Z" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Content Body --}}
                        <div class="p-6 flex flex-col flex-1">
                            <h3
                                class="font-bold text-jti-primary text-base line-clamp-2 mb-6 group-hover:text-jti-orange transition-colors duration-300 min-h-[3rem]">
                                {{ $item->course->title }}
                            </h3>

                            {{-- Progress Logic --}}
                            <div class="mt-auto space-y-3">
                                <div class="flex items-end justify-between">
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            Progress</p>
                                        <p class="text-sm font-semibold text-jti-primary">
                                            {{ $item->progress_percentage ?? 0 }}% <span
                                                class="text-gray-300 font-medium">Selesai</span></p>
                                    </div>
                                    {{-- Mini Icon untuk Visual --}}
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-jti-orange/10 group-hover:text-jti-orange transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="relative w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-jti-primary to-jti-orange rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(20,33,61,0.3)]"
                                        style="width: {{ $item->progress_percentage ?? 0 }}%"></div>
                                </div>

                                <a href="#"
                                    class="flex items-center justify-center gap-2 w-full mt-4 py-3.5 bg-jti-primary text-white text-xs font-semibold uppercase tracking-widest rounded-2xl hover:bg-jti-maroon hover:shadow-lg hover:shadow-jti-maroon/20 transition-all duration-300">
                                    <span>Lanjutkan Belajar</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12 flex justify-center">
                {{ $enrolled->links() }}
            </div>
        @else
            {{-- Empty State Premium --}}
            <div
                class="bg-white rounded-xl p-16 md:p-24 text-center border border-gray-100 shadow-sm relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 -translate-y-12 translate-x-12 w-64 h-64 bg-gray-50 rounded-full blur-3xl opacity-50">
                </div>

                <div class="relative">
                    <div
                        class="w-24 h-24 bg-gray-50 rounded-lg flex items-center justify-center mx-auto mb-8 text-gray-200 rotate-12 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-jti-primary tracking-tight">Belum Ada Kursus Aktif</h3>
                    <p class="text-gray-400 max-w-sm mx-auto mt-3 mb-10 font-medium">Investasikan waktu luangmu dengan
                        mempelajari skill baru hari ini.</p>
                    <a href="{{ route('courses.index') }}"
                        class="inline-flex items-center gap-3 px-10 py-4 bg-jti-primary text-white font-semibold rounded-2xl hover:bg-jti-maroon transition-all shadow-xl shadow-jti-primary/20 active:scale-95">
                        Eksplor Katalog Kursus
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-user.layout.index>

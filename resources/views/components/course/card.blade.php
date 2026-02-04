@props(['course'])

{{-- 1. Hapus 'relative' di pembungkus paling luar agar z-index popover tidak terkekang --}}
<div x-data="{ showInfo: false }" class="h-full" @mouseenter="showInfo = true" @mouseleave="showInfo = false">
    <div class="relative h-full">
        {{-- Card Utama --}}
        <div
            class="group bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-[0_20px_50px_rgba(20,33,61,0.12)] transition-all duration-500 flex flex-col h-full relative z-30">
            {{-- Image Section --}}
            <div class="relative h-40 shrink-0 overflow-hidden">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                    class="w-full h-full object-cover transition-transform duration-700">
                <div class="absolute inset-0 bg-linear-to-t from-jti-primary/20 to-transparent"></div>
                <span
                    class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider text-jti-primary shadow-sm z-10">
                    {{ $course->level }}
                </span>
            </div>

            {{-- Content Section --}}
            <div class="p-4 flex flex-col grow">
                <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">
                    {{ Str::limit($course->instructor_name, 25) }}</p>

                <h3 class="font-bold text-jti-primary text-[15px] line-clamp-2 mb-2 leading-tight min-h-[40px]">
                    {{ $course->title }}
                </h3>

                {{-- Rating --}}
                <div class="flex items-center gap-1 mb-4">
                    <span class="text-xs font-black text-jti-orange">{{ number_format($course->avg_rating, 1) }}</span>
                    <div class="flex text-jti-yellow">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-3 h-3 {{ $i <= round($course->avg_rating) ? 'fill-current' : 'text-gray-200 fill-current' }}"
                                viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="text-[10px] text-gray-400">({{ $course->reviews_count }})</span>
                </div>

                <div class="mt-auto">
                    <div class="flex items-center justify-between mb-3 pt-3 border-t border-gray-50">
                        <div class="font-black text-md text-jti-primary">
                            {{ $course->price > 0 ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'Free' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-2">
                        <button
                            class="col-span-1 flex items-center justify-center p-2 rounded-xl bg-gray-50 text-gray-400 hover:bg-jti-light hover:text-jti-primary transition-all border border-gray-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>

                        <a href="/course/{{ $course->slug }}"
                            class="relative col-span-4 group/btn overflow-hidden flex items-center justify-center bg-jti-yellow text-jti-primary font-black py-2.5 rounded-xl transition-all duration-500 shadow-sm hover:shadow-jti-orange/20 active:scale-95 text-[11px] uppercase tracking-wider">
                            <div
                                class="absolute inset-0 w-full h-full bg-linear-to-r from-jti-orange via-jti-maroon to-jti-primary opacity-0 group-hover/btn:opacity-100 transition-opacity duration-500">
                            </div>
                            <span
                                class="relative z-10 flex items-center gap-1 group-hover/btn:text-white transition-colors duration-300">
                                Mulai <svg class="w-3 h-3 transition-transform group-hover/btn:translate-x-1"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Popover Info: Perbaikan Z-Index dan Pointer Events --}}
        <div x-show="showInfo" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
            {{-- Menambahkan padding kiri negatif agar mouse tidak 'lepas' saat menyeberang ke popover --}} class="absolute left-full top-0 ml-1 pl-1 w-85 z-9999 hidden lg:block"
            style="display: none;">

            <div class="bg-white border border-gray-100 shadow-2xl rounded-2xl p-6 relative pointer-events-auto">
                {{-- Arrow --}}
                <div
                    class="absolute right-full top-12 w-4 h-4 bg-white border-l border-b border-gray-100 rotate-45 -mr-2 z-10">
                </div>

                <div class="relative z-20">
                    <h4 class="font-bold text-jti-primary text-lg mb-2 leading-tight">{{ $course->title }}</h4>
                    <p class="text-xs text-gray-500 mb-5 leading-relaxed line-clamp-4">{{ $course->description }}</p>

                    <div class="flex flex-col gap-2">
                        {{-- Tombol Tambah ke Keranjang --}}
                        <button
                            class="w-full border-2 border-jti-primary text-jti-primary hover:bg-jti-primary hover:text-white font-bold py-2.5 rounded-xl transition-all">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

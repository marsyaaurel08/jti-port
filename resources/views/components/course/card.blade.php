@props(['course'])

<div class="h-full">
    <div class="relative h-full">
        {{-- Card Utama - Class 'group' ada di sini agar hover hanya berpengaruh pada card ini saja --}}
        <div class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-[0_20px_50px_rgba(20,33,61,0.12)] transition-all duration-500 flex flex-col h-full relative z-30 font-sans">

            {{-- Image Section --}}
            <div class="relative h-40 shrink-0 overflow-hidden">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider text-jti-primary shadow-sm z-10">
                    {{ $course->level }}
                </span>
            </div>

            {{-- Content Section --}}
            <div class="p-4 flex flex-col grow">
                {{-- Instruktur --}}
                <p class="text-[11px] text-gray-500 font-medium mb-1">
                    {{ Str::limit($course->instructor_name, 30) }}
                </p>

                {{-- Judul --}}
                <h3 class="font-bold text-jti-primary text-[15px] line-clamp-2 mb-1.5 leading-tight group-hover:text-jti-maroon transition-colors">
                    {{ $course->title }}
                </h3>

                {{-- Deskripsi Singkat --}}
                <p class="text-xs text-gray-400 line-clamp-2 mb-3 leading-relaxed">
                    {{ $course->description }}
                </p>

                {{-- Rating Section ala Udemy --}}
                <div class="flex items-center gap-1 mb-4 mt-auto">
                    <span class="text-xs font-bold text-jti-maroon">{{ number_format($course->avg_rating, 1) }}</span>
                    <div class="flex text-jti-yellow gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-3 h-3 {{ $i <= round($course->avg_rating) ? 'fill-current' : 'text-gray-200 fill-current' }}"
                                viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-[10px] text-gray-400">({{ number_format($course->reviews_count) }})</span>
                </div>

                {{-- Price & Action Section --}}
                <div class="pt-3 border-t border-gray-50">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-bold text-[16px] text-jti-primary">
                            {{ $course->price > 0 ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'Free' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-2">
                        <button class="col-span-1 flex items-center justify-center p-2 rounded-lg bg-gray-50 text-gray-400 hover:bg-jti-light hover:text-jti-primary transition-all border border-gray-100 active:scale-90" title="Tambah ke Keranjang">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>

                        <a href="/course/{{ $course->slug }}" class="relative col-span-4 group/btn overflow-hidden flex items-center justify-center bg-jti-yellow text-jti-primary py-2.5 rounded-lg transition-all duration-500 hover:shadow-lg active:scale-95 text-sm font-bold tracking-wider">
                            <div class="absolute inset-0 w-full h-full bg-jti-primary opacity-0 group-hover/btn:opacity-100 transition-opacity duration-500"></div>
                            <span class="relative z-10 flex items-center gap-1 group-hover/btn:text-white transition-colors duration-300">
                                Mulai <svg class="w-3 h-3 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

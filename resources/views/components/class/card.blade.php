@props(['class'])

<div class="h-full font-sans">
    <div class="relative h-full">
        {{-- Card Utama - 'group' ada di sini agar hover bersifat mandiri per card --}}
        <div class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-[0_20px_50px_rgba(20,33,61,0.12)] transition-all duration-500 flex flex-col h-full relative z-30">

            {{-- Image Section --}}
            <div class="relative h-40 shrink-0 overflow-hidden">
                <img src="{{ $class->thumbnail_url }}" alt="{{ $class->title }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                <span
                    class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider text-jti-primary shadow-sm z-10">
                    {{ $class->level }}
                </span>
            </div>

            {{-- Content Section --}}
            <div class="p-4 flex flex-col grow">
                {{-- Judul --}}
                <h3 class="font-bold text-jti-primary text-[15px] line-clamp-2 mb-1.5 leading-tight group-hover:text-jti-maroon transition-colors">
                    {{ $class->title }}
                </h3>

                {{-- Instruktur --}}
                <div class="flex items-center gap-1.5 mb-2">
                    <span class="text-[12px] text-gray-500 truncate font-medium">
                        {{ $class->instructor->name ?? $class->instructor_name }}
                    </span>
                </div>

                {{-- Deskripsi --}}
                <p class="text-xs text-gray-400 line-clamp-2 mb-3 leading-relaxed">
                    {{ $class->description }}
                </p>

                {{-- Rating Section ala Udemy --}}
                <div class="flex items-center gap-1 mb-4 mt-auto">
                    <span class="text-xs font-bold text-jti-maroon">{{ number_format($class->avg_rating, 1) }}</span>
                    <div class="flex text-jti-yellow gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-3 h-3 {{ $i <= round($class->avg_rating) ? 'fill-current' : 'text-gray-200 fill-current' }}"
                                viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-[10px] text-gray-400 font-medium">({{ number_format($class->reviews_count) }})</span>
                </div>

                {{-- Price & Actions --}}
                <div class="pt-3 border-t border-gray-50">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-bold text-[16px] text-jti-primary">
                            {{ $class->price > 0 ? 'Rp ' . number_format($class->price, 0, ',', '.') : 'Free' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-2">
                        {{-- Add to Cart --}}
                        <button title="Tambah ke Keranjang"
                            class="col-span-1 flex items-center justify-center p-2 rounded-xl bg-gray-50 text-gray-400 hover:bg-jti-light hover:text-jti-primary transition-all border border-gray-100 shadow-sm active:scale-90">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>

                        {{-- Main Action --}}
                        <a href="/class/{{ $class->slug }}"
                            class="relative col-span-4 group/btn overflow-hidden flex items-center justify-center bg-jti-yellow text-jti-primary font-bold py-2.5 rounded-xl transition-all duration-500 shadow-sm hover:shadow-lg active:scale-95 text-sm tracking-wider">
                            <div class="absolute inset-0 w-full h-full bg-jti-primary opacity-0 group-hover/btn:opacity-100 transition-opacity duration-500"></div>
                            <span class="relative z-10 flex items-center gap-1 group-hover/btn:text-white transition-colors duration-300">
                                Mulai <svg class="w-3 h-3 transition-transform group-hover/btn:translate-x-1"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

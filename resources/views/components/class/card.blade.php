@props(['class'])

<div class="h-full font-sans">
    <div class="relative h-full">
        {{-- Card Utama --}}
        <div class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-[0_20px_50px_rgba(20,33,61,0.12)] transition-all duration-500 flex flex-col h-full relative z-30">

            {{-- Image Section --}}
            <div class="relative h-40 shrink-0 overflow-hidden">
                <img src="{{ $class->thumbnail_url }}" alt="{{ $class->title }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>

            {{-- Content Section --}}
            <div class="p-4 flex flex-col grow">
                {{-- Instruktur --}}
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="text-[11px] text-gray-500 font-medium truncate">
                        {{ $class->instructor->name ?? ($class->instructor_name ?? 'Instruktur JTI') }}
                    </span>
                </div>

                {{-- Judul --}}
                <h3 class="font-bold text-jti-primary text-[15px] line-clamp-2 mb-1.5 leading-tight group-hover:text-jti-maroon transition-colors">
                    {{ $class->title }}
                </h3>

                {{-- Deskripsi --}}
                <p class="text-xs text-gray-400 line-clamp-2 mb-3 leading-relaxed">
                    {{ $class->description }}
                </p>

                {{-- Rating & Level Section (Disesuaikan berdampingan) --}}
                <div class="flex items-center flex-wrap justify-between gap-2 mb-4 mt-auto">
                    {{-- Rating --}}
                    <div class="flex items-center gap-1">
                        <span class="text-sm font-bold text-jti-maroon">{{ number_format($class->avg_rating, 1) }}</span>
                        @if (view()->exists('components.course.stars'))
                            <x-course.stars :rating="$class->avg_rating" />
                        @endif
                        <span class="text-[10px] text-gray-400 font-medium">({{ number_format($class->enrollments_count ?? 0) }})</span>
                    </div>


                    {{-- Modern Level Badge --}}
                    @php
                        $levelColor = match (strtolower($class->level)) {
                            'beginner', 'pemula' => 'bg-green-100 text-green-700 border-green-200',
                            'intermediate', 'menengah' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'advanced', 'mahir' => 'bg-red-100 text-red-700 border-red-200',
                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="px-2 py-0.5 {{ $levelColor }} rounded border text-[9px] font-black uppercase tracking-wider">
                        {{ $class->level }}
                    </span>
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
                        <a href="/classes/{{ $class->slug }}"
                            class="relative col-span-4 group/btn overflow-hidden flex items-center justify-center bg-jti-yellow text-jti-primary font-bold py-2.5 rounded-xl transition-all duration-500 shadow-sm hover:shadow-lg active:scale-95 text-sm tracking-wider">
                            <div class="absolute inset-0 w-full h-full bg-jti-primary opacity-0 group-hover/btn:opacity-100 transition-opacity duration-500"></div>
                            <span class="relative z-10 flex items-center gap-1 group-hover/btn:text-white transition-colors duration-300">
                                Lihat Detail <svg class="w-3 h-3 transition-transform group-hover/btn:translate-x-1"
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

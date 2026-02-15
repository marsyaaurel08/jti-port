<div class="group flex flex-col sm:flex-row bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl hover:shadow-jti-primary/5 transition-all duration-500">
    {{-- Thumbnail --}}
    <div class="sm:w-64 h-48 shrink-0 overflow-hidden relative">
        {{-- Status Badge --}}
        <div class="absolute top-3 left-3 z-10">
            <span class="bg-jti-yellow text-jti-primary text-[9px] font-black px-2 py-1 rounded shadow-sm uppercase tracking-wider">
                {{ $class->is_active ? 'Aktif' : 'Tutup' }}
            </span>
        </div>

        <img src="{{ $class->course->thumbnail_url ?? 'https://via.placeholder.com/400x300' }}"
             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $class->title }}">
        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
    </div>

    {{-- Info Konten --}}
    <div class="p-5 flex flex-col grow">
        <div class="flex justify-between items-start gap-4">
            <div class="grow">
                <h3 class="text-lg font-black text-jti-primary leading-tight mb-2 group-hover:text-jti-orange transition-colors">
                    {{ $class->title }}
                </h3>

                {{-- Nama Instructor --}}
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-full bg-jti-light border border-gray-50 flex items-center justify-center overflow-hidden">
                        <span class="text-[10px] font-black text-jti-primary uppercase">{{ substr($class->instructor->name, 0, 1) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-500 leading-none">{{ $class->instructor->name }}</span>
                    </div>
                </div>

                {{-- Tanggal Kelas --}}
                <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-jti-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $class->start_date->format('d M Y') }} - {{ $class->end_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Harga --}}
            <div class="text-right shrink-0">
                <div class="text-lg font-black text-jti-primary tracking-tighter">
                    {{ $class->price > 0 ? 'Rp' . number_format($class->price, 0, ',', '.') : 'Gratis' }}
                </div>
            </div>
        </div>

        {{-- Meta Data & Tombol --}}
        <div class="mt-auto pt-4 border-t border-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4 grow max-w-xs">
                <div class="flex flex-col shrink-0">
                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Kapasitas</span>
                    <span class="text-xs font-black text-jti-primary">{{ $class->current_enrolled }}/{{ $class->max_capacity }}</span>
                </div>
                <div class="grow h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-jti-orange transition-all duration-1000"
                         style="width: {{ ($class->current_enrolled / $class->max_capacity) * 100 }}%"></div>
                </div>
            </div>

            {{-- Tombol Lihat Detail --}}
            <a href="{{ route('classes.show', $class->id) }}"
               class="inline-flex items-center justify-center gap-2 bg-white text-jti-primary border-2 border-jti-primary px-5 py-2 rounded-xl text-sm font-black uppercase tracking-widest
                      hover:bg-jti-primary hover:text-white hover:shadow-lg hover:shadow-jti-primary/20
                      transition-all duration-300 active:scale-95 group/btn whitespace-nowrap">
                <span>Lihat Detail</span>
                <svg class="w-4 h-4 transform transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    </div>
</div>

<a href="{{ route('classes.show', $class->slug) }}"
    class="group flex flex-col sm:flex-row bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-xl hover:shadow-jti-primary/5 transition-all duration-500">

    {{-- Thumbnail Section (Ukuran disamakan dengan Course: sm:w-64 h-44) --}}
    <div class="sm:w-64 h-44 shrink-0 overflow-hidden relative">
        <div class="absolute top-3 left-3 z-10">
            <span
                class="{{ $class->is_active ? 'bg-emerald-500' : 'bg-gray-500' }} text-white text-[9px] font-bold px-2 py-1 rounded shadow-sm uppercase tracking-wider">
                {{ $class->is_active ? 'Aktif' : 'Tutup' }}
            </span>
        </div>

        <img src="{{ $class->course->thumbnail_url ?? 'https://via.placeholder.com/400x300' }}"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
            alt="{{ $class->title }}">
        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
    </div>

    {{-- Info Konten --}}
    <div class="p-5 flex flex-col grow">
        <div class="flex justify-between items-start gap-4">
            <div class="grow">
                <h3
                    class="text-lg font-bold text-jti-primary leading-tight mb-2 group-hover:text-jti-orange transition-colors">
                    {{ $class->title }}
                </h3>

                {{-- Instructor --}}
                <div class="flex items-center gap-2 mb-3">
                    <div
                        class="w-6 h-6 rounded-full bg-jti-primary/5 border border-jti-primary/10 flex items-center justify-center overflow-hidden">
                        <span
                            class="text-[10px] font-bold text-jti-primary uppercase">{{ substr($class->instructor->name, 0, 1) }}</span>
                    </div>
                    <span class="text-xs font-medium text-gray-500">{{ $class->instructor->name }}</span>
                </div>

                {{-- Tanggal --}}
                <div class="flex items-center gap-1.5 text-[11px] text-gray-400 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ $class->start_date->format('d M') }} - {{ $class->end_date->format('d M Y') }}</span>
                </div>
            </div>

            {{-- Harga --}}
            <div class="text-right shrink-0">
                <div class="text-lg font-bold text-jti-primary tracking-tight">
                    {{ $class->price > 0 ? 'Rp' . number_format($class->price, 0, ',', '.') : 'Gratis' }}
                </div>
            </div>
        </div>

        {{-- Meta Data & Progress Bar --}}
        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center gap-4">
            <div class="flex flex-col shrink-0">
                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Kapasitas</span>
                <span
                    class="text-xs font-bold text-gray-700">{{ $class->current_enrolled }}/{{ $class->max_capacity }}</span>
            </div>

            @php
                $percentage = ($class->current_enrolled / $class->max_capacity) * 100;
                // Logika 3 Warna
                $barColor = match (true) {
                    $percentage >= 90 => 'bg-red-500', // Hampir Penuh
                    $percentage >= 50 => 'bg-amber-500', // Mulai Terisi
                    default => 'bg-emerald-500', // Masih Luas
                };
            @endphp

            <div class="grow h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full {{ $barColor }} transition-all duration-1000"
                    style="width: {{ $percentage }}%"></div>
            </div>

            {{-- Icon Indikator klik (Opsional untuk UX) --}}
            <div class="text-gray-300 group-hover:text-jti-orange transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </div>
</a>

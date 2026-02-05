@props(['course'])

<div
    class="group flex flex-col sm:flex-row bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl hover:shadow-jti-primary/5 transition-all duration-500">
    {{-- Thumbnail --}}
    <div class="sm:w-64 h-44 shrink-0 overflow-hidden relative">
        <img src="{{ $course->thumbnail_url }}"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
    </div>

    {{-- Info Konten --}}
    <div class="p-5 flex flex-col grow">
        <div class="flex justify-between items-start gap-4">
            <div class="grow">
                <h3
                    class="text-lg font-black text-jti-primary leading-tight mb-1 group-hover:text-jti-orange transition-colors">
                    {{ $course->title }}
                </h3>
                <p class="text-xs text-gray-500 line-clamp-2 mb-3 max-w-2xl">
                    {{ $course->description }}
                </p>
            </div>

            {{-- Harga di Pojok Kanan --}}
            <div class="text-right shrink-0">
                <div class="text-lg font-black text-jti-primary">
                    {{ $course->price > 0 ? 'Rp' . number_format($course->price, 0, ',', '.') : 'Gratis' }}
                </div>
            </div>
        </div>

        {{-- Meta Data --}}
        <div class="mt-auto flex flex-wrap items-center gap-y-2 gap-x-4">
            {{-- Rating --}}
            <div class="flex items-center gap-1">
                <span class="text-sm font-black text-jti-orange">{{ number_format($course->avg_rating, 1) }}</span>

                {{-- Memanggil komponen bintang yang baru dibuat --}}
                <x-course.stars :rating="$course->avg_rating" />

                <span class="text-[10px] text-gray-400">({{ $course->reviews_count }})</span>
            </div>

            <div class="flex items-center gap-4 text-sm text-gray-500 font-medium">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" />
                    </svg>
                    22.5 total jam
                </span>
                <span class="px-2 py-0.5 bg-gray-100 rounded text-jti-primary font-black uppercase tracking-tighter">
                    {{ $course->level }}
                </span>
            </div>
        </div>
    </div>
</div>

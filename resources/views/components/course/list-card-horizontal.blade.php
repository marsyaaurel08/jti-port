@props(['course'])

<a href="/courses/{{ $course->slug }}"
    class="group flex flex-col sm:flex-row bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-xl hover:shadow-jti-primary/5 transition-all duration-500">
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
                    class="text-lg font-semibold text-jti-primary leading-tight mb-1 group-hover:text-jti-orange transition-colors">
                    {{ $course->title }}
                </h3>
                <p class="text-xs text-gray-500 line-clamp-2 mb-3 max-w-2xl">
                    {{ $course->description }}
                </p>
            </div>

            {{-- Harga di Pojok Kanan --}}
            <div class="text-right shrink-0">
                <div class="text-lg font-semibold text-jti-primary">
                    {{ $course->price > 0 ? 'Rp' . number_format($course->price, 0, ',', '.') : 'Gratis' }}
                </div>
            </div>
        </div>

        {{-- Meta Data --}}
        <div class="mt-auto flex flex-wrap items-center gap-y-2 gap-x-4">
            {{-- Rating --}}
            <div class="flex items-center gap-1">
                <span class="text-sm font-semibold text-jti-orange">{{ number_format($course->avg_rating, 1) }}</span>

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
                @php
                    $levelColor = match ($course->level) {
                        'beginner' => 'bg-green-100 text-green-700 border-green-200',
                        'intermediate' => 'bg-blue-100 text-blue-700 border-blue-200',
                        'advanced' => 'bg-red-100 text-red-700 border-red-200',
                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                    };
                @endphp

                <span
                    class="px-3 py-1 {{ $levelColor }} rounded-lg border text-xs capitalize tracking-widest shadow-sm">
                    {{ $course->level }}
                </span>
            </div>
        </div>
    </div>
</a>

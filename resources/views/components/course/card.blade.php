@props(['course'])

<div class="group bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full">
    <div class="relative overflow-hidden">
        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-full h-44 object-cover transition-transform duration-500 group-hover:scale-110">
        <div class="absolute inset-0 bg-jti-primary/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>

        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider text-jti-primary shadow-sm">
            {{ $course->level }}
        </span>
    </div>

    <div class="p-4 flex flex-col flex-grow">
        <h3 class="font-bold text-jti-primary text-md line-clamp-2 mb-1 group-hover:text-jti-maroon transition-colors">
            {{ $course->title }}
        </h3>
        <p class="text-xs text-gray-500 mb-2">{{ $course->instructor_name }}</p>

        <div class="flex items-center gap-1 mb-3">
            <span class="text-sm font-black text-jti-orange">{{ $course->avg_rating }}</span>
            <div class="flex text-jti-yellow">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-3 h-3 {{ $i <= round($course->avg_rating) ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
            </div>
            <span class="text-[10px] text-gray-400">({{ $course->reviews_count }})</span>
        </div>

        <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between">
            <div class="font-black text-lg text-jti-primary">
                {{ $course->price > 0 ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'Gratis' }}
            </div>
            <button class="p-2 rounded-lg bg-jti-light text-jti-primary hover:bg-jti-primary hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </button>
        </div>
    </div>
</div>

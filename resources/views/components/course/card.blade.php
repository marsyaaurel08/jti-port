@props(['course'])

<div class="h-full" x-data="{ isLoading: false }">
    <div class="relative h-full">
        <div
            class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-[0_20px_50px_rgba(20,33,61,0.12)] transition-all duration-500 flex flex-col h-full relative z-30 font-sans">

            {{-- Image Section --}}
            <div class="relative h-40 shrink-0 overflow-hidden">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>

            {{-- Content Section --}}
            <div class="p-4 flex flex-col grow">
                {{-- Instruktur --}}
                <p class="text-[11px] text-gray-500 font-medium mb-1">
                    {{ Str::limit($course->instructor_name ?? 'Instruktur JTI', 30) }}
                </p>

                {{-- Judul --}}
                <h3
                    class="font-bold text-jti-primary text-[15px] line-clamp-2 mb-1.5 leading-tight group-hover:text-jti-maroon transition-colors">
                    {{ $course->title }}
                </h3>

                {{-- Deskripsi Singkat --}}
                <p class="text-xs text-gray-400 line-clamp-2 mb-3 leading-relaxed">
                    {{ $course->description }}
                </p>

                {{-- Rating & Level Section --}}
                <div class="flex items-center justify-between flex-wrap gap-2 mb-4 mt-auto">
                    {{-- Rating ala Udemy --}}
                    <div class="flex items-center gap-1">
                        <span
                            class="text-sm font-bold text-jti-maroon">{{ number_format($course->avg_rating, 1) }}</span>
                        @if (view()->exists('components.course.stars'))
                            <x-course.stars :rating="$course->avg_rating" />
                        @endif
                        <span
                            class="text-[10px] text-gray-400">({{ number_format($course->enrollments_count ?? 0) }})</span>
                    </div>

                    {{-- Level Badge (Pindah ke sini) --}}
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

                {{-- Price & Action Section --}}
                <div class="pt-3 border-t border-gray-50">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-bold text-[16px] text-jti-primary">
                            {{ $course->price > 0 ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'Free' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-2">
                        @if (
                            !auth()->check() ||
                                !auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar']))
                            <button type="button"
                                @click="addToCart({{ $course->id }}, () => { isLoading = false; }); isLoading = true;"
                                :disabled="isLoading"
                                class="col-span-1 flex items-center justify-center p-2 rounded-lg bg-gray-50 text-gray-400 hover:bg-jti-light hover:text-jti-primary transition-all border border-gray-100 active:scale-90 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-gray-50 disabled:hover:text-gray-400"
                                title="Tambah ke Keranjang">
                                <!-- Cart Icon -->
                                <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>

                                <!-- Loading Spinner -->
                                <svg x-show="isLoading" x-cloak class="w-5 h-5 animate-spin" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </button>
                        @endif

                        <a href="{{ route('courses.show', $course->slug) }}"
                            class="relative {{ auth()->check() &&auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar'])? 'col-span-5': 'col-span-4' }} group/btn overflow-hidden flex items-center justify-center bg-jti-yellow text-jti-primary py-2.5 rounded-lg transition-all duration-500 hover:shadow-lg active:scale-95 text-sm font-bold tracking-wider">
                            <div
                                class="absolute inset-0 w-full h-full bg-jti-primary opacity-0 group-hover/btn:opacity-100 transition-opacity duration-500">
                            </div>
                            <span
                                class="relative z-10 flex items-center gap-1 group-hover/btn:text-white transition-colors duration-300">
                                Lihat Detail <svg class="w-3 h-3 transition-transform group-hover/btn:translate-x-1"
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
    </div>
</div>

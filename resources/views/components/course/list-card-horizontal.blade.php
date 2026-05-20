@props(['course'])

<a href="/courses/{{ $course->slug }}">
    <div
        class="group flex flex-col md:flex-row bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-jti-primary/30 hover:shadow-xl transition-all duration-500 relative">

        {{-- Thumbnail Section --}}
        <div class="md:w-72 h-52 shrink-0 overflow-hidden relative">
            <a href="/courses/{{ $course->slug }}" class="block h-full">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">

                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors duration-500"></div>
            </a>

            {{-- Badge Level di Thumbnail sudah dihapus agar lebih bersih --}}
        </div>

        {{-- Content Section --}}
        <div class="p-6 flex flex-col grow relative">
            <div class="flex justify-between items-start gap-4 mb-3">
                <div class="grow">
                    <a href="/courses/{{ $course->slug }}"
                        class="text-lg md:text-xl font-bold text-slate-800 leading-snug group-hover:text-jti-primary transition-colors duration-300">
                        {{ $course->title }}
                    </a>
                </div>

                {{-- Price --}}
                <div class="shrink-0 text-right">
                    <span class="text-lg font-bold text-jti-primary tracking-tight">
                        {{ $course->price > 0 ? 'Rp. ' . number_format($course->price, 0, ',', '.') : 'Gratis' }}
                    </span>
                </div>
            </div>

            <p class="text-sm text-slate-500 line-clamp-2 mb-6 leading-relaxed">
                {{ $course->description }}
            </p>

            {{-- Footer Meta & Stats --}}
            <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-5">
                    {{-- Rating Stat --}}
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Rating</span>
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="w-4 h-4 text-amber-400 fill-amber-400">
                                <path
                                    d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />
                            </svg>
                            <span
                                class="text-sm font-bold text-slate-700">{{ number_format($course->avg_rating, 1) }}</span>
                        </div>
                    </div>

                    {{-- Level Stat (Pindahan dari Thumbnail) --}}
                    <div class="flex flex-col border-l border-slate-100 pl-4">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Level</span>
                        <span
                            class="text-sm font-bold {{ match (strtolower($course->level)) {
                                'beginner' => 'text-emerald-600',
                                'intermediate' => 'text-amber-600',
                                'advanced' => 'text-rose-600',
                                default => 'text-slate-600',
                            } }}">
                            {{ ucfirst($course->level) }}
                        </span>
                    </div>

                    {{-- Duration Stat --}}
                    <div class="flex flex-col border-l border-slate-100 pl-4">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Durasi</span>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6v6l4 2" />
                            </svg>
                            <span class="text-sm font-bold text-slate-700">
                                {{ floor($course->total_duration / 60) }}j {{ $course->total_duration % 60 }}m
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Minimalist Cart Button --}}
                {{-- @auth
                    @if (!auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar']))
                        <div x-data="{ isLoading: false }">
                            <button @click="addToCart({{ $course->id }}, () => { isLoading = false }); isLoading = true;"
                                :disabled="isLoading"
                                class="group/cart relative flex items-center justify-center w-11 h-11 rounded-xl bg-slate-50 text-slate-400 hover:bg-jti-yellow/20 hover:text-jti-primary transition-all duration-300 active:scale-90 border border-slate-100 shadow-sm"
                                title="tambah ke keranjang">

                                <div x-show="!isLoading" class="relative flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="w-5 h-5 transition-transform duration-300 group-hover/cart:-rotate-12">
                                        <circle cx="8" cy="21" r="1" />
                                        <circle cx="19" cy="21" r="1" />
                                        <path
                                            d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                    </svg>
                                </div>

                                <div x-show="isLoading" class="animate-spin flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 animate-spin">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                    @endif
                @endauth --}}
            </div>
        </div>
    </div>
</a>

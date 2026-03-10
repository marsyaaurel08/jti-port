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
                            <i data-lucide="star" class="w-4 h-4 text-amber-400 fill-amber-400"></i>
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
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400" style="stroke-width: 2px;"></i>
                            <span class="text-sm font-bold text-slate-700">
                                {{ floor($course->total_duration / 60) }}j {{ $course->total_duration % 60 }}m
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Minimalist Cart Button --}}
                @auth
                    @if (!auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar']))
                        <div x-data="{ isLoading: false }">
                            <button @click="addToCart({{ $course->id }}, () => { isLoading = false }); isLoading = true;"
                                :disabled="isLoading"
                                class="group/cart relative flex items-center justify-center w-11 h-11 rounded-xl bg-slate-50 text-slate-400 hover:bg-jti-yellow/20 hover:text-jti-primary transition-all duration-300 active:scale-90 border border-slate-100 shadow-sm"
                                title="tambah ke keranjang">

                                <div x-show="!isLoading" class="relative flex items-center justify-center">
                                    <i data-lucide="shopping-cart"
                                        class="w-5 h-5 transition-transform duration-300 group-hover/cart:-rotate-12"
                                        style="stroke-width: 1.8px;"></i>
                                </div>

                                <div x-show="isLoading" class="animate-spin flex items-center justify-center">
                                    <i data-lucide="loader-2" class="w-5 h-5" style="stroke-width: 2px;"></i>
                                </div>
                            </button>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</a>

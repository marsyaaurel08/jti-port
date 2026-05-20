@props(['class'])
<a href="{{ route('classes.show', $class->slug) }}">
    <div
        class="group flex flex-col md:flex-row bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-jti-primary/30 hover:shadow-xl transition-all duration-500 relative">

        {{-- Thumbnail Section --}}
        <div class="md:w-72 h-54 shrink-0 overflow-hidden relative">
            <a href="{{ route('classes.show', $class->slug) }}" class="block h-full">
                <img src="{{ $class->course->thumbnail_url ?? 'https://via.placeholder.com/400x300' }}"
                    alt="{{ $class->title }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">

                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors duration-500"></div>
            </a>

        </div>

        {{-- Content Section --}}
        <div class="p-6 flex flex-col grow relative">
            <div class="flex justify-between items-start gap-4 mb-3">
                <div class="grow">
                    <a href="{{ route('classes.show', $class->slug) }}"
                        class="text-lg md:text-xl font-bold text-slate-800 leading-snug group-hover:text-jti-primary transition-colors duration-300">
                        {{ $class->title }}
                    </a>
                </div>

                {{-- Price --}}
                <div class="shrink-0 text-right">
                    <span class="text-lg font-bold text-jti-primary tracking-tight">
                        {{ $class->price > 0 ? 'Rp. ' . number_format($class->price, 0, ',', '.') : 'Gratis' }}
                    </span>
                </div>
            </div>

            {{-- Meta Info (Instructor & Date) --}}
            <div class="space-y-2 mb-6">
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="w-4 h-4 text-slate-400">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <span class="font-medium">{{ $class->instructor->name ?? 'Instruktur JTI' }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="w-4 h-4 text-slate-400">
                        <path d="M8 2v4" />
                        <path d="M16 2v4" />
                        <rect width="18" height="18" x="3" y="4" rx="2" />
                        <path d="M3 10h18" />
                    </svg>
                    <span class="text-xs font-semibold uppercase tracking-tight">
                        {{ $class->start_date?->format('d M') }} — {{ $class->end_date?->format('d M Y') }}
                    </span>
                </div>
            </div>

            {{-- Stats Row & Minimalist Cart Action --}}
            <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    {{-- Capacity Stat --}}
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Kapasitas</span>
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" class="w-3.5 h-3.5 text-jti-primary">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                            <span
                                class="text-sm /font-bold text-slate-700">{{ $class->current_enrolled ?? 0 }}/{{ $class->max_capacity ?? 0 }}</span>
                        </div>
                    </div>

                    {{-- Level Stat --}}
                    <div class="flex flex-col border-l border-slate-100 pl-4">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Level</span>
                        <span
                            class="text-sm font-bold {{ match (strtolower($class->level)) {
                                'beginner' => 'text-emerald-600',
                                'intermediate' => 'text-amber-600',
                                'advanced' => 'text-rose-600',
                                default => 'text-slate-600',
                            } }}">
                            {{ ucfirst($class->level) }}
                        </span>
                    </div>
                </div>

                {{-- Minimalist Cart Button --}}
                {{-- @auth
                    @if (!auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar']))
                        <div x-data="{ isLoading: false }">
                            <button
                                @click="addToCartClass({{ $class->id }}, () => { isLoading = false }); isLoading = true;"
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

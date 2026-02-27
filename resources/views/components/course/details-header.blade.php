@props(['course'])

<div class="relative bg-[#020617] text-white py-16 lg:py-24 overflow-hidden border-b border-white/[0.03]">
    {{-- Ambient Light (Khas Academix: Menggunakan gradasi lembut untuk kedalaman) --}}
    <div class="absolute top-0 left-0 -translate-y-1/2 -translate-x-1/4 w-[600px] h-[600px] bg-jti-primary/20 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 translate-y-1/2 translate-x-1/4 w-[400px] h-[400px] bg-jti-yellow/5 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-[1440px] mx-auto px-6 lg:px-48 relative z-10">
        <div class="lg:w-3/4">

            {{-- Breadcrumbs - Dibuat lebih tipis & spaced-out --}}
            <nav class="flex items-center gap-3 text-[10px] font-semibold tracking-[0.3em] uppercase text-gray-500 mb-10" data-aos="fade-down">
                <a href="/" class="hover:text-jti-yellow transition-colors duration-300">Home</a>
                <span class="text-white/10">/</span>
                <a href="/courses" class="hover:text-jti-yellow transition-colors duration-300">Courses</a>
                <span class="text-white/10">/</span>
                <span class="text-jti-yellow/80">{{ $course->category->name }}</span>
            </nav>

            {{-- Title - Menggunakan tracking-tighter dan font-bold (bukan black) --}}
            <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold mb-10 leading-[1.1] tracking-tighter text-white" data-aos="fade-up">
                {{ $course->title }}
            </h1>

            {{-- Badge Group --}}
            <div class="flex flex-wrap items-center gap-5 md:gap-8" data-aos="fade-up" data-aos-delay="200">

                {{-- Rating - Glassmorphism style --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-white/[0.03] border border-white/[0.08] rounded-full backdrop-blur-md">
                        <svg class="w-3.5 h-3.5 text-jti-yellow fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <span class="text-sm font-bold text-gray-100">{{ number_format($course->avg_rating, 1) }}</span>
                    </div>
                    <span class="text-[11px] font-medium text-gray-500 uppercase tracking-widest">
                        <span class="text-gray-300">2.1M+</span> Enrolled
                    </span>
                </div>

                {{-- Separator --}}
                <div class="hidden md:block w-px h-4 bg-white/10"></div>

                {{-- Level Badge --}}
                <div class="flex items-center gap-3">
                    @php
                        $levelStyles = match(strtolower($course->level)) {
                            'beginner', 'pemula' => 'border-emerald-500/30 text-emerald-400 bg-emerald-500/5',
                            'intermediate', 'menengah' => 'border-jti-yellow/30 text-jti-yellow bg-jti-yellow/5',
                            'advanced', 'mahir' => 'border-rose-500/30 text-rose-400 bg-rose-500/5',
                            default => 'border-white/20 text-white/60 bg-white/5',
                        };
                    @endphp
                    <span class="text-[10px] font-bold text-gray-600 uppercase tracking-[0.2em]">Difficulty</span>
                    <span class="{{ $levelStyles }} px-4 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wider">
                        {{ $course->level }}
                    </span>
                </div>

                {{-- Last Updated --}}
                <div class="flex items-center gap-2.5 text-[11px] text-gray-500 font-medium uppercase tracking-wider">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-jti-yellow/50"></i>
                    Updated: <span class="text-gray-300">{{ $course->updated_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

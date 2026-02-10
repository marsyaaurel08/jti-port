@props(['course'])

<div class="bg-slate-900 text-white py-16">
    <div class="max-w-[1440px] mx-auto px-6">
        <div class="lg:w-2/3">
            {{-- Breadcrumbs --}}
            <nav class="flex text-sm font-bold text-jti-orange mb-6 capitalize tracking-widest">
                <a href="/" class="underline underline-offset-4 hover:text-white transition-colors">Home</a>
                <span class="mx-2 text-gray-600">/</span>
                <a href="/courses" class="underline underline-offset-4 hover:text-white transition-colors">Courses</a>
                <span class="mx-2 text-gray-600">/</span>
                <a href="/courses/?selectedCategories[0]={{ $course->category->id }}" class="underline underline-offset-4 hover:text-white transition-colors">
                    {{ $course->category->name }}
                </a>
            </nav>

            <h1 class="text-4xl md:text-5xl font-black mb-8 leading-tight">{{ $course->title }}</h1>

            <div class="flex flex-wrap items-center gap-8 text-sm font-bold">
                {{-- Rating --}}
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1 text-jti-yellow bg-jti-yellow/10 px-3 py-1.5 rounded-lg border border-jti-yellow/20">
                        <span class="text-lg font-black">{{ number_format($course->avg_rating, 1) }}</span>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </div>
                    <span class="text-gray-400 font-medium">(2,127,980 ratings)</span>
                </div>

                {{-- Separator --}}
                <div class="hidden md:block w-px h-8 bg-gray-800"></div>

                {{-- MODERN LEVEL BADGE --}}
                <div class="flex items-center gap-3">
                    <span class="text-gray-400 uppercase tracking-widest text-[11px]">Level:</span>
                    @php
                        $levelStyles = match(strtolower($course->level)) {
                            'beginner', 'pemula' => 'bg-green-500 text-white shadow-[0_0_15px_rgba(34,197,94,0.3)]',
                            'intermediate', 'menengah' => 'bg-jti-orange text-white shadow-[0_0_15px_rgba(242,153,74,0.3)]',
                            'advanced', 'mahir' => 'bg-red-600 text-white shadow-[0_0_15px_rgba(220,38,38,0.3)]',
                            default => 'bg-gray-600 text-white',
                        };
                    @endphp
                    <span class="{{ $levelStyles }} px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-widest transition-transform  cursor-default">
                        {{ $course->level }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

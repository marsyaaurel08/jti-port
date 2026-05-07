@props(['class'])

<div class="relative bg-slate-950 text-white py-12 lg:py-20 overflow-hidden border-b border-white/5">
    {{-- Decorative Background (Optional: Memberikan kesan modern) --}}
    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[500px] h-[500px] bg-jti-primary/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-[1440px] mx-auto px-6 lg:px-48 relative z-10">
        <div class="lg:w-2/3">
            {{-- Breadcrumbs - Lebih slim & minimalis --}}
            <nav class="flex items-center gap-2 text-[10px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-8">
                <a href="/" class="hover:text-jti-orange transition-colors">Home</a>
                <span class="text-gray-700">/</span>
                <a href="/classes" class="hover:text-jti-orange transition-colors">Classes</a>
                <span class="text-gray-700">/</span>
                <a href="/classes/?selectedCategories[0]={{ $class->course->category->id }}" class="text-jti-orange/80 hover:text-jti-orange transition-colors">
                    {{ $class->course->category->name }}
                </a>
            </nav>

            {{-- Title - Menggunakan font-bold (bukan black) & tracking tight --}}
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-8 leading-[1.15] tracking-tight text-white drop-shadow-sm">
                {{ $class->title }}
            </h1>

            {{-- Stats & Level --}}
            <div class="flex flex-wrap items-center gap-6">
                {{-- Level Badge --}}
                <div class="flex items-center gap-3">
                    @php
                        $levelStyles = match(strtolower($class->level)) {
                            'beginner', 'pemula' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'intermediate', 'menengah' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            'advanced', 'mahir' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                            default => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                        };
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Level</span>
                        <span class="{{ $levelStyles }} px-3 py-1 rounded-md border text-[10px] font-bold uppercase tracking-tighter">
                            {{ $class->level }}
                        </span>
                    </div>
                </div>

                {{-- Last Updated (Opsional but recommended for modern UI) --}}
                <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Update Terakhir: {{ $class->updated_at->format('M Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

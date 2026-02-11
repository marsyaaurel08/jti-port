{{-- resources/views/components/navbar/explore-menu.blade.php --}}
<div class="hidden md:block relative" @mouseenter="openExplore = true" @mouseleave="openExplore = false">
    <button :class="openExplore ? 'text-jti-maroon' : 'text-gray-700'"
        class="text-sm font-bold hover:text-jti-maroon px-2 py-7 transition-colors duration-200 flex items-center gap-1 group">
        Explore
        <svg :class="openExplore ? 'rotate-180 text-jti-maroon' : 'text-gray-400'"
             class="w-4 h-4 transition-transform duration-200 group-hover:text-jti-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    {{-- Dropdown Container --}}
    <div x-show="openExplore"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute left-0 w-80 bg-white border border-gray-100 shadow-[0_20px_50px_rgba(0,0,0,0.1)] py-4 z-50 rounded-2xl overflow-hidden">

        <div class="px-5 pb-3 text-[10px] text-gray-400 tracking-[0.2em] font-semibold uppercase">Pilih Mode Belajar</div>

        <div class="px-3 space-y-1">
            {{-- Option 1: Kursus Mandiri --}}
            <a href="{{ route('courses.index') }}"
               class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition-all group">
                <div class="w-10 h-10 shrink-0 bg-jti-primary/5 text-jti-primary rounded-lg flex items-center justify-center group-hover:bg-jti-primary group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-jti-primary group-hover:text-jti-maroon transition-colors">Semua Kursus</p>
                    <p class="text-[11px] text-gray-400 leading-relaxed mt-0.5">Belajar mandiri dengan video materi berkualitas.</p>
                </div>
            </a>

            {{-- Option 2: Kelas Pelatihan --}}
            <a href="{{ route('classes.index') }}"
               class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition-all group">
                <div class="w-10 h-10 shrink-0 bg-jti-orange/10 text-jti-orange rounded-lg flex items-center justify-center group-hover:bg-jti-orange group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-jti-primary group-hover:text-jti-maroon transition-colors">Kelas Pelatihan</p>
                    <p class="text-[11px] text-gray-400 leading-relaxed mt-0.5">Belajar dengan instruktur berpengalaman.</p>
                </div>
            </a>
        </div>
    </div>
</div>

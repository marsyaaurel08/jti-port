{{-- Empty State when no classes found --}}
<div
    class="py-32 px-6 text-center border-2 border-dashed border-slate-200 rounded-[2rem] bg-slate-50/50 relative overflow-hidden">
    {{-- Decorative Background Elements --}}
    <div class="absolute top-0 left-0 w-24 h-24 bg-jti-primary/5 rounded-full -translate-x-12 -translate-y-12"></div>
    <div class="absolute bottom-0 right-0 w-32 h-32 bg-jti-orange/5 rounded-full translate-x-16 translate-y-16"></div>

    <div class="relative z-10">
        {{-- Animated Icon Container --}}
        <div
            class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white shadow-xl shadow-slate-200/50 mb-6 group transition-transform duration-500 hover:scale-110">
            <div class="absolute inset-0 bg-gradient-to-br from-jti-primary/10 to-transparent rounded-2xl"></div>
            <svg class="w-10 h-10 text-slate-300 group-hover:text-jti-primary transition-colors duration-300"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
            </svg>
        </div>

        {{-- Text Content --}}
        <h3 class="text-xl font-extrabold text-slate-800 mb-2 tracking-tight">
            Ups! Kelas tidak ditemukan
        </h3>
        <p class="text-slate-500 max-w-sm mx-auto leading-relaxed">
            Kami tidak dapat menemukan kelas yang sesuai dengan kata kunci atau filter Anda. Coba gunakan istilah yang
            lebih umum.
        </p>

        {{-- Action Button (Optional) --}}
        <div class="mt-8">
            <button onclick="window.location.reload()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 hover:border-jti-primary/30 hover:text-jti-primary transition-all duration-300 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset Pencarian
            </button>
        </div>
    </div>
</div>

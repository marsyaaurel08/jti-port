@props(['course'])
<div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transition-all hover:shadow-jti-primary/5">
    <div class="relative group">
        <img src="{{ $course->thumbnail_url }}" class="w-full aspect-video object-cover" alt="Thumbnail">
        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-jti-primary shadow-2xl">
                <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4.5 3L15.5 10L4.5 17V3Z"/></svg>
            </div>
        </div>
    </div>

    <div class="p-8">
        <div class="text-4xl font-black text-jti-primary mb-6">
            Rp{{ number_format($course->price, 0, ',', '.') }}
        </div>

        <div class="space-y-3">
            <button class="w-full py-4 bg-jti-primary text-white font-black rounded-xl hover:bg-jti-maroon transition-all shadow-lg shadow-jti-primary/20">
                Beli Sekarang
            </button>
            <button class="w-full py-4 border-2 border-jti-primary text-jti-primary font-black rounded-xl hover:bg-jti-light transition-all">
                Tambah ke Keranjang
            </button>
        </div>

        <div class="mt-8 space-y-4">
            <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Sudah termasuk:</p>
            <ul class="text-sm font-bold text-gray-600 space-y-3">
                <li class="flex items-center gap-3 italic">
                    <svg class="w-5 h-5 text-jti-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Akses Selamanya
                </li>
                <li class="flex items-center gap-3 italic">
                    <svg class="w-5 h-5 text-jti-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Sertifikat Penyelesaian
                </li>
            </ul>
        </div>
    </div>
</div>

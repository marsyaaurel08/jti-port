@props(['course'])

{{-- Video Preview Section --}}
<div class="relative group cursor-pointer overflow-hidden rounded-t-2xl">
    <img src="{{ $course->thumbnail_url }}"
        class="w-full aspect-video object-cover transition-transform duration-500 group-hover:scale-110" alt="Thumbnail">
</div>

<div class="p-8">
    {{-- Pricing Section --}}
    <div class="flex items-baseline gap-3 mb-8">
        <span class="text-4xl font-black text-jti-primary">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
        @if ($course->old_price)
            <div class="flex flex-col">
                <span
                    class="text-gray-400 line-through text-sm font-bold italic">Rp{{ number_format($course->old_price, 0, ',', '.') }}</span>
                <span class="text-jti-orange text-[10px] font-black uppercase tracking-tighter">Hemat 85%</span>
            </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col gap-3 mb-8">
        <form action="{{ route('cart.add', $course->id) }}" method="POST">
            @csrf
            <button
                class="w-full py-4 bg-jti-primary text-white font-black rounded-xl hover:bg-jti-maroon transition-all shadow-lg shadow-jti-primary/20 active:scale-[0.98]">
                Beli Sekarang
            </button>
        </form>

        <button
            class="w-full py-4 border-2 border-jti-primary text-jti-primary font-black rounded-xl hover:bg-jti-light transition-all active:scale-[0.98]">
            Tambahkan ke Keranjang
        </button>
    </div>

    {{-- Share & Coupon Section - Penataan Lebih Rapi --}}
    <div class="grid grid-cols-1 gap-6 mb-8 pt-6 border-t border-gray-100">
        {{-- Tombol Share Full Width agar lebih proporsional --}}
        <button
            class="w-full flex items-center justify-center gap-3 py-3 text-sm font-black text-gray-600 hover:text-jti-primary transition-all border border-gray-100 rounded-xl hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 100-2.684 3 3 0 000 2.684zm0 9.316a3 3 0 100-2.684 3 3 0 000 2.684z" />
            </svg>
            Bagikan Kursus Ini
        </button>

        {{-- Coupon Input --}}
        <div x-data="{ openCoupon: false }" class="w-full text-center">
            <button @click="openCoupon = !openCoupon"
                class="text-xs font-black text-jti-primary border-b-2 border-dotted border-jti-primary/30 hover:border-jti-primary transition-all uppercase tracking-widest">
                Gunakan Kode Kupon
            </button>

            <div x-show="openCoupon" x-transition class="mt-4 flex gap-2">
                <input type="text" placeholder="KODE123"
                    class="flex-1 px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-jti-primary/10 focus:border-jti-primary transition-all uppercase font-bold">
                <button
                    class="px-5 py-2 bg-slate-900 text-white text-xs font-black rounded-lg hover:bg-black transition-all">
                    Apply
                </button>
            </div>
        </div>
    </div>

    {{-- Features List --}}
    <div class="space-y-4">
        <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Materi yang didapatkan:</p>
        <ul class="space-y-4 text-sm font-bold text-gray-600">
            <li class="flex items-start gap-4 italic leading-tight">
                <svg class="w-5 h-5 text-jti-orange shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                </svg>
                {{ $course->total_duration }} Total durasi video pembelajaran
            </li>
            <li class="flex items-start gap-4 italic leading-tight">
                <svg class="w-5 h-5 text-jti-orange shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                {{ $course->chapters_count }} Bab pembelajaran terstruktur
            </li>
            <li class="flex items-start gap-4 italic leading-tight">
                <svg class="w-5 h-5 text-jti-orange shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                E-Sertifikat resmi dari JTIport
            </li>
            <li class="flex items-start gap-4 italic leading-tight text-jti-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Akses selamanya tanpa batas waktu
            </li>
        </ul>
    </div>
</div>

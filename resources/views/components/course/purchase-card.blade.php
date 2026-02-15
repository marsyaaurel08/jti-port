@props(['course'])

{{-- Video Preview Section --}}
<div class="relative group cursor-pointer overflow-hidden rounded-t-xl">
    <img src="{{ $course->thumbnail_url }}"
        class="w-full aspect-video object-cover transition-transform duration-700 group-hover:scale-105" alt="Thumbnail">
    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/30 transition-colors flex items-center justify-center">
        <div class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5 text-jti-primary fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </div>
    </div>
</div>

<div class="p-5 bg-white rounded-b-xl border-x border-b border-gray-100 shadow-sm">

    {{-- Price & Share Section --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex flex-col">
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold tracking-tight text-gray-900">
                    Rp{{ number_format($course->price, 0, ',', '.') }}
                </span>
                @if ($course->old_price)
                    <span class="text-gray-400 line-through text-xs font-medium">
                        Rp{{ number_format($course->old_price, 0, ',', '.') }}
                    </span>
                @endif
            </div>
            @if ($course->old_price)
                <span class="text-[9px] font-bold text-jti-orange uppercase tracking-wider">Hemat 85%</span>
            @endif
        </div>

        <button title="Bagikan" class="p-2.5 text-gray-400 hover:text-jti-primary hover:bg-gray-50 rounded-lg border border-gray-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 100-2.684 3 3 0 000 2.684zm0 9.316a3 3 0 100-2.684 3 3 0 000 2.684z" />
            </svg>
        </button>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col gap-2 mb-6">
        <form action="{{ route('cart.add', $course->id) }}" method="POST">
            @csrf
            <button class="w-full py-3 bg-jti-primary text-white font-bold rounded-lg hover:bg-jti-primary/90 transition-all text-sm tracking-wide">
                Beli Sekarang
            </button>
        </form>

        <button class="w-full py-3 border border-gray-200 text-gray-600 font-semibold rounded-lg hover:bg-gray-50 transition-all text-sm">
            Tambah Keranjang
        </button>
    </div>

    {{-- Coupon Section --}}
    <div x-data="{ openCoupon: false }" class="mb-6 pt-4 border-t border-gray-50">
        <button @click="openCoupon = !openCoupon"
            class="w-full text-center text-[10px] font-bold text-gray-400 hover:text-jti-primary uppercase tracking-widest transition-colors">
            Punya Kode Kupon?
        </button>

        <div x-show="openCoupon" x-transition class="mt-3 flex gap-2">
            <input type="text" placeholder="KODE"
                class="flex-1 px-3 py-2 text-xs border border-gray-200 rounded-md focus:border-jti-primary outline-none uppercase font-semibold">
            <button class="px-4 py-2 bg-gray-900 text-white text-[10px] font-bold rounded-md hover:bg-black transition-colors uppercase">
                Apply
            </button>
        </div>
    </div>

    {{-- Features List --}}
    <div class="bg-gray-50/80 p-4 rounded-xl">
        <ul class="space-y-2.5">
            <li class="flex items-center gap-2.5 text-xs text-gray-600 font-medium">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ $course->total_duration }} Durasi Video
            </li>
            <li class="flex items-center gap-2.5 text-xs text-gray-600 font-medium">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" /></svg>
                {{ $course->chapters_count }} Bab Materi
            </li>
            <li class="flex items-center gap-2.5 text-xs text-jti-primary font-bold">
                <svg class="w-4 h-4 text-jti-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                Akses Selamanya
            </li>
        </ul>
    </div>
</div>

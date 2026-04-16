<x-layouts.user :user="$this->user">
    @if (empty($cartItems))
        {{-- Empty State: Lebih minimalis --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Keranjang Saya</h1>
            <p class="text-gray-600 mt-2">Lihat semua item yang telah Anda tambahkan</p>
        </div>
        <div class="bg-white rounded-[2rem] border border-slate-100 p-16 text-center shadow-sm">
            <div class="relative inline-flex mb-6">
                {{-- Subtle Glow Kuning --}}
                <div class="absolute inset-0 bg-jti-yellow/20 rounded-full blur-2xl"></div>
                <div class="relative w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" class="w-10 h-10">
                        <path d="m15 11-1 9" />
                        <path d="m19 11-4-7" />
                        <path d="M2 11h20" />
                        <path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4" />
                        <path d="M4.5 15.5h15" />
                        <path d="m5 11 4-7" />
                        <path d="m9 11 1 9" />
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">Keranjang belanja anda masih kosong</h3>
            <p class="text-slate-500 max-w-xs mx-auto mb-8 text-sm leading-relaxed font-medium">
                Temukan materi sertifikasi yang sesuai untuk meningkatkan karir anda sekarang.
            </p>
            <a href="{{ route('courses.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-jti-primary text-white text-sm font-medium rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95 group">
                Jelajahi kursus
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4 group-hover:translate-x-1 transition-transform">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                </svg>
            </a>
        </div>
    @else
        <div class="space-y-6">
            {{-- Header --}}
            <div class="px-2">
                <h2 class="text-2xl font-bold text-jti-primary tracking-tight">Keranjang Belanja</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">Selesaikan pembayaran per item untuk akses instan</p>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
                <div class="divide-y divide-slate-50">
                    @foreach ($cartItems as $item)
                        @php
                            $isClass = !is_null($item['class_id']);
                            $courseOrClass = $isClass ? $item['class'] : $item['course'];
                            $slug = $courseOrClass['slug'] ?? '';
                            $routeName = $isClass ? 'checkout.class' : 'checkout.course';
                        @endphp

                        <div wire:key="{{ $item['id'] }}"
                            class="group p-6 sm:p-8 flex flex-col md:flex-row items-center gap-6 hover:bg-slate-50/50 transition-all duration-300">

                            {{-- Thumbnail dengan Badge Status --}}
                            <div class="relative shrink-0">
                                <div
                                    class="w-40 h-24 overflow-hidden rounded-2xl bg-slate-100 border border-slate-200/50 shadow-sm">
                                    <img src="{{ $courseOrClass['thumbnail_url'] ?? 'https://via.placeholder.com/150' }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                </div>
                                {{-- Floating Badge --}}
                                <div class="absolute -top-2 -left-2 shadow-sm">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                                {{ $isClass ? 'bg-amber-400 text-jti-primary' : 'bg-jti-primary text-white' }}">
                                        {{ $isClass ? 'Kelas' : 'Course' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Konten --}}
                            <div class="flex-1 min-w-0 text-center md:text-left">
                                <h3
                                    class="text-lg font-bold text-slate-900 leading-snug group-hover:text-jti-primary transition-colors line-clamp-2">
                                    {{ $courseOrClass['title'] }}
                                </h3>

                                <div class="flex items-center justify-center md:justify-start gap-3 mt-3">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Harga</span>
                                        <span class="text-lg font-extrabold text-jti-primary">
                                            Rp {{ number_format($courseOrClass['price'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions Group --}}
                            <div
                                class="shrink-0 flex items-center gap-4 w-full md:w-auto border-t md:border-0 pt-4 md:pt-0">
                                {{-- Tombol Hapus: Lebih subtle --}}
                                <button wire:click="removeItem({{ $item['id'] }})" wire:loading.attr="disabled"
                                    title="Hapus dari keranjang"
                                    class="flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all active:scale-90">

                                    <div wire:loading.remove wire:target="removeItem({{ $item['id'] }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                            <path d="M10 11v6" />
                                            <path d="M14 11v6" />
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                            <path d="M3 6h18" />
                                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                        </svg>
                                    </div>
                                    <div wire:loading wire:target="removeItem({{ $item['id'] }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 animate-spin">
                                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                        </svg>
                                    </div>
                                </button>

                                {{-- Tombol Checkout: Lebih menonjol --}}
                                <a href="{{ route("student.$routeName", $slug) }}"
                                    class="flex-1 md:flex-none flex items-center justify-center gap-3 px-8 py-3.5 bg-jti-yellow text-jti-primary font-bold rounded-2xl hover:bg-jti-yellow/90 hover:shadow-xl hover:shadow-jti-yellow/20 transition-all active:scale-95 whitespace-nowrap">
                                    <span>Bayar Sekarang</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                        <path d="m9 18 6-6-6-6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</x-layouts.user>

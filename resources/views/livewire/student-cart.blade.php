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
                    <x-lucide-shopping-cart class="absolute w-6 h-6 text-jti-yellow" />
                </div>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">Keranjang belanja anda masih kosong</h3>
            <p class="text-slate-500 max-w-xs mx-auto mb-8 text-sm leading-relaxed font-medium">
                Temukan materi sertifikasi yang sesuai untuk meningkatkan karir anda sekarang.
            </p>
            <a href="{{ route('courses.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-jti-primary text-white text-sm font-medium rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95 group">
                Jelajahi kursus
                <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
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
                            $isDeleted = isset($courseOrClass['deleted_at']) && !is_null($courseOrClass['deleted_at']);
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
                                        <x-lucide-trash class="w-5 h-5" />
                                    </div>
                                    <div wire:loading wire:target="removeItem({{ $item['id'] }})">
                                        <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                    </div>
                                </button>

                                {{-- Tombol Checkout: Lebih menonjol --}}
                                @if ($isDeleted)
                                    <span
                                        class="flex-1 md:flex-none flex items-center justify-center gap-3 px-8 py-3.5 bg-slate-100 text-jti-primary font-bold rounded-2xl  transition-all active:scale-95 whitespace-nowrap">
                                        Tidak Tersedia
                                    </span>
                                @else
                                    <a href="{{ route("student.$routeName", $slug) }}"
                                        class="flex-1 md:flex-none flex items-center justify-center gap-3 px-8 py-3.5 bg-jti-yellow text-jti-primary font-bold rounded-2xl hover:bg-jti-yellow/90 hover:shadow-xl hover:shadow-jti-yellow/20 transition-all active:scale-95 whitespace-nowrap">
                                        <span>Bayar Sekarang</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</x-layouts.user>

<x-layouts.user :user="$this->user">
    @if (empty($cartItems))
        {{-- Empty State: Lebih minimalis --}}
        <div class="bg-white rounded-[2rem] border border-slate-100 p-16 text-center shadow-sm">
            <div class="relative inline-flex mb-6">
                {{-- Subtle Glow Kuning --}}
                <div class="absolute inset-0 bg-jti-yellow/20 rounded-full blur-2xl"></div>
                <div class="relative w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                    <i data-lucide="shopping-basket" class="w-10 h-10" style="stroke-width: 1.2px;"></i>
                </div>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">Keranjang belanja anda masih kosong</h3>
            <p class="text-slate-500 max-w-xs mx-auto mb-8 text-sm leading-relaxed font-medium">
                Temukan materi sertifikasi yang sesuai untuk meningkatkan karir anda sekarang.
            </p>
            <a href="{{ route('courses.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-jti-primary text-white text-sm font-medium rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95 group">
                Jelajahi kursus
                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    @else
        <div class="space-y-6">
            {{-- Header --}}
            <div class="px-2">
                <h2 class="text-2xl font-semibold text-jti-primary tracking-tight">Keranjang belanja</h2>
                <p class="text-sm text-slate-500 font-medium">Kelola item pilihan anda sebelum melakukan pembayaran</p>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
                <div class="divide-y divide-slate-50">
                    @foreach ($cartItems as $item)
                        @php
                            $course = $item['course'] ?? $item['class'];
                            $thumbnail = $course['thumbnail_url'] ?? null;
                            $isClass = isset($item['class_id']) && $item['class_id'] !== null;
                        @endphp
                        <div wire:key="{{ $item['id'] }}"
                            class="group p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6 hover:bg-slate-50/50 transition-colors">

                            {{-- Thumbnail dengan soft border --}}
                            <div class="relative w-36 h-24 shrink-0 overflow-hidden rounded-xl bg-slate-100 border border-slate-200/50">
                                <img src="{{ $thumbnail ?? 'https://via.placeholder.com/150' }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1.5">
                                    {{-- Badge Tipe: Menggunakan warna kuning sebagai aksen --}}
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $isClass ? 'bg-jti-yellow/10 text-jti-maroon' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $isClass ? 'Live class' : 'Kursus digital' }}
                                    </span>
                                </div>

                                <h3 class="text-base font-semibold text-slate-900 leading-snug group-hover:text-jti-primary transition-colors">
                                    {{ $course['title'] }}
                                </h3>

                                <div class="flex items-center gap-1.5 mt-2">
                                    <span class="text-xs font-medium text-slate-400">Harga:</span>
                                    <span class="text-sm font-semibold text-jti-primary">
                                        Rp {{ number_format($course['price'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0">
                                <button wire:click="removeItem({{ $item['id'] }})"
                                    wire:loading.attr="disabled"
                                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-100 text-slate-300 hover:text-rose-500 hover:bg-rose-50 hover:border-rose-100 transition-all shadow-sm">

                                    <div wire:loading.remove wire:target="removeItem({{ $item['id'] }})">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </div>

                                    <div wire:loading wire:target="removeItem({{ $item['id'] }})">
                                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-rose-500"></i>
                                    </div>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Summary Section: Dominasi Kuning untuk CTA --}}
                <div class="p-8 bg-jti-yellow/5 border-t border-jti-yellow/10 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="text-center sm:text-left">
                        <p class="text-xs font-medium text-slate-500 mb-1">Total pembayaran</p>
                        <p class="text-2xl font-semibold text-jti-primary">
                            Rp {{ number_format(collect($cartItems)->sum(fn($i) => ($i['course']['price'] ?? $i['class']['price'])), 0, ',', '.') }}
                        </p>
                    </div>

                    <button class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-3.5 bg-jti-yellow text-jti-primary font-semibold rounded-xl hover:bg-jti-yellow/90 hover:shadow-lg hover:shadow-jti-yellow/20 transition-all active:scale-95">
                        Lanjutkan ke pembayaran
                        <i data-lucide="arrow-right-circle" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-layouts.user>

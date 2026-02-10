{{-- resources/views/pages/user/cart.blade.php --}}
<x-user.layout.index :user="$user">
    <div class="space-y-8">
        {{-- Header --}}
        <div class="space-y-1">
            <h1 class="text-3xl font-black text-jti-primary tracking-tight">Keranjang Belanja</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-[0.2em]">Selesaikan pembayaran untuk akses
                kursus</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            {{-- Left Side: Items List --}}
            <div class="lg:col-span-2 space-y-4">
                @forelse($cartItems as $item)
                    <div
                        class="group bg-white rounded-3xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col sm:flex-row items-center gap-6">
                        {{-- Course Thumbnail --}}
                        <div class="w-full sm:w-32 h-24 rounded-2xl overflow-hidden flex-shrink-0">
                            <img src="{{ $item->course->thumbnail_url }}" alt="{{ $item->course->title }}"
                                class="w-full h-full object-cover">
                        </div>

                        {{-- Course Info --}}
                        <div class="flex-1 space-y-1 text-center sm:text-left">
                            <span
                                class="text-[10px] font-black text-jti-orange uppercase tracking-widest bg-jti-orange/5 px-2 py-1 rounded">
                                {{ $item->course->category->name }}
                            </span>
                            <h3 class="font-bold text-jti-primary text-base line-clamp-1">{{ $item->course->title }}
                            </h3>
                            <p class="text-xs text-gray-400 font-medium italic">Instruktur:
                                {{ $item->course->instructor->name }}</p>
                        </div>

                        {{-- Price & Action --}}
                        <div
                            class="flex flex-row sm:flex-col items-center sm:items-end gap-4 w-full sm:w-auto border-t sm:border-t-0 pt-4 sm:pt-0">
                            <div class="flex-1 sm:flex-none">
                                <p class="text-lg font-black text-jti-primary leading-none">
                                    Rp{{ number_format($item->course->price, 0, ',', '.') }}</p>
                                @if ($item->course->old_price)
                                    <p class="text-[11px] text-gray-300 line-through mt-1">
                                        Rp{{ number_format($item->course->old_price, 0, ',', '.') }}</p>
                                @endif
                            </div>

                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-2.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all group/btn">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50 rounded-[3rem] p-12 text-center border-2 border-dashed border-gray-100">
                        <p class="text-gray-400 font-bold italic">Keranjang kamu masih kosong.</p>
                        <a href="{{ route('courses.index') }}"
                            class="text-jti-primary text-sm font-black underline mt-2 inline-block">Cari kursus yuk!</a>
                    </div>
                @endforelse
            </div>

            {{-- Right Side: Order Summary --}}
            <div class="lg:col-span-1">
                <div
                    class="bg-jti-primary rounded-[2.5rem] p-8 text-white shadow-xl shadow-jti-primary/20 sticky top-24">
                    <h2 class="text-xl font-black mb-6 flex items-center gap-2 italic">
                        Ringkasan Belanja
                        <span class="h-1 w-8 bg-jti-orange rounded-full"></span>
                    </h2>

                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-sm font-medium text-white/60">
                            <span>Harga Normal</span>
                            <span>Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium text-emerald-400">
                            <span>Diskon</span>
                            <span>-Rp0</span>
                        </div>
                        <div class="pt-4 border-t border-white/10 flex justify-between items-end">
                            <span class="text-sm font-bold">Total Pembayaran</span>
                            <span
                                class="text-2xl font-black text-jti-yellow">Rp{{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Promo Code --}}
                    <div class="mb-8 group">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 block mb-2">Kode
                            Promo (Opsional)</label>
                        <div class="flex gap-2">
                            <input type="text" placeholder="JTIPRO"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-jti-orange/50 transition-all uppercase placeholder:text-white/20 font-bold">
                            <button
                                class="bg-white text-jti-primary px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-jti-yellow transition-all">Cek</button>
                        </div>
                    </div>

                    {{-- <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-jti-orange text-white py-4 rounded-2xl font-black uppercase tracking-[0.15em] hover:bg-white hover:text-jti-primary shadow-lg shadow-jti-orange/20 transition-all duration-300 flex items-center justify-center gap-3">
                            <span>Checkout Sekarang</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </button>
                    </form> --}}

                    <div class="mt-6 flex items-center justify-center gap-3 opacity-40">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Pembayaran Aman &
                            Terenkripsi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user.layout.index>

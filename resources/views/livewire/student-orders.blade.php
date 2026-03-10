<x-layouts.user :user="$this->user">
    @if ($orders->isEmpty())
        {{-- Compact Modern Empty State --}}
        <div class="relative py-16 flex flex-col items-center text-center">
            <div class="absolute inset-0 flex items-center justify-center -z-10 opacity-20">
                <div class="w-48 h-48 bg-jti-yellow rounded-full blur-[100px]"></div>
            </div>

            <div
                class="w-24 h-24 bg-white rounded-[2rem] shadow-xl flex items-center justify-center mb-8 rotate-3 hover:rotate-0 transition-transform duration-500 border border-slate-50">
                <i data-lucide="ghost" class="w-10 h-10 text-slate-300"></i>
            </div>

            <h3 class="text-2xl font-bold text-slate-900 tracking-tight mb-3">Semua beres!</h3>
            <p class="text-sm text-slate-500 font-medium max-w-xs leading-relaxed mb-8">
                Tidak ada tagihan yang menggantung. Saatnya menambah ilmu baru hari ini?
            </p>

            <a href="{{ route('courses.index') }}"
                class="bg-jti-primary text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:shadow-lg transition-all duration-300 active:scale-95">
                Cari Kursus
            </a>
        </div>
    @else
        <div class="space-y-8">
            {{-- Compact Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 px-2">
                <div class="space-y-1">
                    <h2 class="text-2xl font-extrabold text-jti-primary tracking-tight">Pesanan Anda</h2>
                </div>
                <div class="flex items-center gap-2.5 bg-white p-1.5 pr-4 rounded-xl shadow-sm border border-slate-100">
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider leading-none mb-1">Status
                        </p>
                        <p class="text-xs font-bold text-slate-700 leading-none">{{ $orders->count() }} Menunggu</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5">
                @foreach ($orders as $order)
                    <div wire:key="order-{{ $order->id }}"
                        class="group relative bg-white rounded-[2rem] transition-all duration-500 hover:shadow-xl hover:shadow-slate-200/50 border border-slate-100 overflow-hidden">

                        <div class="p-6 md:p-8">
                            {{-- Section: Top Info --}}
                            <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-8">
                                <div class="space-y-2">
                                    <div class="inline-block px-3 py-1 bg-slate-50 rounded-lg border border-slate-100">
                                        <p class="text-[11px] font-bold text-slate-500 font-mono tracking-tight">
                                            {{ $order->invoice_number }}</p>
                                    </div>
                                    <p class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        {{ $order->created_at->format('d M Y • H:i') }} WIB
                                    </p>
                                </div>

                                <div class="md:text-right">
                                    <p class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.15em] mb-1">
                                        Total Amount</p>
                                    <p class="text-2xl font-black text-jti-primary tracking-tight">
                                        <span
                                            class="text-xs font-bold text-slate-300 mr-0.5">Rp</span>{{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Section: Content & CTA --}}
                            <div
                                class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center pt-6 border-t border-slate-50">
                                {{-- Items List --}}
                                <div class="lg:col-span-7 space-y-4">
                                    @foreach ($order->orderItems as $item)
                                        <div class="flex items-center gap-4">
                                            <div class="relative shrink-0">
                                                <img src="{{ $item->course->thumbnail_url ?? $item->class->thumbnail_url }}"
                                                    class="w-16 h-11 object-cover rounded-xl shadow-sm border border-slate-100"
                                                    alt="">
                                            </div>
                                            <div class="min-w-0">
                                                <h4
                                                    class="text-sm font-bold text-slate-700 leading-snug line-clamp-1 group-hover:text-jti-primary transition-colors">
                                                    {{ $item->item_name }}
                                                </h4>
                                                <p class="text-[11px] font-bold text-jti-yellow mt-0.5">
                                                    Rp{{ number_format($item->price_at_purchase, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Mini Action Card --}}
                                <div
                                    class="lg:col-span-5 bg-slate-50/70 rounded-2xl p-4 flex items-center justify-between gap-4 border border-slate-100/50">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 bg-white rounded-xl shadow-sm flex items-center justify-center border border-slate-100">
                                            <i data-lucide="wallet" class="w-4 h-4 text-slate-400"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p
                                                class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">
                                                Method</p>
                                            <p class="text-[11px] font-extrabold text-slate-700 truncate uppercase">
                                                {{ $order->payment_method ? str_replace('_', ' ', $order->payment_method) : '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <button wire:click="viewVAInstructions({{ $order->id }})"
                                        class="shrink-0 px-5 py-2.5 bg-jti-yellow text-jti-primary font-bold text-[11px] rounded-xl hover:shadow-md hover:bg-amber-400 transition-all duration-300 active:scale-95">
                                        Lihat Instruksi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.user>

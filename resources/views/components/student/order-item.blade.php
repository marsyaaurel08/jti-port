@props(['order'])
<div wire:key="order-{{ $order->id }}"
    class="bg-white rrounded-4xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow">

    <div class="p-6 sm:p-8">
        {{-- Order Meta --}}
        <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6 pb-6 border-b border-slate-50">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nomor Invoice</span>
                <p class="text-sm font-mono font-bold text-jti-primary">{{ $order->invoice_number }}</p>
                <p class="text-xs text-slate-400 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="w-3 h-3">
                        <path d="M8 2v4" />
                        <path d="M16 2v4" />
                        <rect width="18" height="18" x="3" y="4" rx="2" />
                        <path d="M3 10h18" />
                    </svg>
                    {{ $order->created_at->format('d M Y • H:i') }} WIB
                </p>
            </div>
            <div class="md:text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Tagihan</span>
                <p class="text-2xl font-bold text-jti-primary">
                    <span
                        class="text-sm font-semibold mr-0.5">Rp</span>{{ number_format($order->total_amount, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 space-y-4">
                @foreach ($order->orderItems as $item)
                    <div class="flex items-center gap-4 group">
                        <div
                            class="relative w-20 h-14 shrink-0 overflow-hidden rounded-lg bg-slate-100 border border-slate-200/50">
                            <img src="{{ $item->course->thumbnail_url ?? ($item->class->thumbnail_url ?? 'https://via.placeholder.com/150') }}"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <div class="flex gap-4">
                                <h4
                                    class="text-sm font-semibold text-slate-800 leading-snug line-clamp-1 group-hover:text-jti-primary transition-colors">
                                    {{ $item->item_name }}
                                </h4>
                                @if ($item->course_id)
                                    <span
                                        class="px-2 py-0.5 rounded-lg text-[8px] font-bold uppercase tracking-wider bg-jti-primary text-white whitespace-nowrap">
                                        Course
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-0.5 rounded-lg text-[8px] font-bold uppercase tracking-wider bg-jti-yellow text-jti-primary whitespace-nowrap">
                                        Kelas
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <p class="text-xs font-bold text-jti-yellow">
                                    Rp{{ number_format($item->price_at_purchase, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Action Area --}}
            <div
                class="lg:col-span-5 flex flex-col sm:flex-row items-center gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                <div class="flex-1 flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="w-5 h-5 text-slate-400">
                            <path
                                d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1" />
                            <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase">Metode</p>
                        <p class="text-xs font-bold text-slate-700 uppercase">
                            {{ $order->payment_method ? str_replace('_', ' ', $order->payment_method) : 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-2 w-full sm:w-auto">
                    <!-- Cek Gateway Pembayaran -->
                    @if ($order->payment_gateway === 'midtrans' && $order->snap_token)
                        <!-- Tombol untuk Midtrans -->
                        <button
                            wire:click="$dispatch('trigger-midtrans-snap', { token: '{{ $order->snap_token }}', orderId: {{ $order->id }} })"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-jti-yellow text-jti-primary font-bold text-xs rounded-xl hover:bg-jti-yellow/90 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="w-4 h-4">
                                <rect width="20" height="14" x="2" y="5" rx="2" />
                                <line x1="2" x2="22" y1="10" y2="10" />
                            </svg>
                            Bayar
                        </button>
                    @else
                        <!-- Tombol untuk BNI / VA Native -->
                        <button wire:click="showVaInstructions({{ $order->id }})"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-jti-yellow text-jti-primary font-bold text-xs rounded-xl hover:bg-jti-yellow/90 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="w-4 h-4">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" x2="8" y1="13" y2="13" />
                                <line x1="16" x2="8" y1="17" y2="17" />
                                <polyline points="10 9 9 9 8 9" />
                            </svg>
                            Lihat Instruksi
                        </button>
                    @endif
                    @if ($order->payment_proof)
                        <button wire:click="viewProof({{ $order->id }})"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white text-emerald-600 border border-emerald-100 font-bold text-xs rounded-xl hover:bg-emerald-50 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="w-4 h-4">
                                <path
                                    d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Bukti
                        </button>
                    @else
                        <button wire:click="openProofUpload({{ $order->id }})"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white text-blue-600 border border-blue-100 font-bold text-xs rounded-xl hover:bg-blue-50 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M12 3v12" />
                                <path d="m17 8-5-5-5 5" />
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            </svg>
                            Upload
                        </button>
                    @endif
                    <button wire:click="cancelOrder({{ $order->id }})"
                        onclick="return confirm('Apakah Anda yakin ingin membatalkan order ini?')"
                        class="flex-1 sm:flex-none px-5 py-2.5 bg-white text-rose-600 border border-rose-100 font-bold text-xs rounded-xl hover:bg-rose-50 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="w-4 h-4">
                            <circle cx="12" cy="12" r="10" />
                            <path d="m15 9-6 6" />
                            <path d="m9 9 6 6" />
                        </svg>
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $wire.on('trigger-midtrans-snap', (event) => {
            let data = event[0] ?? event; // Kompatibilitas Livewire 3
            let snapToken = data.token;
            let orderId = data.orderId;

            if (typeof window.snap !== 'undefined') {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        // Panggil fungsi di backend tanpa reload
                        $wire.handlePaymentFinished(orderId);
                    },
                    onPending: function(result) {
                        $wire.handlePaymentFinished(orderId);
                    },
                    onError: function(result) {
                        alert("Terjadi kesalahan saat memproses pembayaran.");
                    },
                    onClose: function() {
                        console.log("Popup Midtrans ditutup.");
                    }
                });
            }
        });
    </script>
@endscript

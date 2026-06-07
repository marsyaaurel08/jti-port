@props(['order'])
<div wire:key="order-{{ $order->id }}"
    class="bg-white rrounded-4xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow">

    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Order Meta --}}
        <div class="flex flex-row justify-between items-start gap-4 mb-6 pb-6 border-b border-slate-100">
            <div class="space-y-1 w-full">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nomor Invoice</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">-</span>
                    <span
                        class="bg-jti-primary inline-flex items-center px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider text-white border border-slate-200/60 shadow-sm">
                        {{ $order->payment_method ? str_replace('_', ' ', $order->payment_method) : 'N/A' }}
                    </span>
                </div>
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
            <div class="text-right w-full">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Tagihan</span>
                <p class="text-xl sm:text-2xl font-bold text-jti-primary">
                    <span
                        class="text-sm font-semibold mr-0.5">Rp</span>{{ number_format($order->total_amount, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Order Items & Action Area --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 lg:gap-8">
            {{-- Left side: Order Items --}}
            <div class="w-full lg:flex-1 space-y-4">
                @foreach ($order->orderItems as $item)
                    <div class="flex items-start sm:items-center gap-4 group">
                        <div
                            class="relative w-16 h-12 sm:w-20 sm:h-14 shrink-0 overflow-hidden rounded-lg bg-slate-100 border border-slate-200/50">
                            <img src="{{ $item->course->thumbnail_url ?? ($item->class->thumbnail_url ?? 'https://via.placeholder.com/150') }}"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-3">
                                <h4
                                    class="text-sm font-semibold text-slate-800 leading-snug line-clamp-2 sm:line-clamp-1 group-hover:text-jti-primary transition-colors">
                                    {{ $item->item_name }}
                                </h4>
                                <div class="shrink-0">
                                    <span
                                        class="inline-block px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-wider text-white whitespace-nowrap {{ $item->class_id ? 'bg-jti-yellow' : 'bg-jti-orange' }}">
                                        {{ $item->class_id ? 'Kelas' : 'Course' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Right side: Action Area --}}
            <div
                class="w-full lg:w-auto flex flex-col sm:flex-row lg:flex-col xl:flex-row items-start sm:items-center lg:items-start xl:items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100/70">
                {{-- Payment Method Info --}}

                {{-- Action Buttons --}}
                <div
                    class="flex flex-wrap gap-2 w-full sm:w-auto sm:flex-1 lg:flex-initial justify-start sm:justify-end lg:justify-start xl:justify-end">
                    <!-- Cek Gateway Pembayaran -->
                    <button wire:click="openPaymentModal({{ $order->id }})"
                        class="flex-1 sm:flex-none px-4 py-2 bg-jti-yellow text-jti-primary font-bold text-xs rounded-xl hover:bg-jti-yellow/90 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-1.5">
                        <x-lucide-credit-card class="w-3.5 h-3.5" />
                        Bayar
                    </button>

                    @if ($order->payment_proof)
                        <button wire:click="viewProof({{ $order->id }})"
                            class="flex-1 sm:flex-none px-4 py-2 bg-white text-emerald-600 border border-emerald-100 font-bold text-xs rounded-xl hover:bg-emerald-50 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-1.5">
                            <x-lucide-eye class="w-3.5 h-3.5" />
                            Bukti
                        </button>
                    @else
                        <button wire:click="openProofUpload({{ $order->id }})"
                            class="flex-1 sm:flex-none px-4 py-2 bg-white text-blue-600 border border-blue-100 font-bold text-xs rounded-xl hover:bg-blue-50 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-1.5">
                            <x-lucide-upload class="w-3.5 h-3.5" />
                            Upload
                        </button>
                    @endif

                    <button wire:click="cancelOrder({{ $order->id }})"
                        onclick="return confirm('Apakah Anda yakin ingin membatalkan order ini?')"
                        class="flex-1 sm:flex-none px-4 py-2 bg-white text-rose-600 border border-rose-100 font-bold text-xs rounded-xl hover:bg-rose-50 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-1.5">
                        <x-lucide-x class="w-3.5 h-3.5" />
                        Batal
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

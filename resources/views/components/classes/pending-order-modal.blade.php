@props(['class', 'pendingOrder' => null])

@if ($pendingOrder)
    <div data-pending-order="true" x-data="{ showModal: true }" x-show="showModal"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-9999 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showModal = false" @keydown.escape="showModal = false" x-init="window.addEventListener('show-pending-modal', () => { showModal = true; })">

        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-lg mx-4 bg-white rounded-[24px] shadow-2xl overflow-hidden">

            <div class="px-4 sm:px-6 pt-6 sm:pt-8 pb-2 relative">
                <button @click="showModal = false"
                    class="absolute top-6 right-6 text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h3 class="text-xl sm:text-2xl font-extrabold text-jti-primary tracking-tight">
                    Pesanan Belum Selesai
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <div class="w-2 h-2 rounded-full bg-jti-orange animate-pulse"></div>
                    <p class="text-xs sm:text-sm text-gray-500 font-medium">
                        Anda memiliki transaksi menggantung
                    </p>
                </div>
            </div>

            <div class="px-4 sm:px-6 pb-6 sm:pb-8 max-h-[75vh] overflow-y-auto">

                <div class="mt-6 bg-[#0b1b43] rounded-2xl p-6 relative overflow-hidden shadow-lg border border-[#162758]">
                    <div class="absolute top-4 right-4 opacity-20">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <p class="text-xs font-bold text-gray-300 uppercase tracking-wider mb-1">TOTAL TAGIHAN</p>
                        <h4 class="text-3xl sm:text-4xl font-black text-[#fec115] tracking-tight mb-6">
                            Rp{{ number_format($pendingOrder->total_amount, 0, ',', '.') }}
                        </h4>

                        <div class="flex flex-row justify-between border-t border-white/20 pt-4 mt-2">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ID Pesanan</p>
                                <p class="text-sm font-bold text-white">{{ $pendingOrder->invoice_number ?? $pendingOrder->id }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Metode</p>
                                <p class="text-sm font-bold text-white">
                                    {{ $pendingOrder->payment_method === 'bni_transfer' ? 'BNI Transfer' : 
                                       ($pendingOrder->payment_method === 'midtrans' ? 'Midtrans' : 
                                       ucwords(str_replace('_', ' ', $pendingOrder->payment_method))) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-6 mb-3 px-1">Rincian Kelas</p>

                <div class="border border-gray-200 rounded-2xl p-5 bg-white space-y-4">
                    
                    <div class="flex items-start gap-3.5">
                        <div class="w-6 h-6 rounded border border-gray-200 bg-gray-50 flex items-center justify-center text-[11px] font-bold text-gray-600 shrink-0 mt-0.5">
                            1
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Nama Kelas</p>
                            <p class="text-sm font-bold text-jti-primary leading-snug mt-0.5">{{ $class->title }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-6 h-6 rounded border border-gray-200 bg-gray-50 flex items-center justify-center text-[11px] font-bold text-gray-600 shrink-0 mt-0.5">
                            2
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Waktu Transaksi</p>
                            <p class="text-sm font-bold text-jti-primary mt-0.5">{{ $pendingOrder->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                    </div>

                </div>

                <div class="bg-[#fff9e6] border border-[#ffe082] rounded-xl p-4 mt-5 flex items-start gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#fec115] mt-1 shrink-0"></div>
                    <p class="text-xs sm:text-sm text-[#8a6d1c] leading-relaxed">
                        Pesanan ini akan otomatis <span class="font-bold">dibatalkan</span> jika Anda melanjutkan membuat transaksi baru.
                    </p>
                </div>

                <div class="mt-6 flex flex-col gap-3">
                    
                    <button wire:click="cancelPendingOrder" wire:loading.attr="disabled" type="button"
                        class="w-full px-4 py-3.5 sm:py-4 text-sm font-semibold text-[#0b1b43] bg-[#fec115] hover:bg-[#e5ae13] rounded-[14px] shadow-sm transition-all duration-200 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        
                        <span wire:loading.remove>Lanjutkan Checkout Baru</span>
                        
                        <svg wire:loading class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading>Memproses...</span>
                    </button>

                    <button @click="showModal = false" type="button"
                        class="w-full px-4 py-3 text-sm font-bold text-gray-500 bg-transparent hover:text-[#0b1b43] hover:bg-gray-50 rounded-[14px] transition-all duration-200 active:scale-[0.98]">
                        Tutup
                    </button>

                </div>
            </div>
        </div>
    </div>
@endif
<div @if(!$showProofUpload && !$showProofView && !$showFallbackPaymentModal && !$showMidtransPaymentModal && !$showBNIPaymentModal) wire:poll.5s @endif>
    <x-layouts.user :user="$this->user">
        {{-- SUCCESS TOAST (Premium melayang, otomatis hilang, bisa ditutup manual) --}}
        @if (session('success'))
            <div class="fixed top-24 right-6 bg-green-50 text-green-800 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-75"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
                x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 blur-sm">
                <div
                    class="shrink-0 w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-sm">
                    <x-lucide-check class="w-4 h-4" />
                </div>
                <div class="pr-2">
                    <p class="text-sm font-semibold text-emerald-50">Tindakan berhasil</p>
                    <p class="text-xs text-slate-300/90 leading-normal">{{ session('success') }}</p>
                </div>
                <button type="button" @click="show = false"
                    class="text-slate-400 hover:text-white ml-auto transition-colors p-1 rounded-lg hover:bg-slate-800">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif

        {{-- ERROR TOAST (Premium melayang, otomatis hilang, bisa ditutup manual) --}}
        @if (session('error'))
            <div class="fixed top-24 right-6 bg-rose-50 text-rose-800 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-75"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
                x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 blur-sm">
                <div
                    class="shrink-0 w-8 h-8 bg-rose-500 rounded-full flex items-center justify-center text-white shadow-sm">
                    <x-lucide-alert-circle class="w-4 h-4" />
                </div>
                <div class="pr-2">
                    <p class="text-sm font-semibold text-rose-400">Terjadi kesalahan</p>
                    <p class="text-xs text-slate-300/90 leading-normal">{{ session('error') }}</p>
                </div>
                <button type="button" @click="show = false"
                    class="text-slate-400 hover:text-white ml-auto transition-colors p-1 rounded-lg hover:bg-slate-800">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        @endif

        {{-- KONTEN UTAMA HALAMAN --}}
        @if ($orders->isEmpty())
            <div wire:key="empty-orders">
                {{-- Empty State --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Pesanan Saya</h1>
                    <p class="text-gray-600 mt-2">Pantau status pembayaran dan riwayat transaksi Anda</p>
                </div>

                <div class="bg-white rounded-4xl border border-slate-100 p-16 text-center shadow-sm">
                    <div class="relative inline-flex mb-6">
                        <div class="absolute inset-0 bg-jti-yellow/20 rounded-full blur-2xl"></div>
                        <div class="relative w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                            <x-lucide-ghost class="w-10 h-10 text-gray-400" />
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 mb-2">Semua beres!</h3>
                    <p class="text-slate-500 max-w-xs mx-auto mb-8 text-sm leading-relaxed font-medium">
                        Tidak ada tagihan yang menggantung. Saatnya menambah ilmu baru hari ini?
                    </p>
                    <a href="{{ route('courses.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-jti-primary text-white text-sm font-medium rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95 group">
                        Cari Kursus
                        <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-6" wire:key="filled-orders">
                {{-- Header --}}
                <div class="px-2 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-jti-primary tracking-tight">Pesanan Anda</h2>
                        <p class="text-sm text-slate-500 font-medium">Kelola pembayaran untuk item yang telah Anda pesan</p>
                    </div>

                    {{-- Status Badge --}}
                    <div class="inline-flex items-center gap-2.5 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100">
                        <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                        <p class="text-xs font-bold text-slate-700">{{ $orders->count() }} Menunggu Pembayaran</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach ($orders as $order)
                        <x-student.order-item :order="$order" wire:key="order-{{ $order->id }}" />
                    @endforeach
                </div>
            </div>
        @endif

    </x-layouts.user>
    {{-- Modal: Lihat Bukti Pembayaran --}}
    <div x-data="{ showModal: $wire.entangle('showProofView') }"
         x-cloak
         x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="showModal = false"
         @keydown.escape.window="showModal = false"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        
        @if ($selectedOrder)
            <div x-show="showModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-white rounded-4xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                @click.stop>
                {{-- Header --}}
                <div class="sticky top-0 bg-white border-b border-slate-100 px-6 sm:px-8 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Bukti Pembayaran</h3>
                    <button @click="showModal = false"
                        class="p-2 hover:bg-slate-100 rounded-xl transition-all text-slate-400 hover:text-slate-600">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Content --}}
                <div class="p-6 sm:p-8 space-y-6">
                    {{-- Invoice Info --}}
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">Invoice</p>
                            <p class="text-sm font-mono font-bold text-jti-primary mt-1">
                                {{ $selectedOrder->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase">Total</p>
                            <p class="text-sm font-bold text-jti-primary mt-1">Rp
                                {{ number_format($selectedOrder->total_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Image Preview --}}
                    <div class="space-y-2">
                        <p class="text-xs font-bold text-slate-400 uppercase">Bukti Pembayaran</p>
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 p-4 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('storage/' . $selectedOrder->payment_proof) }}" alt="Bukti Pembayaran"
                                class="max-w-full max-h-[60vh] object-contain rounded-lg">
                        </div>
                    </div>

                    {{-- Close Button --}}
                    <button @click="showModal = false"
                        class="w-full px-6 py-3 bg-jti-primary text-white font-bold rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Upload Bukti Pembayaran --}}
    <x-student.proof-upload-modal :selectedOrder="$selectedOrder" :paymentProof="$paymentProof" />

    {{-- Modal: BNI --}}
    {{-- <x-order.bni-payment-modal :show="$showBNIPaymentModal" :bniPaymentData="$bniPaymentData" /> --}}

    {{-- Fallback Payment Modal --}}
    <x-order.fallback-payment-modal :fallbackPaymentData="$fallbackPaymentData" />

    {{-- Modal: Midtrans--}}
    <x-order.payment-modal :paymentModalData="$paymentModalData" :totalAmount="$totalAmount" :isPaid="$isPaid"
        :successCountdown="$successCountdown" />
</div>

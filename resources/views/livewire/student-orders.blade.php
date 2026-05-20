<x-layouts.user :user="$this->user">
    {{-- SUCCESS TOAST --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4500)" x-show="show"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-3/4"
            class="fixed top-6 right-6 z-[9999] bg-green-50 text-green-800 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[300px] animate-slide-in-right">
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ERROR TOAST --}}
    @if (session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-3/4"
            class="fixed top-6 right-6 z-[9999] bg-red-50 text-red-800 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[300px] animate-slide-in-right">
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif
    @if ($orders->isEmpty())
        {{-- Empty State: Dibuat konsisten dengan Cart --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pesanan Saya</h1>
            <p class="text-gray-600 mt-2">Pantau status pembayaran dan riwayat transaksi Anda</p>
        </div>

        <div class="bg-white rounded-4xl border border-slate-100 p-16 text-center shadow-sm">
            <div class="relative inline-flex mb-6">
                {{-- Subtle Glow Kuning --}}
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
    @else
        <div class="space-y-6">
            {{-- Header --}}
            <div class="px-2 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-jti-primary tracking-tight">Pesanan Anda</h2>
                    <p class="text-sm text-slate-500 font-medium">Kelola pembayaran untuk item yang telah Anda pesan</p>
                </div>

                {{-- Status Badge --}}
                <div
                    class="inline-flex items-center gap-2.5 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-100">
                    <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                    <p class="text-xs font-bold text-slate-700">{{ $orders->count() }} Menunggu Pembayaran</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($orders as $order)
                    <x-student.order-item :order="$order" />
                @endforeach
            </div>
            {{-- Modal: Upload Bukti Pembayaran --}}
            <x-student.proof-upload-modal :show="$showProofUpload" :selectedOrder="$selectedOrder" :paymentProof="$paymentProof" />
            {{-- Modal: Custom Payment --}}
            <x-order.payment-modal :showCustomPaymentModal="$showCustomPaymentModal" :paymentModalData="$paymentModalData" :totalAmount="$totalAmount" :isPaid="$isPaid"
                :successCountdown="$successCountdown" />
        </div>
    @endif

    {{-- Modal: Lihat Bukti Pembayaran --}}
    @if ($showProofView && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-4xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                {{-- Header --}}
                <div
                    class="sticky top-0 bg-white border-b border-slate-100 px-6 sm:px-8 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Bukti Pembayaran</h3>
                    <button wire:click="closeProofView()"
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
                        <div
                            class="border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 p-4 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('storage/' . $selectedOrder->payment_proof) }}" alt="Bukti Pembayaran"
                                class="max-w-full max-h-[60vh] object-contain rounded-lg">
                        </div>
                    </div>

                    {{-- Close Button --}}
                    <button wire:click="closeProofView()"
                        class="w-full px-6 py-3 bg-jti-primary text-white font-bold rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-layouts.user>

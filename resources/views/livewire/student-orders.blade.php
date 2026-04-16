<x-layouts.user :user="$this->user">
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" class="w-10 h-10">
                        <path d="M9 10h.01" />
                        <path d="M15 10h.01" />
                        <path d="M12 2a8 8 0 0 0-8 8v12l3-3 2.5 2.5L12 19l2.5 2.5L17 19l3 3V10a8 8 0 0 0-8-8z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">Semua beres!</h3>
            <p class="text-slate-500 max-w-xs mx-auto mb-8 text-sm leading-relaxed font-medium">
                Tidak ada tagihan yang menggantung. Saatnya menambah ilmu baru hari ini?
            </p>
            <a href="{{ route('courses.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-jti-primary text-white text-sm font-medium rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95 group">
                Cari Kursus
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
                    <div wire:key="order-{{ $order->id }}"
                        class="bg-white rrounded-4xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow">

                        <div class="p-6 sm:p-8">
                            {{-- Order Meta --}}
                            <div
                                class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6 pb-6 border-b border-slate-50">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nomor
                                        Invoice</span>
                                    <p class="text-sm font-mono font-bold text-jti-primary">{{ $order->invoice_number }}
                                    </p>
                                    <p class="text-xs text-slate-400 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                        </svg>
                                        {{ $order->created_at->format('d M Y • H:i') }} WIB
                                    </p>
                                </div>
                                <div class="md:text-right">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total
                                        Tagihan</span>
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

                                {{-- Action Area: Menggunakan aksen kuning/biru yang soft --}}
                                <div
                                    class="lg:col-span-5 flex flex-col sm:flex-row items-center gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                                    <div class="flex-1 flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-slate-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="w-5 h-5 text-slate-400">
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
                                        <button wire:click="viewVAInstructions({{ $order->id }})"
                                            class="flex-1 sm:flex-none px-5 py-2.5 bg-jti-yellow text-jti-primary font-bold text-xs rounded-xl hover:bg-jti-yellow/90 transition-all active:scale-95 shadow-sm">
                                            Instruksi
                                        </button>
                                        @if ($order->payment_proof)
                                            <button wire:click="viewProof({{ $order->id }})"
                                                class="flex-1 sm:flex-none px-5 py-2.5 bg-white text-emerald-600 border border-emerald-100 font-bold text-xs rounded-xl hover:bg-emerald-50 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="w-4 h-4">
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
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="w-4 h-4">
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
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="w-4 h-4">
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
                @endforeach
            </div>
        </div>
    @endif

    {{-- Modal: Upload Bukti Pembayaran --}}
    <x-student.proof-upload-modal :show="$showProofUpload" :selectedOrder="$selectedOrder" :paymentProof="$paymentProof" />

    {{-- Modal: VA Instructions --}}
    <x-order.va-instructions-modal :show="$showVAInstructions" :vaData="$vaData" :transferMethodName="$transferMethods[$selectedOrder?->payment_method] ?? 'Transfer'" :instructions="$transferInstructions[$selectedOrder?->payment_method] ?? []" />

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
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="w-5 h-5">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
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

                    {{-- Info Message --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                        <p class="text-xs text-blue-700 font-medium">
                            <i data-lucide="info" class="w-4 h-4 inline mr-2"></i>
                            Bukti pembayaran Anda telah terupload. Admin akan memverifikasi dalam waktu 1x24 jam.
                        </p>
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

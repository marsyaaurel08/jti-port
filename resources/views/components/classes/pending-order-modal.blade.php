@props(['class', 'pendingOrder' => null])

@if ($pendingOrder)
    <div data-pending-order="true" x-data="{ showModal: true }" x-show="showModal"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-9999 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="showModal = false" @keydown.escape="showModal = false" x-init="window.addEventListener('show-pending-modal', () => { showModal = true; })">

        <!-- Modal Box -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden">

            <!-- Header -->
            <div
                class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-200 px-6 sm:px-8 pt-6 sm:pt-8 pb-4 sm:pb-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 sm:gap-4">
                        <!-- Warning Icon -->
                        <div class="flex-shrink-0 mt-1">
                            <div
                                class="flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-amber-100">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Title & Description -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">
                                Pesanan Belum Diselesaikan
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-relaxed">
                                Anda memiliki pesanan yang masih menunggu pembayaran untuk kelas ini
                            </p>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <button @click="showModal = false"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors p-1">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body Content -->
            <div class="px-6 sm:px-8 py-6 sm:py-8 space-y-4 sm:space-y-6 max-h-[70vh] overflow-y-auto">

                <!-- Product Info Card -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Kelas</p>
                    <h4 class="text-base sm:text-lg font-bold text-gray-900 line-clamp-2">{{ $class->title }}</h4>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 line-clamp-1">{{ $class->course->title ?? 'N/A' }}
                    </p>
                </div>

                <!-- Order Details Grid -->
                <div class="space-y-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Detail Pesanan</p>

                    <!-- Order ID -->
                    <div
                        class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-sm text-gray-600">ID Pesanan</span>
                        </div>
                        <span
                            class="text-sm font-semibold text-gray-900 font-mono">{{ $pendingOrder->order_number ?? $pendingOrder->id }}</span>
                    </div>

                    <!-- Amount -->
                    <div
                        class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-600">Nominal</span>
                        </div>
                        <span class="text-base sm:text-lg font-bold text-jti-primary">
                            Rp{{ number_format($pendingOrder->total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Payment Method -->
                    <div
                        class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h10m4 0a1 1 0 11-2 0 1 1 0 012 0zM7 15a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                            <span class="text-sm text-gray-600">Metode Pembayaran</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">
                            @if ($pendingOrder->payment_method === 'bni_transfer')
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold">
                                    BNI Transfer
                                </span>
                            @elseif ($pendingOrder->payment_method === 'midtrans')
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-50 text-purple-700 rounded-lg text-xs font-semibold">
                                    Midtrans
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold">
                                    {{ ucwords(str_replace('_', ' ', $pendingOrder->payment_method)) }}
                                </span>
                            @endif
                        </span>
                    </div>

                    <!-- Order Date -->
                    <div
                        class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-gray-600">Waktu Pesan</span>
                        </div>
                        <span class="text-sm text-gray-900 font-medium">
                            {{ $pendingOrder->created_at->format('d M Y H:i') }}
                        </span>
                    </div>

                    <!-- Status Badge -->
                    <div class="flex items-center justify-between p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-600">Status</span>
                        </div>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold">
                            <span class="inline-block h-2 w-2 rounded-full bg-amber-600"></span>
                            Menunggu Pembayaran
                        </span>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                    <div class="flex gap-3">
                        <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs sm:text-sm text-blue-900 leading-relaxed">
                            <span class="font-semibold">💡 Info:</span> Pesanan sebelumnya akan otomatis dibatalkan
                            jika Anda melanjutkan ke checkout yang baru.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer / Action Buttons -->
            <div class="border-t border-gray-200 bg-gray-50 px-6 sm:px-8 py-4 flex flex-col sm:flex-row gap-3">
                <!-- Batalkan Button -->
                <button @click="showModal = false" type="button"
                    class="flex-1 px-4 py-2.5 sm:py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 active:scale-[0.98] order-2 sm:order-1">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batalkan
                    </span>
                </button>

                <!-- Lanjutkan Button -->
                <button wire:click="cancelPendingOrder" wire:loading.attr="disabled" type="button"
                    class="flex-1 px-4 py-2.5 sm:py-3 text-sm font-semibold text-white bg-gradient-to-r from-jti-primary to-jti-primary/80 rounded-xl hover:from-jti-primary/90 hover:to-jti-primary/70 hover:shadow-lg transition-all duration-200 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 order-1 sm:order-2">
                    <svg wire:loading.remove class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    <svg wire:loading class="h-4 w-4 sm:h-5 sm:w-5 animate-spin" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span wire:loading.remove>Lanjutkan Checkout</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </div>
    </div>
@endif

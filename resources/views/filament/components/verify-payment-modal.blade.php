<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $record->invoice_number }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pembayaran</p>
            <p class="text-lg font-bold text-primary-600 dark:text-primary-400 mt-1">
                Rp{{ number_format($record->total_amount, 0, ',', '.') }}
            </p>
        </div>
    </div>

    @if ($record->payment_proof)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 flex-shrink-0 bg-primary-100 dark:bg-primary-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Bukti Pembayaran</h3>
                </div>

                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <img src="{{ asset('storage/' . $record->payment_proof) }}" alt="Bukti Pembayaran"
                        class="w-full h-auto object-contain max-h-96 mx-auto">
                </div>
            </div>
        </div>
    @endif

    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pelanggan</p>
                <p class="font-semibold text-gray-900 dark:text-white mt-1">{{ $record->user->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</p>
                <p class="font-semibold text-gray-900 dark:text-white mt-1 text-sm">{{ $record->user->email }}</p>
            </div>
        </div>
    </div>

    <div class="bg-info-50 dark:bg-info-500/10 border border-info-200 dark:border-info-500/20 rounded-lg p-4">
        <p class="text-sm text-info-800 dark:text-info-300">
            Setelah memeriksa bukti pembayaran di atas, klik <strong>"Ya, Verifikasi"</strong> untuk mengkonfirmasi
            bahwa pembayaran valid dan mengubah status menjadi <strong>"Dibayar"</strong>.
        </p>
    </div>
</div>

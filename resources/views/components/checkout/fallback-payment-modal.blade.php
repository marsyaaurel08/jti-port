@props(['showFallbackPaymentModal', 'fallbackPaymentData'])

<div x-data="{
    showToast: false,
    toastMessage: '',
    copyToClipboard(text, message) {
        let cleanText = text.toString().replace(/\D/g, '');
        navigator.clipboard.writeText(cleanText).then(() => {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        });
    }
}"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity"
    style="display: {{ $showFallbackPaymentModal ? 'flex' : 'none' }}">

    <div x-cloak x-show="showToast" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-[10000] bg-gray-900 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 text-sm font-medium">
        <x-lucide-check-circle class="w-5 h-5 text-emerald-400" />
        <span x-text="toastMessage"></span>
    </div>

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl mx-4 flex flex-col overflow-hidden max-h-[90vh]">

        <div class="px-6 pt-6 pb-4 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    Instruksi Pembayaran Manual
                </h2>
            </div>
            <button wire:click="closeFallbackPaymentModal" class="text-gray-400 hover:text-gray-600 transition">
                <x-lucide-x class="w-6 h-6" />
            </button>
        </div>

        <div class="px-6 py-2 overflow-y-auto flex-1 custom-scrollbar">

            <div class="bg-[#FFF5F5] border border-[#FED7D7] rounded-xl p-4 flex items-start gap-3 mb-2 mt-4">
                <span class="w-3 h-3 rounded-full bg-red-500 mt-1 shrink-0"></span>
                <p class="text-xs text-gray-700 leading-relaxed">
                    Mohon maaf, sedang terjadi gangguan koneksi dengan layanan BNI. Kami telah membuatkan <strong class="text-gray-900">Virtual Account Alternatif</strong> untuk Anda. Silakan selesaikan pembayaran ke VA di bawah ini.
                </p>
            </div>

            <div class="bg-jti-primary rounded-2xl p-6 relative overflow-hidden shadow-lg border border-gray-800 mb-6 mt-4">
                <div class="absolute top-4 right-4 opacity-10">
                    <x-lucide-credit-card class="w-16 h-16 text-gray-300" />
                </div>

                <p class="text-[11px] font-bold tracking-widest text-gray-300 mb-2 uppercase">
                    Nomor VA Alternatif (BNI)
                </p>

                <div class="flex justify-between items-center mb-6 relative z-10">
                    <p class="text-lg md:text-2xl font-extrabold text-jti-yellow tracking-widest">
                        {{ data_get($fallbackPaymentData, 'va_number') ?? '-' }}
                    </p>
                    <button type="button" @click="copyToClipboard('{{ data_get($fallbackPaymentData, 'va_number') }}', 'Nomor Virtual Account disalin!')"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-jti-yellow/20 active:scale-95 transition group">
                        <x-lucide-copy class="w-5 h-5 text-jti-yellow group-hover:scale-110 transition-transform" />
                    </button>
                </div>

                <div class="flex justify-between border-t border-white/10 pt-4 relative z-10 items-end">
                    <div>
                        <p class="text-[10px] text-gray-400 mb-1 uppercase">Pengecekan</p>
                        <p class="font-bold text-white text-sm">MANUAL</p>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <p class="text-[10px] text-gray-400 mb-1 uppercase">Total Nominal</p>
                        <p class="font-bold text-jti-yellow text-lg leading-none">Rp{{ number_format(data_get($fallbackPaymentData, 'total_amount'), 0, ',', '.') }}</p>
                        <button type="button" @click="copyToClipboard('{{ data_get($fallbackPaymentData, 'total_amount') }}', 'Nominal Tagihan disalin!')"
                            class="flex items-center gap-1 text-[10px] text-gray-400 hover:text-jti-yellow transition mt-1.5 active:scale-95">
                            <x-lucide-copy class="w-3 h-3" /> Salin Nominal
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Panduan Tambahan:</span>
                </div>

                <div class="border border-gray-100 bg-white shadow-sm rounded-xl p-5 mb-4">
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="shrink-0 w-6 h-6 flex items-center justify-center bg-white border border-gray-200 shadow-sm rounded-md text-[11px] font-bold text-gray-700 mt-0.5">
                                1
                            </span>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Lakukan transfer sesuai total nominal (tepat hingga 3 digit terakhir jika ada) ke nomor VA di atas.
                            </p>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="shrink-0 w-6 h-6 flex items-center justify-center bg-white border border-gray-200 shadow-sm rounded-md text-[11px] font-bold text-gray-700 mt-0.5">
                                2
                            </span>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Setelah transfer berhasil, <strong class="text-gray-900">simpan struk/bukti transfer</strong> pembayaran Anda.
                            </p>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="shrink-0 w-6 h-6 flex items-center justify-center bg-white border border-gray-200 shadow-sm rounded-md text-[11px] font-bold text-gray-700 mt-0.5">
                                3
                            </span>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Hubungi Admin melalui pesan/WhatsApp untuk melampirkan bukti tersebut guna mempercepat proses verifikasi.
                            </p>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="px-6 py-4 bg-white border-t border-gray-100">
            <button wire:click="closeFallbackPaymentModal"
                class="w-full py-3.5 bg-jti-yellow hover:bg-[#FFB300] text-gray-900 font-bold rounded-xl transition-colors">
                Mengerti & Tutup
            </button>
        </div>

    </div>
</div>

@props(['show', 'selectedOrder', 'paymentProof'])

{{-- Modal: Upload Bukti Pembayaran --}}
@if ($show && $selectedOrder)
    <div class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 backdrop-blur-md"
        x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        @click.self="show = false; $wire.closeProofUpload()">

        <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] overflow-hidden shadow-2xl border border-slate-200 flex flex-col"
            @click.stop>

            {{-- Header --}}
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Upload Bukti Pembayaran</h2>
                    <p class="text-xs text-slate-500 font-medium mt-1">Invoice {{ $selectedOrder->invoice_number }}
                    </p>
                </div>
                <button @click="show = false; $wire.closeProofUpload()"
                    class="p-2 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-6 overflow-y-auto">

                {{-- Info Box --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-3">
                    <div class="bg-blue-400 p-2 rounded-lg shrink-0">
                        <i data-lucide="info" class="w-4 h-4 text-white"></i>
                    </div>
                    <div class="text-sm text-blue-900 space-y-1">
                        <p class="font-semibold">Bukti Pembayaran (Optional)</p>
                        <p>Upload bukti transfer Anda, untuk berjaga-jaga jika terjadi kendala dalam proses verifikasi
                            pembayaran.</p>
                    </div>
                </div>

                {{-- File Upload Area --}}
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">
                        Bukti Pembayaran <span class="text-red-500">*</span>
                    </label>

                    {{-- Tampilkan Dropzone HANYA jika belum ada gambar yang dipilih --}}
                    @if (!$paymentProof)
                        <div
                            class="relative border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-blue-300 transition-colors cursor-pointer group">
                            <input type="file" wire:model="paymentProof" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                            <div class="space-y-3">
                                <div
                                    class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto group-hover:bg-blue-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 5h6" />
                                        <path d="M19 2v6" />
                                        <path d="M21 11.5V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7.5" />
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                        <circle cx="9" cy="9" r="2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700">Klik atau drag gambar ke sini</p>
                                    <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG (Maks. 5MB)</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @error('paymentProof')
                        <p class="text-sm text-red-600 font-medium flex items-center gap-1.5 mt-2">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </p>
                    @enderror

                    {{-- Preview: Muncul jika gambar sudah ada --}}
                    @if ($paymentProof && !$errors->has('paymentProof'))
                        <div class="mt-4 relative group">
                            <div class="flex justify-between items-end mb-2">
                                <p class="text-xs font-bold text-slate-500 tracking-wider">PREVIEW BUKTI</p>

                                {{-- Tombol Ganti Gambar --}}
                                <button type="button" wire:click="$set('paymentProof', null)"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                                    <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                    Ganti Gambar
                                </button>
                            </div>

                            <div
                                class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-inner bg-slate-100">
                                <img src="{{ $paymentProof->temporaryUrl() }}"
                                    class="w-full h-56 object-contain mx-auto">

                                {{-- Overlay Hapus saat Hover --}}
                                <div
                                    class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" wire:click="$set('paymentProof', null)"
                                        class="bg-white/20 backdrop-blur-md text-white px-4 py-2 rounded-full text-sm font-semibold border border-white/30 hover:bg-white/40 transition-all">
                                        Hapus & Pilih Ulang
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Transaction Info --}}
                <div class="bg-slate-50 rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="text-sm text-slate-600">Total Pembayaran</span>
                        <span
                            class="font-bold text-slate-900">Rp{{ number_format($selectedOrder->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Status</span>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-clock-icon lucide-clock">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6v6l4 2" />
                            </svg>
                            Menunggu Verifikasi
                        </span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50 flex gap-3">
                <button @click="show = false; $wire.closeProofUpload()"
                    class="flex-1 py-3 text-slate-700 font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-all">
                    Batal
                </button>
                <button wire:click="uploadPaymentProof" wire:loading.attr="disabled"
                    class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 disabled:opacity-50 transition-all flex items-center justify-center gap-2"
                    wire:loading.class="opacity-50">
                    <span wire:loading.remove>Upload Bukti</span>
                    <span wire:loading>
                        <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
                        Uploading...
                    </span>
                </button>
            </div>
        </div>
    </div>
@endif

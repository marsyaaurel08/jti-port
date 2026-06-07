@props([
    'showMidtransPaymentModal',
    'paymentModalData',
    'totalAmount',
    'countdownSeconds',
    'isPaid' => false,
    'successCountdown' => 0, // Tambahkan ini di props teratas
])

{{-- Di tag pembuka div paling luar, pastikan polling dimatikan/diubah kondisinya saat sukses --}}
<div wire:target="showMidtransPaymentModal" @if ($showMidtransPaymentModal && !$isPaid) wire:poll.3s="checkPaymentStatus" @endif
    class="fixed inset-0 z-9999 flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity"
    style="display: {{ $showMidtransPaymentModal ? 'flex' : 'none' }}">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl mx-4 flex flex-col overflow-hidden max-h-[90vh]">

        <div class="px-6 pt-6 pb-4 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $isPaid ? 'Status Pembayaran' : 'Instruksi Pembayaran' }}
                </h2>
            </div>
            <button wire:click="closeMidtransPaymentModal" class="text-gray-400 hover:text-gray-600 transition">
                <x-lucide-x class="w-6 h-6" />
            </button>
        </div>

        <div class="px-6 py-2 overflow-y-auto flex-1 custom-scrollbar">

            @if ($isPaid)
                {{-- ======================================================== --}}
                {{-- TAMPILAN SUKSES & COUNTDOWN (WIRE:POLL SETIAP 1 DETIK)   --}}
                {{-- ======================================================== --}}
                <div wire:poll.1s="startSuccessCountdown"
                    class="flex flex-col items-center justify-center py-12 px-6 text-center animate-fade-in">

                    <!-- Icon: Dibuat lebih smooth tanpa bounce kasar, menggunakan ring halus -->
                    <div class="mb-6 relative flex items-center justify-center">
                        <div class="absolute inset-0 rounded-full bg-emerald-500/10 animate-ping opacity-25 scale-75">
                        </div>
                        <div
                            class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 ring-8 ring-emerald-50/50">
                            <x-lucide-check class="w-10 h-10 text-emerald-500" />
                        </div>
                    </div>

                    <!-- Title & Subtitle -->
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight mb-1">Pembayaran Berhasil</h3>
                    <p class="text-sm text-gray-500 mb-6">Terima kasih, transaksi Anda telah kami proses.</p>

                    <!-- Detail Transaksi: Menggunakan layout Card yang lebih bersih & border tipis modern -->
                    <div
                        class="w-full bg-gray-50/60 rounded-2xl p-5 border border-gray-100/80 mb-6 space-y-3.5 text-sm">
                        <div class="flex justify-between items-center text-gray-500">
                            <span class="text-xs font-medium">ID Transaksi</span>
                            <span class="font-mono text-xs font-semibold text-gray-700 tracking-tight">
                                {{ data_get($paymentModalData, 'external_id') ?? '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-gray-500 border-t border-gray-100 pt-3.5">
                            <span class="text-xs font-medium">Metode Pembayaran</span>
                            <span class="text-xs font-semibold text-gray-800 uppercase tracking-wider">
                                {{ data_get($paymentModalData, 'bank') ?? (data_get($paymentModalData, 'store') ?? 'E-Wallet / QRIS') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-100 pt-3.5">
                            <span class="text-xs font-medium text-gray-500">Total Nominal</span>
                            <span class="font-bold text-gray-900">
                                Rp{{ number_format($totalAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Countdown Redirect: Dibuat sebagai footer text yang subtle, bukan badge mencolok -->
                    <div class="inline-flex items-center gap-2 text-xs text-gray-400 font-medium">
                        <x-lucide-loader-2 class="animate-spin h-3.5 w-3.5 text-gray-400" />
                        <span>Mengalihkan ke halaman pesanan dalam <b
                                class="font-mono text-gray-600 font-bold">{{ $successCountdown }}</b> detik...</span>
                    </div>

                </div>
            @else
                <div class="bg-[#FFF8E6] border border-[#FFE082] rounded-xl p-4 flex items-start gap-3 mb-2 mt-4">
                    <span class="w-3 h-3 rounded-full bg-[#FFB300] mt-1 shrink-0"></span>
                    <p class="text-xs text-gray-700 leading-relaxed">
                        Selesaikan pembayaran sebelum <strong
                            class="text-gray-900">{{ \Carbon\Carbon::parse(data_get($paymentModalData, 'expiry'))->translatedFormat('d M Y, H:i') }}
                            WIB</strong>. Pesanan akan dibatalkan otomatis jika melewati batas waktu.
                    </p>
                </div>
                {{-- ======================================================== --}}
                {{-- TAMPILAN KARTU UTAMA & INSTRUKSI PEMBAYARAN (BELUM BAYAR) --}}
                {{-- ======================================================== --}}
                @php
                    $qrUrl = data_get($paymentModalData, 'qr_url');
                    $paymentType = data_get($paymentModalData, 'payment_type');
                    $paymentCode = data_get($paymentModalData, 'payment_code');
                    $storeName = data_get($paymentModalData, 'store');

                    $isRetail = $paymentType === 'cstore' || !empty($paymentCode);
                @endphp

                @if (!empty($qrUrl))
                    <div
                        class="bg-white rounded-2xl p-6 relative overflow-hidden shadow-sm border border-gray-200 text-center mb-6">
                        <p class="text-[12px] font-bold tracking-widest text-gray-500 mb-4 uppercase">Scan QR Code untuk
                            Membayar</p>

                        <div class="inline-block p-4 bg-white border-2 border-dashed border-gray-300 rounded-2xl mb-4">
                            <img src="{{ $qrUrl }}" alt="QRIS Code" class="w-52 h-52 mx-auto object-contain">
                        </div>

                        <div class="flex justify-between border-t border-gray-100 pt-4 text-left mt-2">
                            <div>
                                <p class="text-[10px] text-gray-400 mb-1 uppercase">Metode</p>
                                <p class="font-bold text-gray-800">
                                    {{ strtoupper($paymentType === 'qris' ? 'QRIS' : $paymentType ?? 'E-WALLET') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1 uppercase">Total Nominal</p>
                                <p class="font-bold text-orange-500">Rp{{ number_format($totalAmount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif ($isRetail)
                    <div
                        class="bg-jti-primary rounded-2xl p-6 relative overflow-hidden shadow-lg border border-gray-800 mb-6">
                        <p class="text-[11px] font-bold tracking-widest text-gray-300 mb-2 uppercase">Kode Pembayaran /
                            Barcode</p>

                        <div class="flex justify-between items-center mb-6 relative z-10">
                            <p class="text-3xl font-extrabold text-jti-yellow tracking-widest">{{ $paymentCode }}</p>
                            <button x-data="{
                                textToCopy: '{{ data_get($paymentModalData, 'va_number') }}',
                                copied: false
                            }"
                                x-on:click="navigator.clipboard.writeText(textToCopy);copied = true;setTimeout(() => copied = false, 2000);"
                                type="button"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-jti-yellow/20 active:scale-95 transition">
                                <!-- Teks tombol akan berubah otomatis saat berhasil disalin -->
                                <x-lucide-copy
                                    class="w-5 h-5 text-jti-yellow group-hover:scale-110 transition-transform" />
                            </button>
                        </div>

                        <div class="flex justify-between border-t border-white/10 pt-4 relative z-10">
                            <div>
                                <p class="text-[10px] text-gray-400 mb-1 uppercase">Gerai Retail</p>
                                <p class="font-bold text-white">{{ strtoupper($storeName ?? 'MINIMARKET') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1 uppercase">Total Nominal</p>
                                <p class="font-bold text-jti-yellow">Rp{{ number_format($totalAmount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif (in_array($paymentType, ['bank_transfer', 'echannel']))
                    <div
                        class="bg-jti-primary rounded-2xl p-6 relative overflow-hidden shadow-lg border border-gray-800 mb-6">
                        <div class="absolute top-4 right-4 opacity-10">
                            <x-lucide-credit-card class="w-16 h-16 text-gray-300" />
                        </div>

                        <p class="text-[11px] font-bold tracking-widest text-gray-300 mb-2 uppercase">Nomor Virtual
                            Account</p>

                        <div class="flex justify-between items-center mb-6 relative z-10">
                            <p class="text-lg md:text-2xl font-extrabold text-jti-yellow tracking-widest">
                                {{ data_get($paymentModalData, 'bill_key') ?? data_get($paymentModalData, 'va_number') }}
                            </p>
                            <button x-data="{
                                textToCopy: '{{ data_get($paymentModalData, 'va_number') }}',
                                copied: false
                            }"
                                x-on:click="navigator.clipboard.writeText(textToCopy);copied = true;setTimeout(() => copied = false, 2000);"
                                type="button"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg  hover:bg-jti-yellow/20 active:scale-95 transition">
                                <!-- Teks tombol akan berubah otomatis saat berhasil disalin -->
                                <x-lucide-copy
                                    class="w-5 h-5 text-jti-yellow group-hover:scale-110 transition-transform" />
                            </button>
                        </div>

                        @if (data_get($paymentModalData, 'biller_code'))
                            <div class="mb-4 relative z-10">
                                <p class="text-[10px] text-gray-400 mb-1 uppercase">Kode Perusahaan (Biller Code)</p>
                                <p class="font-bold text-white">{{ data_get($paymentModalData, 'biller_code') }}</p>
                            </div>
                        @endif

                        <div class="flex justify-between border-t border-white/10 pt-4 relative z-10">
                            <div>
                                <p class="text-[10px] text-gray-400 mb-1 uppercase">Bank Penerima</p>
                                <p class="font-bold text-white">
                                    {{ strtoupper(data_get($paymentModalData, 'bank') ?? 'MANDIRI') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1 uppercase">Total Nominal</p>
                                <p class="font-bold text-jti-yellow">Rp{{ number_format($totalAmount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- SUB-LOGIC: BLOK TAB INSTRUKSI --}}
                @php
                    $rawInstructions = data_get($paymentModalData, 'instructions', []);
                    $instructions = [];
                    if (!empty($rawInstructions)) {
                        if (is_string(reset($rawInstructions))) {
                            $instructions = ['Cara Pembayaran' => $rawInstructions];
                        } else {
                            $instructions = $rawInstructions;
                        }
                    }
                    $firstKey = !empty($instructions) ? array_key_first((array) $instructions) : '';
                @endphp

                @if (!empty($instructions))
                    <div x-data="{ activeTab: '{{ $firstKey }}' }">
                        <div class="flex items-center mb-3">
                            <span class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Instruksi:</span>
                            @if (count($instructions) > 1)
                                <select x-model="activeTab"
                                    class="ml-2 text-sm font-bold text-gray-800 bg-transparent border-none focus:ring-0 cursor-pointer p-0">
                                    @foreach ($instructions as $method => $steps)
                                        <option value="{{ $method }}">{{ $method }}</option>
                                    @endforeach
                                </select>
                            @else
                                <span class="ml-2 text-sm font-bold text-gray-800">{{ $firstKey }}</span>
                            @endif
                        </div>

                        <div class="border border-gray-100 bg-white shadow-sm rounded-xl p-5 mb-4">
                            @foreach ($instructions as $method => $steps)
                                <div x-show="activeTab === '{{ $method }}'" style="display: none;"
                                    x-transition.opacity>
                                    <ul class="space-y-4">
                                        @foreach ($steps as $index => $step)
                                            <li class="flex items-start gap-3">
                                                <span
                                                    class="shrink-0 w-6 h-6 flex items-center justify-center bg-white border border-gray-200 shadow-sm rounded-md text-[11px] font-bold text-gray-700 mt-0.5">
                                                    {{ $index + 1 }}
                                                </span>
                                                <p class="text-sm text-gray-600 leading-relaxed">{{ $step }}
                                                </p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

        </div>

        {{-- FOOTER ACTION BUTTON --}}
        <div class="px-6 py-4 bg-white border-t border-gray-100">
            <button wire:click="closeMidtransPaymentModal"
                class="w-full py-3.5 {{ $isPaid ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-jti-yellow hover:bg-[#FFB300] text-gray-900' }} font-bold rounded-xl transition-colors">
                {{ $isPaid ? 'Selesai' : 'Tutup' }}
            </button>
        </div>

    </div>
</div>

@props(['show', 'vaData', 'transferMethodName', 'instructions'])

@if ($show && !empty($vaData))
    <div class="fixed inset-0 bg-slate-900/60 z-9999 flex items-center justify-center p-4 backdrop-blur-md"
        x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        @click.self="show = false; $wire.closeModal()">

        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-slate-200 flex flex-col"
            @click.stop>

            {{-- Header --}}
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="text-xl font-bold text-jti-primary tracking-tight">Instruksi Pembayaran</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        <p class="text-xs text-slate-500 font-medium">
                            Batas waktu: {{ \Carbon\Carbon::parse($vaData['expired_at'])->diffInHours(now()) }} jam ke
                            depan
                        </p>
                    </div>
                </div>
                <button @click="show = false; $wire.closeModal()"
                    class="p-2 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg"class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x-icon lucide-x">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-8 overflow-y-auto custom-scrollbar">

                {{-- VA Number Card --}}
                <div class="bg-jti-primary rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                    {{-- Decorative pattern --}}
                    <div class="absolute right-0 top-0 opacity-10 translate-x-4 -translate-y-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-credit-card-icon lucide-credit-card">
                            <rect width="20" height="14" x="2" y="5" rx="2" />
                            <line x1="2" x2="22" y1="10" y2="10" />
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <p class="text-[10px] text-slate-300 font-bold tracking-widest mb-2">NOMOR VIRTUAL ACCOUNT</p>
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-3xl font-mono font-bold text-jti-yellow tracking-widest">
                                {{ $vaData['va_number'] }}
                            </div>
                            <button
                                @click="navigator.clipboard.writeText('{{ $vaData['va_number'] }}'); $dispatch('notify', {message: 'Nomor VA berhasil disalin!'})"
                                class="p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-all active:scale-90 group border border-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5 text-jti-yellow group-hover:scale-110 transition" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-copy-icon lucide-copy">
                                    <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                    <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-8 grid grid-cols-2 gap-4 border-t border-white/10 pt-4">
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold mb-1">BANK PENERIMA</p>
                                <p class="font-bold text-sm">{{ $vaData['bank_name'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold mb-1">TOTAL NOMINAL</p>
                                <p class="font-bold text-sm text-jti-yellow">
                                    Rp{{ number_format($vaData['amount'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Transfer Steps --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 px-1">
                        <i data-lucide="info" class="w-4 h-4 text-jti-yellow"></i>
                        <p class="text-xs font-bold text-slate-500 tracking-wide">METODE: {{ $transferMethodName['name'] ?? 'Transfer' }}</p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl border border-slate-100 p-6 shadow-inner">
                        <ul class="space-y-5">
                            @forelse ($instructions as $index => $step)
                                <li class="flex gap-4">
                                    <span
                                        class="shrink-0 w-6 h-6 bg-white border border-slate-200 text-jti-primary text-[11px] font-bold flex items-center justify-center rounded-lg shadow-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <p class="text-sm text-slate-600 leading-relaxed">{{ $step }}</p>
                                </li>
                            @empty
                                <li class="text-sm text-slate-400 italic text-center">Instruksi tidak tersedia</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- Expiration Alert --}}
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 flex gap-4 items-start">
                    <div class="bg-amber-400 p-2 rounded-lg shrink-0 shadow-sm">
                        <i data-lucide="clock" class="w-4 h-4 text-white"></i>
                    </div>
                    <p class="text-sm text-amber-900 leading-relaxed">
                        Selesaikan pembayaran sebelum <span
                            class="font-bold underline">{{ \Carbon\Carbon::parse($vaData['expired_at'])->format('d M Y, H:i') }}
                            WIB</span>.
                        Pesanan akan dibatalkan otomatis jika melewati batas waktu.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
                <button @click="show = false; $wire.closeModal()"
                    class="w-full py-4 bg-jti-yellow text-jti-primary font-bold rounded-2xl hover:bg-amber-400 transition-all shadow-lg shadow-amber-200 active:scale-[0.98]">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endif

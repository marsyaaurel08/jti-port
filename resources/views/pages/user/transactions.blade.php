{{-- resources/views/pages/user/transactions.blade.php --}}
<x-user.layout.index :user="$user">
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold text-jti-primary tracking-tight">Riwayat Transaksi</h1>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto scrollbar-hide">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-linear-to-r from-gray-50/50 to-white">
                            <th class="px-8 py-5 text-[10px] font-semibold text-gray-400 capitalize tracking-widest">ID Order</th>
                            <th class="px-8 py-5 text-[10px] font-semibold text-gray-400 capitalize tracking-widest">Metode & Tanggal</th>
                            <th class="px-8 py-5 text-[10px] font-semibold text-gray-400 capitalize tracking-widest">Total Pembayaran</th>
                            <th class="px-8 py-5 text-[10px] font-semibold text-gray-400 capitalize tracking-widest">Status</th>
                            <th class="px-8 py-5 text-[10px] font-semibold text-gray-400 capitalize tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @forelse($transactions as $trx)
                            <tr class="group hover:bg-gray-50/80 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <span class="font-semibold text-jti-primary bg-jti-primary/5 px-3 py-1.5 rounded-lg">#{{ $trx->id }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-jti-primary">{{ $trx->payment_method ?? 'Bank Transfer' }}</span>
                                        <span class="text-[11px] text-gray-400 font-medium">{{ $trx->created_at->format('d M Y, H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm">
                                    <span class="font-semibold text-jti-primary text-base">Rp{{ number_format($trx->total_price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $statusClasses = match(strtolower($trx->status)) {
                                            'success', 'settlement' => 'bg-emerald-50 text-emerald-600 ring-emerald-500/20',
                                            'pending' => 'bg-amber-50 text-amber-600 ring-amber-500/20',
                                            default => 'bg-rose-50 text-rose-600 ring-rose-500/20',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-semibold capitalize tracking-widest ring-1 1ing-inset {{ $statusClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                        {{ $trx->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <a href="#" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-100 shadow-sm text-gray-400 hover:text-jti-orange hover:border-jti-orange transition-all group-hover:shadow-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                        <p class="text-gray-500 font-bold">Belum ada transaksi</p>
                                        <p class="text-xs text-gray-400 mt-1">Semua pembelian kursus akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            @if($transactions->hasPages())
                <div class="p-8 bg-gray-50/30 border-t border-gray-50">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-user.layout.index>

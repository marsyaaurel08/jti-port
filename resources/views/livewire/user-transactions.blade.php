<div class="bg-[#F8FAFC] min-h-screen">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-24 py-12 md:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            <div class="lg:col-span-3" wire:ignore>
                <div class="sticky top-26">
                    <x-user.sidebar :user="$this->user" />
                </div>
            </div>

            <div class="lg:col-span-9 space-y-10">
                @if (count($this->transactions) === 0)
                    <div
                        class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 p-16 text-center">
                        <div class="relative inline-flex mb-8">
                            <div class="absolute inset-0 bg-jti-primary/10 rounded-3xl blur-xl animate-pulse"></div>
                            <div
                                class="relative w-20 h-20 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center justify-center text-slate-400">
                                <i data-lucide="receipt-text" class="w-10 h-10" style="stroke-width: 1.5px;"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3 tracking-tight">Belum Ada Catatan Transaksi
                        </h3>
                        <p class="text-slate-500 max-w-sm mx-auto mb-10 leading-relaxed text-lg">
                            Mulai tingkatkan skill Anda dengan memilih kursus sertifikasi unggulan hari ini.
                        </p>
                        <a href="{{ route('courses.index') }}"
                            class="inline-flex items-center gap-3 px-8 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-jti-primary transition-all duration-300 shadow-xl shadow-slate-900/10 hover:shadow-jti-primary/20 active:scale-95 group">
                            Mulai Eksplorasi Kursus
                            <i data-lucide="arrow-right"
                                class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                @else
                    <section
                        class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-50/50 border-b border-slate-100">
                                        <th class="px-8 py-6 text-left text-xs font-bold text-slate-400 uppercase tracking-widest cursor-pointer hover:text-jti-primary transition-colors"
                                            wire:click="sort('invoice_number')">
                                            <div class="flex items-center gap-2">
                                                <span>Invoice</span>
                                                @if ($sortBy === 'invoice_number')
                                                    <i data-lucide="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                                        class="w-3 h-3 text-jti-primary"></i>
                                                @endif
                                            </div>
                                        </th>
                                        <th class="px-8 py-6 text-left text-xs font-bold text-slate-400 uppercase tracking-widest cursor-pointer hover:text-jti-primary transition-colors"
                                            wire:click="sort('created_at')">
                                            <div class="flex items-center gap-2">
                                                <span>Tanggal</span>
                                                @if ($sortBy === 'created_at')
                                                    <i data-lucide="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                                        class="w-3 h-3 text-jti-primary"></i>
                                                @endif
                                            </div>
                                        </th>
                                        <th class="px-8 py-6 text-left text-xs font-bold text-slate-400 uppercase tracking-widest cursor-pointer hover:text-jti-primary transition-colors"
                                            wire:click="sort('total_amount')">
                                            <div class="flex items-center gap-2">
                                                <span>Total</span>
                                                @if ($sortBy === 'total_amount')
                                                    <i data-lucide="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                                        class="w-3 h-3 text-jti-primary"></i>
                                                @endif
                                            </div>
                                        </th>
                                        <th
                                            class="px-8 py-6 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">
                                            Status</th>
                                        <th
                                            class="px-8 py-6 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">
                                            Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50" wire:key="transactions-table">
                                    @foreach ($this->transactions as $transaction)
                                        <tr class="hover:bg-slate-50/50 transition-colors duration-200 group"
                                            wire:key="transaction-{{ $transaction->id }}">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="p-2 bg-slate-100 rounded-lg group-hover:bg-white transition-colors">
                                                        <i data-lucide="hash" class="w-4 h-4 text-slate-500"></i>
                                                    </div>
                                                    <span
                                                        class="font-bold text-slate-800 tracking-tight">{{ $transaction->invoice_number }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="text-sm font-medium text-slate-500">{{ $transaction->created_at->format('d M, Y') }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-sm font-extrabold text-slate-900">
                                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-8 py-6">
                                                @php
                                                    $statusColor = $this->getStatusColor($transaction->status);
                                                    $statusLabel = $this->getStatusLabel($transaction->status);

                                                    $statusClasses = match ($statusColor) {
                                                        'green' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                        'yellow' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                        'red' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                        default => 'bg-slate-50 text-slate-500 border-slate-100',
                                                    };
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-extrabold uppercase tracking-widest border {{ $statusClasses }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6">
                                                <a href="#"
                                                    class="inline-flex items-center gap-2 text-xs font-bold text-jti-primary hover:text-jti-maroon transition-colors group/link">
                                                    Lihat Invoice
                                                    <i data-lucide="external-link"
                                                        class="w-3.5 h-3.5 group-hover/link:translate-x-0.5 group-hover/link:-translate-y-0.5 transition-transform"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="px-8 py-8 bg-slate-50/50 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                                    Halaman {{ $this->transactions->currentPage() }} dari
                                    {{ $this->transactions->lastPage() }}
                                </p>
                                <div class="pagination-custom">
                                    {{ $this->transactions->links() }}
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>

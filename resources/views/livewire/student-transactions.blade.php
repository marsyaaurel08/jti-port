<x-layouts.user :user="$this->user">
    @if (count($this->transactions) === 0)
        {{-- Empty State --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-20 text-center">
            <div class="relative inline-flex mb-6">
                <div class="absolute inset-0 bg-jti-yellow/15 rounded-full blur-2xl"></div>
                <div class="relative w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                    <x-lucide-file-text class="w-10 h-10" style="stroke-width: 1.5px;" />
                </div>
            </div>
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Transaksi</h1>
                <p class="text-gray-600 mt-2">Pantau status pembayaran dan riwayat transaksi Anda</p>
            </div>
            <a href="{{ route('courses.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-jti-primary text-white text-sm font-medium rounded-xl hover:shadow-lg transition-all active:scale-95 group">
                Mulai eksplorasi kursus
                <x-lucide-arrow-right class="w-4 h-4" style="stroke-width: 2px;" />
            </a>
        </div>
    @else
        <div class="space-y-6">
            {{-- Header & Sorting Minimalis --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-2">
                <div>
                    <h2 class="text-2xl font-semibold text-jti-primary tracking-tight">Riwayat transaksi</h2>
                    <p class="text-sm text-slate-500 font-medium">Pantau status pembayaran dan unduh invoice</p>
                </div>

                {{-- Toggle Sorting yang bersih --}}
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                    <button wire:click="sort('created_at')"
                        class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all {{ $sortBy === 'created_at' ? 'bg-white text-jti-primary shadow-sm' : 'text-slate-500 hover:text-jti-primary' }}">
                        Terbaru
                    </button>
                    <button wire:click="sort('total_amount')"
                        class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all {{ $sortBy === 'total_amount' ? 'bg-white text-jti-primary shadow-sm' : 'text-slate-500 hover:text-jti-primary' }}">
                        Nominal
                    </button>
                </div>
            </div>

            {{-- List Transaksi --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="divide-y divide-slate-50">
                    @foreach ($this->transactions as $transaction)
                        @php
                            $statusColor = $this->getStatusColor($transaction->status);
                            $statusLabel = $this->getStatusLabel($transaction->status);

                            $statusStyle = match ($statusColor) {
                                'green' => 'bg-emerald-50 text-emerald-600 border-emerald-100/50',
                                'yellow' => 'bg-jti-yellow/10 text-jti-maroon border-jti-yellow/20',
                                'red' => 'bg-rose-50 text-rose-600 border-rose-100/50',
                                default => 'bg-slate-50 text-slate-500 border-slate-100',
                            };
                        @endphp

                        <div wire:key="transaction-{{ $transaction->id }}"
                            class="group flex flex-col md:flex-row md:items-center gap-6 p-6 sm:p-8 hover:bg-slate-50/50 transition-colors">

                            {{-- Info Utama --}}
                            <div class="flex items-center gap-5 flex-1 min-w-0">
                                <div
                                    class="w-12 h-12 shrink-0 bg-jti-light rounded-xl flex items-center justify-center text-jti-primary group-hover:bg-jti-yellow/20 transition-colors">
                                    <x-lucide-file-text class="w-6 h-6" style="stroke-width: 1.5px;" />
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span
                                            class="text-sm font-semibold text-slate-900 truncate capitalize">{{ $transaction->invoice_number }}</span>
                                        <span
                                            class="text-[11px] font-medium text-slate-400 shrink-0">{{ $transaction->created_at->format('d M, Y') }}</span>
                                    </div>
                                    @php
                                        $firstItem = $transaction->orderItems->first();
                                        $typeLabel =
                                            $firstItem && $firstItem->class_id
                                                ? 'Pembayaran kelas'
                                                : 'Pembayaran course';
                                        
                                        $productName = $firstItem ? ($firstItem->item_name ?? ($firstItem->class_id ? optional($firstItem->class)->title : optional($firstItem->course)->title)) : '';
                                    @endphp
                                    <p class="text-xs text-slate-500 font-medium">
                                        {{ $typeLabel }}
                                        @if($productName)
                                            - <span class="text-slate-700">{{ Str::limit($productName, 35) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Harga & Status --}}
                            <div class="flex items-center justify-between md:justify-end gap-10">
                                <div class="md:text-right shrink-0">
                                    <span class="text-[11px] font-medium text-slate-400 block mb-0.5">Total Bayar</span>
                                    <span class="text-sm font-semibold text-jti-primary">
                                        Rp. {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0 min-w-[100px]">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-[11px] font-semibold border capitalize {{ $statusStyle }}">
                                        {{ strtolower($statusLabel) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination yang lebih tenang --}}
                <div class="px-8 py-6 bg-slate-50/30 border-t border-slate-50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-[11px] font-medium text-slate-400">
                            menampilkan halaman {{ $this->transactions->currentPage() }} dari
                            {{ $this->transactions->lastPage() }}
                        </p>
                        <div class="pagination-minimalist">
                            {{ $this->transactions->links('components.pagination.custom-tailwind') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-layouts.user>

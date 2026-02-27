<div class="bg-[#FAFBFC] min-h-screen">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-48 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 lg:gap-16">
            <!-- Sidebar -->
            <div class="lg:col-span-1" wire:ignore>
                <x-user.sidebar :user="$this->user" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-12">
                <!-- Header Section -->
                <section class="relative">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-2 h-6 bg-jti-yellow rounded-full"></span>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">Riwayat Transaksi</h1>
                    </div>
                    <p class="text-gray-600 text-lg">Kelola dan lihat semua riwayat transaksi pembayaran Anda</p>
                </section>

                @if (count($this->transactions) === 0)
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-gray-100 mb-6">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Transaksi</h3>
                        <p class="text-gray-600 mb-6">Riwayat transaksi Anda akan muncul di sini setelah Anda melakukan
                            pembayaran</p>
                        <a href="{{ route('courses.index') }}"
                            class="inline-block px-6 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-semibold rounded-xl transition-colors">
                            Mulai Belanja
                        </a>
                    </div>
                @else
                    <!-- Transaction Table -->
                    <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 cursor-pointer hover:bg-gray-100 transition-colors"
                                            wire:click="sort('invoice_number')">
                                            <div class="flex items-center gap-2">
                                                <span>Invoice</span>
                                                @if ($sortBy === 'invoice_number')
                                                    <span
                                                        class="text-jti-yellow">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </div>
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 cursor-pointer hover:bg-gray-100 transition-colors"
                                            wire:click="sort('created_at')">
                                            <div class="flex items-center gap-2">
                                                <span>Tanggal</span>
                                                @if ($sortBy === 'created_at')
                                                    <span
                                                        class="text-jti-yellow">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </div>
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 cursor-pointer hover:bg-gray-100 transition-colors"
                                            wire:click="sort('total_amount')">
                                            <div class="flex items-center gap-2">
                                                <span>Total</span>
                                                @if ($sortBy === 'total_amount')
                                                    <span
                                                        class="text-jti-yellow">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </div>
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Status</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200" wire:key="transactions-table">
                                    @forelse ($this->transactions as $transaction)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150"
                                            wire:key="transaction-{{ $transaction->id }}">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                                #{{ $transaction->invoice_number }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $transaction->created_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @php
                                                    $statusColor = $this->getStatusColor($transaction->status);
                                                    $statusLabel = $this->getStatusLabel($transaction->status);
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                                    @if ($statusColor === 'green') bg-emerald-100 text-emerald-700
                                                    @elseif ($statusColor === 'yellow')
                                                        bg-amber-100 text-amber-700
                                                    @elseif ($statusColor === 'red')
                                                        bg-red-100 text-red-700
                                                    @else
                                                        bg-gray-100 text-gray-700 @endif
                                                    ">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <a href="#"
                                                    class="text-jti-yellow hover:text-jti-primary font-semibold transition-colors hover:underline">
                                                    Lihat Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-6 border-t border-gray-200 bg-gray-50">
                            <div class="flex justify-center">
                                {{ $this->transactions->links() }}
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>

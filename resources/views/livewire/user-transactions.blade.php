<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <x-user.sidebar :user="$this->user" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Riwayat Transaksi</h1>
                    <p class="text-gray-600 mt-2">Lihat semua transaksi pembayaran Anda</p>
                </div>

                @if (count($this->transactions) === 0)
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Transaksi</h3>
                        <p class="text-gray-600">Riwayat transaksi Anda akan muncul di sini</p>
                    </div>
                @else
                    <!-- Transaction Table -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 cursor-pointer hover:bg-gray-100"
                                            wire:click="sort('invoice_number')">
                                            Invoice
                                            <span class="ml-1">
                                                @if ($sortBy === 'invoice_number')
                                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                                @endif
                                            </span>
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 cursor-pointer hover:bg-gray-100"
                                            wire:click="sort('created_at')">
                                            Tanggal
                                            <span class="ml-1">
                                                @if ($sortBy === 'created_at')
                                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                                @endif
                                            </span>
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 cursor-pointer hover:bg-gray-100"
                                            wire:click="sort('total_amount')">
                                            Total
                                            <span class="ml-1">
                                                @if ($sortBy === 'total_amount')
                                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                                @endif
                                            </span>
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">
                                            Status
                                        </th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" wire:key="transactions-table">
                                    @forelse ($this->transactions as $transaction)
                                        <tr class="hover:bg-gray-50 transition-colors"
                                            wire:key="transaction-{{ $transaction->id }}">
                                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                                {{ $transaction->invoice_number }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $transaction->created_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @php
                                                    $statusColor = $this->getStatusColor($transaction->status);
                                                    $statusLabel = $this->getStatusLabel($transaction->status);
                                                @endphp
                                                <span
                                                    class="inline-block px-3 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 rounded-full text-xs font-semibold">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <a href="#"
                                                    class="text-jti-yellow hover:text-jti-maroon font-medium">
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
                        <div class="px-6 py-4 border-t">
                            {{ $this->transactions->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

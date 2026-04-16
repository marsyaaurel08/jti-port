<div>
    @if (!empty($this->quizAttempts))
        <div class="mt-6 border-t border-gray-200 pt-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Riwayat Pengerjaan</h3>

        <p class="text-sm text-gray-600 mb-6">
            Anda telah mencoba kuis ini sebanyak {{ $this->quizStats['total_attempts'] }} kali
        </p>

        <!-- Summary Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nilai Tertinggi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $this->quizStats['best_score'] }}%</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Rata-rata</p>
                <p class="text-2xl font-bold text-gray-900">{{ $this->quizStats['average_score'] }}%</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Status Terakhir</p>
                <p class="text-2xl font-bold {{ $this->quizStats['last_attempt_status'] === 'Lulus' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $this->quizStats['last_attempt_status'] }}
                </p>
            </div>
        </div>

        <!-- Attempts Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">#</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Nilai</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Waktu</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->quizAttempts as $attempt)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">{{ $attempt['attempt_number'] }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $attempt['score'] }}%</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $attempt['finished_at']->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $this->formatTimeTaken($attempt['time_taken']) }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($attempt['is_passed'])
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                        Lulus
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                        Gagal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    @else
        <div class="mt-6 border-t border-gray-200 pt-6">
            <p class="text-sm text-gray-600">Belum ada riwayat pengerjaan kuis.</p>
        </div>
    @endif
</div>

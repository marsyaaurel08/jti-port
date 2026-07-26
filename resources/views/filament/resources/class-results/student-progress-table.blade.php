@php
    $enrollments = $getRecord()->enrollments()->with(['user', 'course'])->get();
@endphp

<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
            <tr>
                <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">No</th>
                <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Nama Mahasiswa</th>
                <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Email</th>
                <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Progres</th>
                <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Tanggal Daftar</th>
                <th class="px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
            @forelse ($enrollments as $index => $enrollment)
                @php
                    $total = $enrollment->course->total_lessons ?? 0;
                    $completed = $enrollment->actual_completed_lesson_count ?? 0;
                    $percent = $total > 0 ? ($completed / $total) * 100 : 0;
                    $isCompleted = $enrollment->is_completed;
                @endphp
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                    {{-- No --}}
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                        {{ $index + 1 }}
                    </td>

                    {{-- Nama --}}
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                        <div class="flex items-center gap-2">
                            <x-heroicon-m-user class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" />
                            {{ $enrollment->user->name ?? '-' }}
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        {{ $enrollment->user->email ?? '-' }}
                    </td>

                    {{-- Progres --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1.5 min-w-[160px]">
                            {{-- Progress bar --}}
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div
                                    class="h-2 rounded-full transition-all duration-500 {{ $percent >= 100 ? 'bg-emerald-500' : ($percent >= 50 ? 'bg-amber-500' : 'bg-sky-500') }}"
                                    style="width: {{ min($percent, 100) }}%"
                                ></div>
                            </div>
                            {{-- Label --}}
                            <span class="text-xs font-medium {{ $percent >= 100 ? 'text-emerald-600 dark:text-emerald-400' : ($percent >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-sky-600 dark:text-sky-400') }}">
                                {{ number_format($percent, 1) }}% ({{ $completed }}/{{ $total }} Lessons)
                            </span>
                        </div>
                    </td>

                    {{-- Tanggal Daftar --}}
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-m-calendar class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" />
                            {{ $enrollment->enrolled_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3">
                        @if ($isCompleted)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-emerald-600/20 dark:ring-emerald-500/30">
                                <x-heroicon-m-check-circle class="w-3.5 h-3.5" />
                                Selesai {{ $enrollment->completed_at?->format('d M Y') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 ring-1 ring-amber-600/20 dark:ring-amber-500/30">
                                <x-heroicon-m-clock class="w-3.5 h-3.5" />
                                Belum Selesai
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                        <div class="flex flex-col items-center gap-2">
                            <x-heroicon-o-users class="w-8 h-8" />
                            <span>Belum ada mahasiswa yang terdaftar</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer summary --}}
    @if ($enrollments->isNotEmpty())
        <div class="px-4 py-3 bg-gray-50 dark:bg-white/5 border-t border-gray-200 dark:border-white/10 text-sm text-gray-500 dark:text-gray-400">
            Total: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $enrollments->count() }}</span> mahasiswa terdaftar
            &middot;
            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $enrollments->where('is_completed', true)->count() }}</span> selesai
            &middot;
            <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $enrollments->where('is_completed', false)->count() }}</span> dalam progres
        </div>
    @endif
</div>

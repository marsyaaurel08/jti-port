{{-- resources/views/pages/user/index.blade.php --}}
<x-user.layout.index :user="$user">
    <div class="space-y-10">

        {{-- Welcome Header (Opsional tapi mempercantik) --}}
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold text-jti-primary tracking-tight">Halo, {{ explode(' ', $user->name)[0] }}! 👋</h1>
            <p class="text-gray-500 text-sm">Ayo lanjutkan progres belajarmu hari ini.</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $statItems = [
                    ['label' => 'Total Kursus', 'value' => $stats['total_course'], 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                    ['label' => 'Selesai', 'value' => $stats['completed'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                    ['label' => 'Transaksi', 'value' => $stats['transactions'], 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                ];
            @endphp
            @foreach($statItems as $item)
                <div class="group bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl {{ $item['bg'] }} {{ $item['text'] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-[0.15em] mb-0.5">{{ $item['label'] }}</p>
                        <p class="text-2xl font-semibold text-jti-primary leading-none">{{ $item['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Recent Courses --}}
        <section>
            <div class="flex items-end justify-between mb-8">
                <div class="space-y-1">
                    <h3 class="text-xl font-semibold text-jti-primary tracking-tight">Lanjutkan Belajar</h3>
                    <div class="h-1 w-12 bg-jti-orange rounded-full"></div>
                </div>
                <a href="{{ route('user.enrolled') }}" class="group flex items-center gap-2 text-sm font-semibold text-jti-orange transition-all">
                    <span>Lihat Semua</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>

            @if(count($recent_courses) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($recent_courses as $enrollment)
                        <div class="hover:-translate-y-2 transition-transform duration-300">
                            <x-course.card :course="$enrollment->course" />
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State jika belum ada kursus --}}
                <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-12 text-center">
                    <p class="text-gray-400 font-medium">Belum ada kursus yang diikuti.</p>
                    <a href="{{ route('courses.index') }}" class="mt-4 inline-block text-jti-primary font-semibold hover:underline">Cari Kursus Sekarang →</a>
                </div>
            @endif
        </section>
    </div>
</x-user.layout.index>

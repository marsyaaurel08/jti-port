@props(['chapter'])

<div x-data="{ open: false }" class="border-b border-gray-100 last:border-0 shadow-sm mb-2 rounded-lg overflow-hidden">
    {{-- Header Chapter --}}
    <button @click="open = !open" class="w-full flex items-center justify-between p-5 bg-gray-50/80 hover:bg-gray-100 transition-all text-left">
        <div class="flex items-center gap-4">
            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 transition-transform duration-200 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <span class="font-bold text-gray-800 text-lg">{{ $chapter->title }}</span>
        </div>
        <div class="text-right">
            <span class="block text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $chapter->lessons->count() }} Materi</span>
            {{-- Menghitung total durasi satu chapter (Opsional) --}}
            <span class="text-[10px] text-gray-400">{{ floor($chapter->lessons->sum('duration_seconds') / 60) }} Menit</span>
        </div>
    </button>

    {{-- List Lesson --}}
    <div x-show="open" x-transition class="bg-white px-5 py-2">
        @foreach($chapter->lessons as $lesson)
            <div class="flex items-center justify-between py-4 border-b border-gray-50 last:border-0 group">
                <div class="flex items-center gap-4">
                    {{-- Icon Berdasarkan Type --}}
                    <div class="flex-shrink-0">
                        @switch($lesson->type)
                            @case('video')
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                @break
                            @case('text')
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                @break
                            @case('quiz')
                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                @break
                            @default
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        @endswitch
                    </div>

                    <div class="flex flex-col leading-tight">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition-colors">{{ $lesson->title }}</span>
                    </div>
                </div>

                {{-- Tampilan Durasi --}}
                <div class="flex items-center gap-1.5 text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[11px] font-mono font-bold">
                        @php
                            $minutes = floor($lesson->duration_seconds / 60);
                            $seconds = $lesson->duration_seconds % 60;
                        @endphp

                        @if($minutes > 0)
                            {{ $minutes }}m
                        @endif
                        {{ $seconds }}s
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>

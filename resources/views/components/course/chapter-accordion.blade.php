@props(['chapter'])

<div x-data="{ open: false }" class="mb-3 overflow-hidden transition-all duration-300 border border-gray-100 rounded-xl bg-white shadow-sm">
    {{-- Header Chapter --}}
    <button @click="open = !open"
        class="flex items-center justify-between w-full p-5 transition-all text-left group hover:bg-gray-50/50">
        <div class="flex items-center gap-5">
            {{-- Status Indicator: Kuning hanya saat open --}}
            <div :class="open ? 'bg-jti-yellow shadow-[0_0_12px_rgba(252,211,77,0.6)]' : 'bg-gray-200'"
                 class="w-1.5 h-1.5 rounded-full transition-all duration-500"></div>

            <div class="flex flex-col">
                {{-- Judul: Default abu-abu tua (gelap), Kuning saat hover atau active --}}
                <span :class="open ? 'text-jti-yellow' : 'text-gray-500 group-hover:text-jti-yellow'"
                      class="text-lg font-semibold tracking-tight transition-colors duration-300">
                    {{ $chapter->title }}
                </span>
                <span class="text-[11px] text-gray-400 font-medium">
                    {{ $chapter->lessons->count() }} materi · {{ floor($chapter->lessons->sum('duration_seconds') / 60) }} menit
                </span>
            </div>
        </div>

        {{-- Icon Arrow --}}
        <div :class="open ? 'text-jti-yellow bg-jti-yellow/10 border-jti-yellow/20' : 'text-gray-300 bg-gray-50 border-gray-100 group-hover:text-jti-yellow'"
             class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300 border">
            <svg :class="open ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    {{-- List Lesson --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="px-4 pb-4">

        <div class="space-y-1 pt-2 border-t border-gray-50">
            @foreach($chapter->lessons as $lesson)
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 group transition-all duration-200 cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="text-gray-300 group-hover:text-jti-yellow transition-colors">
                            @switch($lesson->type)
                                @case('video') <i data-lucide="play" class="w-4 h-4"></i> @break
                                @case('text') <i data-lucide="book-open" class="w-4 h-4"></i> @break
                                @case('quiz') <i data-lucide="help-circle" class="w-4 h-4"></i> @break
                                @default <i data-lucide="hash" class="w-4 h-4"></i>
                            @endswitch
                        </div>

                        <span class="text-sm font-medium text-gray-500 group-hover:text-gray-900 transition-colors">
                            {{ $lesson->title }}
                        </span>
                    </div>

                    <span class="text-[10px] font-medium text-gray-300 group-hover:text-gray-500 transition-colors tabular-nums">
                        {{ floor($lesson->duration_seconds / 60) }}m {{ $lesson->duration_seconds % 60 }}s
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

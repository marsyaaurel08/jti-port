@props(['chapter'])

<div x-data="{ open: false }" class="border-b border-gray-100 last:border-0">
    <button @click="open = !open" class="w-full flex items-center justify-between p-5 bg-gray-50/50 hover:bg-gray-100 transition-all text-left">
        <div class="flex items-center gap-4">
            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"/></svg>
            <span class="font-black text-jti-primary">{{ $chapter->title }}</span>
        </div>
        <span class="text-xs font-bold text-gray-400 italic">{{ $chapter->lessons->count() }} Materi</span>
    </button>

    <div x-show="open" x-transition class="bg-white px-5 py-2">
        @foreach($chapter->lessons as $lesson)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-4">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                    <span class="text-sm font-medium text-gray-600">{{ $lesson->title }}</span>
                </div>
                <span class="text-xs text-gray-400">{{ $lesson->duration }}</span>
            </div>
        @endforeach
    </div>
</div>

<div class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">

        {{-- Previous --}}
        <div>
            @if($prev)
                <a href="{{ $prev }}"
                   class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 19l-7-7 7-7" />
                    </svg>
                    Sebelumnya
                </a>
            @endif
        </div>

        {{-- Next --}}
        <div>
            @if($next)
                <a href="{{ $next }}"
                   class="flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline">
                    Selanjutnya
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @endif
        </div>

    </div>
</div>

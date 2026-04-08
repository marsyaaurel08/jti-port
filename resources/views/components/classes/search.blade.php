{{-- Search Input untuk Classes --}}
<div class=" group">
    <div class="relative">
        {{-- Icon Search & Loading Indicator --}}
        <div class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center justify-center w-5 h-5">
            {{-- Default Search Icon --}}
            <svg wire:loading.remove wire:target="search"
                class="w-full h-full text-gray-400 group-focus-within:text-jti-primary transition-colors duration-300"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>

            {{-- Spinner Loading (Muncul saat Livewire sedang fetch data) --}}
            <svg wire:loading wire:target="search" class="animate-spin w-full h-full text-jti-primary"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>

        {{-- Input Field --}}
        <input wire:model.live.debounce.350ms="search" type="text"
            placeholder="Cari kelas berdasarkan nama, instruktur, deskripsi..."
            class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl
                       shadow-sm hover:shadow-md focus:outline-none focus:ring-4 focus:ring-jti-primary/10
                       focus:border-jti-primary transition-all duration-300 placeholder:text-gray-400 text-gray-700" />

        {{-- Clear Button --}}
        @if ($search)
            <button wire:click="$set('search', '')"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200"
                title="Hapus pencarian">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Search Status Info --}}
    <div class="mt-3 px-1 min-h-[20px]">
        @if ($search)
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Hasil untuk:</span>
                <span class="px-2 py-0.5 bg-jti-primary/10 text-jti-primary font-semibold rounded-md">
                    {{ $search }}
                </span>

                @if ($classes->count() === 0)
                    <span class="text-red-500 ml-auto flex items-center gap-1 italic">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Kelas tidak ditemukan
                    </span>
                @else
                    <span class="text-gray-400 ml-auto">{{ $classes->count() }} Kelas ditemukan</span>
                @endif
            </div>
        @endif
    </div>
</div>

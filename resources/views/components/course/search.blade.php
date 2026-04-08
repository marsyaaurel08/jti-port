{{-- Search Input untuk Course --}}
<div class=" group"> {{-- Ditambahkan max-w agar tidak terlalu lebar di layar desktop --}}
    <div class="relative transition-all duration-300 transform">

        {{-- Search Icon --}}
        <div class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-600 group-focus-within:scale-110 transition-all duration-300"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        {{-- Input Field --}}
        <input wire:model.live.debounce.350ms="search" type="text"
            placeholder="Cari kursus berdasarkan judul, kategori..."
            class="w-full pl-12 pr-12 py-4 bg-white border border-gray-200 rounded-2xl shadow-sm
                       hover:border-gray-300 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500
                       transition-all duration-300 placeholder:text-gray-400 text-gray-700 font-medium" />

        {{-- Clear Button --}}
        @if ($search)
            <button wire:click="$set('search', '')"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-2 bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 rounded-xl transition-all duration-200 group/clear"
                title="Hapus pencarian">
                <svg class="w-5 h-5 transform group-hover/clear:rotate-90 transition-transform duration-200"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Search Info/Hint --}}
    <div class="h-6 mt-2 px-2"> {{-- Fixed height agar tidak 'melompat' saat text muncul --}}
        @if ($search)
            <p class="text-sm text-gray-500 animate-in fade-in slide-in-from-top-1 duration-300">
                Menampilkan hasil untuk "<span class="font-bold text-gray-800">{{ $search }}</span>"
                @if ($courses->count() === 0)
                    <span class="ml-2 text-red-500 font-medium">— Tidak ditemukan hasil</span>
                @else
                    <span class="ml-2 text-blue-600 font-medium">({{ $courses->count() }} kursus)</span>
                @endif
            </p>
        @endif
    </div>
</div>

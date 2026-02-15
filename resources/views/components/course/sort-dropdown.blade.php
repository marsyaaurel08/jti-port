@props(['sort' => 'latest'])

{{-- Sorting Dropdown --}}
<div class="flex items-center gap-4 bg-gray-50/50 p-2 rounded-xl border border-gray-100/50">
    <div class="flex items-center gap-3 ml-auto">
        <label for="sort-select" class="hidden sm:block text-[10px] font-black text-gray-400 uppercase tracking-widest">
            Urutkan Berdasarkan
        </label>

        <div class="relative group">
            <select wire:model.live="sort" id="sort-select"
                class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-black text-jti-primary focus:ring-2 focus:ring-jti-primary/20 focus:border-jti-primary shadow-sm cursor-pointer transition-all hover:border-jti-primary">
                <option value="latest">Materi Terbaru</option>
                <option value="price_low">Harga Termurah</option>
                <option value="price_high">Harga Termahal</option>
            </select>

            {{-- Custom Chevron Icon --}}
            <div
                class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-jti-primary group-hover:text-jti-orange transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Dropdown Sort --}}
<label class="hidden sm:block text-[10px] font-black text-gray-400 uppercase tracking-widest">Urutkan:</label>
<div class="relative group">
    <select wire:model.live="sort"
        class="appearance-none pl-4 pr-10 py-2 bg-white border border-gray-200 rounded-xl text-xs font-black text-jti-primary focus:ring-2 focus:ring-jti-primary/20 cursor-pointer shadow-sm">
        <option value="latest">Terbaru</option>
        <option value="price_low">Investasi Terendah</option>
        <option value="price_high">Investasi Tertinggi</option>
    </select>
    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-jti-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M19 9l-7 7-7-7" stroke-width="2.5" />
        </svg>
    </div>
</div>

@props(['sort' => 'latest'])

{{-- Sorting Dropdown --}}
<div class="flex items-center gap-4 bg-gray-50/50 p-2 rounded-xl border border-gray-100/50">
    <div class="flex items-center gap-3 ml-auto">
        {{-- Label diganti menjadi Icon --}}
        <label for="sort-select" class="text-gray-400 hover:text-jti-primary transition-colors cursor-pointer" title="Urutkan">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
            </svg>
        </label>

        <div class="relative group">
            <select wire:model.live="sort" id="sort-select"
                class="appearance-none pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-black text-jti-primary focus:ring-2 focus:ring-jti-primary/20 focus:border-jti-primary shadow-sm cursor-pointer transition-all hover:border-jti-primary">
                <option value="latest">Materi Terbaru</option>
                <option value="price_low">Termurah</option>
                <option value="price_high">Termahal</option>
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

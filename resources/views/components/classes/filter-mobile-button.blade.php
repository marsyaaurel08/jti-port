{{-- Button Filter Mobile --}}
<button @click="$dispatch('toggle-filter')"
    class="md:hidden inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-jti-primary to-jti-primary/90 text-white font-bold shadow-lg hover:shadow-xl hover:scale-105 rounded-xl transition-all active:scale-95 duration-200">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4.5h18m-18 5h18m-18 5h18m-18 5h18" />
    </svg>
    <span class="text-sm font-semibold">Filter</span>
</button>

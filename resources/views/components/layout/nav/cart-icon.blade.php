<div x-data="{ cartCount: {{ count(session('cart', [])) }} }" @cart-updated.window="cartCount = $event.detail.count" class="relative inline-block">

    <a href="{{ route('student.cart') }}"
        class="relative flex items-center justify-center p-2.5 rounded-xl transition-all duration-300 group hover:bg-jti-primary/10 text-jti-primary hover:text-jti-maroon"
        wire:navigate>

        <svg class="w-6 h-6 transform transition-transform duration-300 group-hover:scale-110" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
            </path>
        </svg>

        <template x-if="cartCount > 0">
            <span x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50"
                x-transition:enter-end="opacity-100 scale-100" x-text="cartCount"
                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-jti-orange text-white text-[10px] font-bold rounded-full ring-2 ring-white shadow-sm px-1">
            </span>
        </template>
    </a>
</div>

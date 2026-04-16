@if (
    !auth()->check() ||
        !auth()->user()->hasAnyRole(['admin', 'super_admin', 'pengajar']))
    <div x-data="{ cartCount: {{ count(session('cart', [])) }} }" @cart-updated.window="cartCount = $event.detail.count" class="relative inline-block">

        <a href="{{ route('student.cart') }}"
            class="relative flex items-center justify-center p-2.5 rounded-xl transition-all duration-300 group hover:bg-jti-primary/10 text-jti-primary hover:text-jti-maroon"
            wire:navigate>

            <i data-lucide="shopping-cart"
                class="w-6 h-6 transform transition-transform duration-300 group-hover:scale-110"></i>

            <template x-if="cartCount > 0">
                <span x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50"
                    x-transition:enter-end="opacity-100 scale-100" x-text="cartCount"
                    class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-jti-orange text-white text-[10px] font-bold rounded-full ring-2 ring-white shadow-sm px-1">
                </span>
            </template>
        </a>
    </div>
@endif

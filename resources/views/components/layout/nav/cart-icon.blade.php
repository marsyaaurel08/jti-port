{{-- resources/views/components/layout/nav/cart-icon.blade.php --}}
@php
    $cartCount = count(session('cart', []));
@endphp

<a href="{{ route('student.cart') }}" class="text-jti-primary hover:text-jti-maroon relative group transition-colors p-2">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>

    @if($cartCount > 0)
        <span class="absolute -top-1 -right-1 bg-jti-orange text-white text-[10px] px-1.5 py-0.5 rounded-full shadow-md font-black">
            {{ $cartCount }}
        </span>
    @endif
</a>

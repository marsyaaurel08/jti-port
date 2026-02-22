{{-- Global State untuk Navbar --}}
<nav x-data="{ openExplore: false, mobileMenu: false, subProfile: false }" class="bg-white border-b border-gray-100 sticky top-0 z-[9999] shadow-sm">

    <div class="mx-auto px-4 md:px-6 h-20 flex items-center justify-between md:justify-center gap-4 md:gap-6">

        {{-- Brand & Toggle --}}
        <div class="flex items-center gap-4">
            <button @click="mobileMenu = !mobileMenu"
                class="md:hidden p-2 text-gray-600 hover:text-jti-maroon transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>

            <a href="/" class="shrink-0 transition-transform hover:scale-105">
                <img src="{{ asset('img/logo-jti.png') }}" class="h-10 md:h-12 w-auto" alt="Logo JTI">
            </a>
        </div>

        {{-- Desktop Navigation --}}
        <x-layout.nav.desktop-menu :categories="$categories" />

        <x-layout.nav.search-bar />

        {{-- Right Side Actions --}}
        <div class="flex items-center gap-2 md:gap-5">
            {{-- Logika Pembeda Auth --}}
            @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                <x-layout.nav.cart-icon />
                @if ($isStaff)
                    {{-- Tampilkan untuk Admin/Pengajar --}}
                    <x-layout.nav.admin-auth />
                @else
                    {{-- Tampilkan untuk Student --}}
                    <x-layout.nav.user-auth />
                @endif
            @else
                {{-- Tampilkan jika belum login atau belum verified --}}
                <x-layout.nav.user-auth />
            @endif
        </div>
    </div>

    {{-- Mobile Sidebar Layer --}}
    <x-layout.nav.mobile-sidebar :categories="$categories" />
</nav>

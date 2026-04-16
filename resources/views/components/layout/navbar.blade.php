{{-- Global State untuk Navbar --}}
<nav x-data="{ openExplore: false, mobileMenu: false, subProfile: false }" class="bg-white border-b border-gray-100 fixed w-full top-0 z-9999 shadow-sm px-4 md:px-48">

    <div class="mx-auto h-20 flex items-center justify-between md:justify-center gap-4 md:gap-6">

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
        <div class="hidden md:flex items-center gap-4 mx-4">
            <a href="{{ route('courses.index') }}"
                class="text-[13px] font-semibold -[0.15em] text-jti-primary hover:text-jti-maroon transition-colors duration-300 relative group">
                Course
                <span
                    class="absolute -bottom-1 left-0 w-0 h-[2px] bg-jti-maroon transition-all duration-300 group-hover:w-full"></span>
            </a>
            <a href="{{ route('classes.index') }}"
                class="text-[13px] font-semibold -[0.15em] text-jti-primary hover:text-jti-maroon transition-colors duration-300 relative group">
                Kelas
                <span
                    class="absolute -bottom-1 left-0 w-0 h-[2px] bg-jti-maroon transition-all duration-300 group-hover:w-full"></span>
            </a>
        </div>

        <div class="flex gap-4 grow justify-end">
            {{-- <x-layout.nav.search-bar /> --}}
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

    </div>

    {{-- Mobile Sidebar Layer --}}
    <x-layout.nav.mobile-sidebar :categories="$categories" />
</nav>

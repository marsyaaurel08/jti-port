<nav x-data="{ openExplore: false, mobileMenu: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div
        class="max-w-[1440px] mx-auto px-4 md:px-6 h-20 flex items-center justify-between md:justify-start gap-4 md:gap-6">

        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-gray-600 hover:text-jti-maroon">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"></path>
                <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <a href="/" class="shrink-0 transition-transform hover:scale-105">
            <img src="{{ asset('img/logo-jti.png') }}" class="h-10 md:h-12 w-auto" alt="Logo JTI">
        </a>

        <div class="hidden md:block relative" @mouseenter="openExplore = true" @mouseleave="openExplore = false">
            <button :class="openExplore ? 'text-jti-maroon' : 'text-gray-700'"
                class="text-sm font-bold hover:text-jti-maroon px-2 py-7 transition-colors duration-200 flex items-center gap-1">
                Explore
                <svg :class="openExplore ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="openExplore" x-transition
                class="absolute left-0 w-72 bg-white border border-gray-100 shadow-2xl py-3 z-50 rounded-b-xl">
                <div class="px-4 py-2 text-[10px] font-black text-jti-maroon/50 uppercase tracking-widest">Semua
                    Kategori</div>
                @foreach ($categories as $category)
                    <a href="/courses?category={{ $category->slug }}"
                        class="flex justify-between items-center px-4 py-3 text-sm font-medium text-jti-primary hover:bg-jti-light hover:text-jti-orange transition-all group">
                        <span>{{ $category->name }}</span>
                        <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all text-jti-orange"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="hidden md:block grow">
            <div class="relative group">
                <div
                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-jti-orange transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" placeholder="Cari materi pelatihan..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-full py-3 pl-12 pr-4 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-jti-orange/20 focus:border-jti-orange transition-all">
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-5">
            <button class="md:hidden p-2 text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <a href="/cart" x-data="{ cartCount: 9 }"
                class="text-jti-primary hover:text-jti-maroon relative group transition-colors p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>

                <span x-show="cartCount > 0" x-text="cartCount"
                    class="absolute -top-1 -right-1 bg-jti-orange text-white text-[10px] font-black px-1.5 py-0.5 rounded-full shadow-md transition-all"
                    style="display: none;">
                </span>
            </a>

            <div class="hidden md:flex items-center gap-3">
                <a href="/login"
                    class="px-6 py-2.5 text-sm font-bold border border-jti-yellow text-jti-yellow hover:bg-jti-yellow hover:text-gray-800 transition-all rounded-lg">Log
                    in</a>
                <a href="/register"
                    class="px-6 py-2.5 text-sm font-bold bg-jti-yellow text-white hover:bg-jti-maroon transition-all shadow-lg shadow-jti-yellow/20 rounded-lg">Sign
                    up</a>
            </div>
        </div>
    </div>

    <div x-show="mobileMenu" class="fixed inset-0 z-40 md:hidden" style="display: none;">

        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="mobileMenu = false"
            class="fixed inset-0 bg-jti-primary/40 backdrop-blur-md">
        </div>

        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 -translate-x-full"
            class="relative bg-white w-80 h-full shadow-2xl p-6 overflow-y-auto flex flex-col gap-8">

            <div class="flex items-center justify-between">
                <img src="{{ asset('img/logo-jti.png') }}" class="h-8 w-auto" alt="Logo JTI">
                <button @click="mobileMenu = false" class="text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="flex flex-col gap-3">
                <a href="/login"
                    class="text-center py-3 font-bold border-2 border-jti-yellow text-jti-yellow rounded-xl hover:bg-jti-yellow/5">Log
                    in</a>
                <a href="/register"
                    class="text-center py-3 font-bold bg-jti-yellow text-white rounded-xl shadow-lg shadow-jti-yellow/20">Sign
                    up</a>
            </div>

            <div class="space-y-4">
                <h3 class="text-[11px] font-black text-jti-maroon uppercase tracking-[0.2em]">Kategori Materi</h3>
                <nav class="flex flex-col gap-2">
                    @foreach ($categories as $category)
                        <a href="/courses?category={{ $category->slug }}"
                            class="flex items-center justify-between py-2 font-semibold text-jti-primary border-b border-gray-50 hover:text-jti-orange">
                            {{ $category->name }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="mt-auto border-t pt-6 text-center">
                <p class="text-xs text-gray-400 font-medium">© 2026 Jtiport Polinema</p>
            </div>
        </div>
    </div>
</nav>

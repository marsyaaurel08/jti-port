@props(['categories'])

<div x-show="mobileMenu" class="fixed inset-0 z-9999 md:hidden" style="display: none;">
    {{-- Overlay --}}
    <div x-show="mobileMenu" x-transition.opacity @click="mobileMenu = false; subProfile = false"
        class="fixed inset-0 bg-jti-primary/40 backdrop-blur-sm"></div>

    {{-- Sidebar Container --}}
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="relative bg-white w-80 h-full shadow-2xl flex flex-col overflow-hidden">

        {{-- LAYER 1: Menu Utama --}}
        <div class="flex flex-col h-full transition-transform duration-300"
            :class="subProfile ? '-translate-x-full' : 'translate-x-0'">

            @auth
                {{-- Profile Header (Trigger Submenu) --}}
                <div @click="subProfile = true"
                    class="bg-gray-50 p-6 flex items-center justify-between border-b border-gray-100 cursor-pointer group">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-16 h-16 rounded-full bg-jti-primary flex items-center justify-center text-white text-2xl shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="absolute top-0 right-0 w-4 h-4 bg-jti-orange border-2 border-white rounded-full shadow-sm">
                            </div>
                        </div>
                        <div>
                            <h2 class="text-jti-primary text-xl font-semibold leading-tight">Hi,
                                {{ Auth::user()->name }}
                            </h2>
                            <p class="text-gray-500 text-xs font-medium">Welcome back</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 5l7 7-7 7" stroke-width="2.5" />
                    </svg>
                </div>
            @endauth

            @guest
                <div class="p-6 border-b flex justify-between items-center">
                    <img src="{{ asset('img/logo-jti.png') }}" class="h-8 w-auto">
                    <button @click="mobileMenu = false">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 flex flex-col gap-3 border-b">
                    <a href="{{ route('filament.admin.auth.login') }}"
                        class="text-center py-3 font-semibold border-2 border-jti-primary text-jti-primary rounded-xl">Log in</a>
                    <a href="/register"
                        class="text-center py-3 font-semibold bg-jti-primary text-white rounded-xl">Sign up</a>
                </div>
            @endguest

            <div class="py-4 grow overflow-y-auto">
                <h3 class="px-6 py-2 text-[10px] text-gray-400 capitalize tracking-widest font-bold">Kategori</h3>
                @foreach ($categories as $category)
                    <a href="/courses?category={{ $category->slug }}"
                        class="flex items-center justify-between px-6 py-3 font-semibold text-gray-700 hover:bg-jti-light/30 transition-all">
                        {{ $category->name }}
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7" stroke-width="2.5" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- LAYER 2: Submenu Profil (Hanya muncul jika sudah login) --}}
        @auth
            <div class="absolute inset-0 bg-white flex flex-col transition-transform duration-300 z-50"
                x-show="subProfile" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full" style="display: none;">

                <div @click="subProfile = false"
                    class="p-6 flex items-center gap-4 border-b border-gray-100 bg-gray-50 cursor-pointer capitalize text-xs tracking-widest text-jti-primary font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" stroke-width="2.5" />
                    </svg>
                    Kembali
                </div>

                <div class="py-2 grow overflow-y-auto">
                    <div class="px-6 py-4 border-b border-gray-50 mb-2">
                        <p class="text-jti-primary truncate text-lg font-bold">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>

                    <a href="{{ route('user.index') }}"
                        class="block px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30">Dashboard Saya</a>
                    <a href="{{ route('user.enrolled') }}"
                        class="block px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30">Kursus Saya</a>
                    <a href="/cart"
                        class="flex items-center justify-between px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30 transition-all">
                        <span>Keranjang Belanja</span>
                        <span class="bg-jti-orange text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full">9</span>
                    </a>
                    <a href="{{ route('user.transactions') }}"
                        class="block px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30">Riwayat Transaksi</a>
                    <a href="{{ route('user.profile') }}"
                        class="block px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30">Edit Profil</a>
                </div>

                <div class="p-4 border-t border-gray-100 bg-red-50/50">
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-4 py-3 text-sm text-red-500 font-bold rounded-lg hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1h8M7 4H4v16h3" stroke-width="2.5" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</div>

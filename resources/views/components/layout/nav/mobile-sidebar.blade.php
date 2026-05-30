{{-- resources/views/components/navbar/mobile-menu.blade.php --}}
@props(['user', 'cartCount' => 0])

<div x-show="mobileMenu" class="fixed inset-0 z-9999 md:hidden" style="display: none;">
    {{-- Overlay: Blur lebih intens untuk fokus --}}
    <div x-show="mobileMenu"
        x-transition:enter="transition opacity-ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition opacity-ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileMenu = false; setTimeout(() => subProfile = false, 300)"
        class="fixed inset-0 bg-jti-primary/40 backdrop-blur-md"></div>

    {{-- Sidebar Container --}}
    <div x-show="mobileMenu"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="relative bg-white w-[85%] max-w-[320px] h-full shadow-2xl flex flex-col overflow-hidden rounded-r-4xl">

        {{-- LAYER 1: Menu Utama --}}
        <div class="flex flex-col h-full transition-transform duration-500"
            :class="subProfile ? '-translate-x-full opacity-0' : 'translate-x-0 opacity-100'">

            @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                {{-- Profile Header --}}
                <div @click="subProfile = true"
                    class="bg-jti-primary p-6 pt-10 pb-8 flex items-center justify-between cursor-pointer group relative overflow-hidden">
                    {{-- Decorative Circle --}}
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>

                    <div class="flex items-center gap-4 relative z-10">
                        <div class="relative">
                            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-lg border border-white/30 flex items-center justify-center text-white text-2xl font-bold shadow-xl">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-4 border-jti-primary rounded-full"></div>
                        </div>
                        <div>
                            <p class="text-white text-[10px] font-bold capitalize tracking-widest mb-1">Selamat Datang 👋</p>
                            <h2 class="text-white text-xl font-bold leading-tight">
                                {{  Auth::user()->name }}
                            </h2>
                        </div>
                    </div>
                    <div class="bg-white/10 p-2 rounded-xl backdrop-blur-sm group-hover:bg-white/20 transition-all">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            @else
                <div class="p-8 border-b flex justify-center bg-gray-50/50">
                    <img src="{{ asset('img/logo-jti.png') }}" class="h-10 w-auto">
                </div>
                <div class="p-6 flex flex-col gap-3">
                    <a href="{{ route('filament.user.auth.login') }}"
                        class="w-full py-4 font-bold text-xs uppercase tracking-widest text-center border-2 border-jti-primary text-jti-primary rounded-2xl active:scale-[0.97] transition-all">
                        Masuk
                    </a>
                    <a href="{{ route('filament.user.auth.register') }}"
                        class="w-full py-4 font-bold text-xs uppercase tracking-widest text-center bg-jti-primary text-white rounded-2xl shadow-lg shadow-jti-primary/20 active:scale-[0.97] transition-all">
                        Daftar Sekarang
                    </a>
                </div>
            @endif

            {{-- Navigasi Explore --}}
            <div class="py-4 grow overflow-y-auto px-4 space-y-1">
                <h3 class="px-4 py-4 text-[11px] text-gray-400 uppercase tracking-[0.2em] font-black">Eksplorasi</h3>

                <a href="{{ route('courses.index') }}"
                    class="group flex items-center gap-4 px-4 py-4 rounded-2xl hover:bg-jti-primary/5 active:bg-jti-primary/10 transition-all border border-transparent">
                    <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Semua Kursus</span>
                </a>

                <a href="{{ route('classes.index') }}"
                    class="group flex items-center gap-4 px-4 py-4 rounded-2xl hover:bg-jti-primary/5 active:bg-jti-primary/10 transition-all border border-transparent">
                    <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700">Kelas Pelatihan</span>
                </a>
            </div>
        </div>

        {{-- LAYER 2: Submenu Profil --}}
        @if (Auth::check() && Auth::user()->hasVerifiedEmail())
            <div class="absolute inset-0 bg-white flex flex-col z-50"
                x-show="subProfile"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full" style="display: none;">

                {{-- Back Button --}}
                <div @click="subProfile = false"
                    class="p-6 pt-10 flex items-center gap-4 border-b border-gray-50 bg-gray-50/80 cursor-pointer active:bg-gray-100 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-100 flex items-center justify-center text-jti-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest text-jti-primary">Kembali</span>
                </div>

                <div class="py-6 grow overflow-y-auto px-4">
                    {{-- User Identity Card --}}
                    <div class="px-5 py-6 bg-linear-to-br from-jti-primary to-jti-primary/80 rounded-4xl mb-6 shadow-xl shadow-jti-primary/20">
                        <p class="text-white text-lg font-bold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-white/60 text-xs truncate mt-1 font-medium">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="space-y-1">
                        @php
                            $userMenus = [
                                ['route' => 'student.my-courses', 'label' => 'Course Saya'],
                                ['route' => 'student.my-classes', 'label' => 'Kelas Saya'],
                                ['route' => 'student.cart', 'label' => 'Keranjang Saya', 'count' => true],
                                ['route' => 'student.orders', 'label' => 'Pesanan Saya', 'count' => true],
                                ['route' => 'student.transactions', 'label' => 'Riwayat Transaksi'],
                                ['route' => 'student.profile', 'label' => 'Pengaturan Profil'],
                            ];
                        @endphp

                        @foreach ($userMenus as $m)
                            <a href="{{ route($m['route']) }}"
                                class="flex items-center justify-between px-5 py-4 rounded-2xl hover:bg-gray-50 active:bg-gray-100 transition-all group">
                                <span class="text-sm font-bold text-gray-600 group-hover:text-jti-primary transition-colors">{{ $m['label'] }}</span>
                                <div class="flex items-center gap-3">
                                    @if (isset($m['count']) && $cartCount > 0)
                                        <span class="bg-jti-orange text-white text-[10px] px-2 py-0.5 rounded-full font-black shadow-sm">
                                            {{ $cartCount }}
                                        </span>
                                    @endif
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-jti-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Logout Section --}}
                <div class="p-6 mt-auto border-t border-gray-100">
                    <form method="POST" action="{{ $user->hasRole(['admin', 'super_admin', 'pengajar']) ? route('filament.admin.auth.logout') : route('filament.user.auth.logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-3 px-4 py-4 text-xs text-red-500 font-black uppercase tracking-widest rounded-2xl bg-red-50 hover:bg-red-100 transition-all">
                            Keluar
                            <x-lucide-log-out class="w-4 h-4" />
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

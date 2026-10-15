{{-- resources/views/components/navbar/mobile-menu.blade.php --}}
@props(['user', 'cartCount' => 0])

<div x-show="mobileMenu" class="fixed inset-0 z-[9999] md:hidden" style="display: none;">
    
    <div x-show="mobileMenu"
         x-transition:enter="transition-opacity duration-300 ease-out"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200 ease-in"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenu = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-xl"></div>

    <div x-show="mobileMenu"
         x-transition:enter="transition-transform duration-300 ease-out"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-250 ease-in"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="relative bg-white w-[85%] max-w-[340px] h-full shadow-2xl flex flex-col overflow-hidden rounded-r-3xl">

        {{-- Single Layer Menu --}}
        <div class="flex flex-col h-full w-full">

            @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                <div class="relative bg-gradient-to-br from-jti-primary via-jti-primary to-jti-primary/90 p-7 pt-12 pb-9 overflow-hidden shrink-0">
                    
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>

                    <div class="flex items-center gap-4 relative z-10">
                        <div class="relative shrink-0">
                            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white text-3xl font-bold shadow-inner">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="absolute bottom-0 right-0 w-5 h-5 bg-emerald-400 border-[2.5px] border-white rounded-full ring-2 ring-white/50"></div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-white/70 text-xs font-medium tracking-widest mb-0.5">Selamat datang kembali</p>
                            <h2 class="text-white text-xl font-semibold tracking-tight truncate">
                                {{ Auth::user()->name }}
                            </h2>
                            <p class="text-white/80 text-xs mt-1 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="px-8 py-10 border-b flex flex-col items-center gap-6 bg-gradient-to-b from-gray-50 to-white shrink-0">
                    <img src="{{ asset('img/logo-jti.png') }}" class="h-11 w-auto" alt="JTI Logo">
                    <div class="flex flex-col w-full gap-3">
                        <a href="{{ route('filament.user.auth.login') }}" 
                           class="w-full py-4 text-center font-semibold text-sm border-2 border-jti-primary text-jti-primary rounded-3xl active:scale-[0.97] transition-all">
                            Masuk
                        </a>
                        <a href="{{ route('filament.user.auth.register') }}" 
                           class="w-full py-4 text-center font-semibold text-sm bg-jti-primary text-white rounded-3xl shadow-xl shadow-jti-primary/30 active:scale-[0.97] transition-all">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>
            @endif

            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
                
                {{-- Navigasi Eksplorasi --}}
                <div class="space-y-1">
                    <h3 class="px-5 mb-3 text-[10px] font-bold text-gray-400 tracking-[0.125em] uppercase">Eksplorasi</h3>

                    <a href="{{ route('courses.index') }}" 
                       class="group flex items-center gap-4 px-5 py-[14px] rounded-3xl hover:bg-zinc-50 active:bg-zinc-100 transition-all">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center transition-transform group-active:scale-110 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-700">Semua Kursus</span>
                    </a>

                    <a href="{{ route('classes.index') }}" 
                       class="group flex items-center gap-4 px-5 py-[14px] rounded-3xl hover:bg-zinc-50 active:bg-zinc-100 transition-all">
                        <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center transition-transform group-active:scale-110 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-700">Kelas Pelatihan</span>
                    </a>
                </div>

                {{-- Navigasi Akun Saya --}}
                @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                    <div class="space-y-1 pt-4 border-t border-gray-100">
                        <h3 class="px-5 mb-3 text-[10px] font-bold text-gray-400 tracking-[0.125em] uppercase">Menu</h3>

                        @php
                            $userMenus = [
                                ['route' => 'student.my-courses', 'label' => 'Course Saya'],
                                ['route' => 'student.my-classes', 'label' => 'Kelas Saya'],
                                ['route' => 'student.cart', 'label' => 'Keranjang Saya', 'count' => true, 'navigate' => true],
                                ['route' => 'student.orders', 'label' => 'Pesanan Saya', 'navigate' => true],
                                ['route' => 'student.transactions', 'label' => 'Riwayat Transaksi', 'navigate' => true],
                                ['route' => 'student.profile', 'label' => 'Pengaturan Profil', 'navigate' => true],
                            ];
                        @endphp

                        @foreach ($userMenus as $m)
                            <a href="{{ route($m['route']) }}"
                               {{ !empty($m['navigate']) ? 'wire:navigate' : '' }}
                               class="group flex items-center justify-between px-5 py-[14px] rounded-3xl hover:bg-gray-50 active:bg-gray-100 transition-all">
                                <span class="font-semibold text-gray-700 group-hover:text-jti-primary transition-colors">
                                    {{ $m['label'] }}
                                </span>
                                
                                <div class="flex items-center gap-3">
                                    @if (isset($m['count']) && $cartCount > 0)
                                        <span class="bg-orange-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full shadow-sm">
                                            {{ $cartCount }}
                                        </span>
                                    @endif
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-300 group-hover:text-jti-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                <div class="p-4 border-t border-gray-100 shrink-0 bg-white">
                    <form method="POST" action="{{ $user->hasRole(['admin', 'super_admin', 'pengajar']) ? route('filament.admin.auth.logout') : route('filament.user.auth.logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-3 py-4 text-red-500 bg-red-50 hover:bg-red-100 active:bg-red-200 font-bold text-xs tracking-widest rounded-2xl transition-colors">
                            <x-lucide-log-out class="w-4 h-4" />
                            KELUAR
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>
{{-- resources/views/components/navbar/mobile-menu.blade.php --}}
@props(['user', 'cartCount' => 0])

<div x-show="mobileMenu" class="fixed inset-0 z-9999 md:hidden" style="display: none;">
    {{-- Overlay dengan efek blur yang lebih halus --}}
    <div x-show="mobileMenu" x-transition.opacity @click="mobileMenu = false; subProfile = false"
        class="fixed inset-0 bg-jti-primary/60 backdrop-blur-md"></div>

    {{-- Sidebar Container --}}
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="relative bg-white w-80 h-full shadow-2xl flex flex-col overflow-hidden">

        {{-- LAYER 1: Menu Utama --}}
        <div class="flex flex-col h-full transition-transform duration-300"
            :class="subProfile ? '-translate-x-full' : 'translate-x-0'">

            @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                {{-- Profile Header (Trigger Submenu) --}}
                <div @click="subProfile = true"
                    class="bg-jti-primary p-8 flex items-center justify-between cursor-pointer group relative overflow-hidden">
                    {{-- Dekorasi background --}}
                    <div
                        class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-32 h-32 bg-white/5 rounded-full">
                    </div>

                    <div class="flex items-center gap-4 relative z-10">
                        <div class="relative">
                            <div
                                class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-jti-primary text-2xl font-semibold shadow-lg">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div
                                class="absolute -top-1 -right-1 w-4 h-4 bg-jti-orange border-2 border-jti-primary rounded-full">
                            </div>
                        </div>
                        <div>
                            <h2 class="text-white text-lg font-semibold leading-tight tracking-tight">Hi,
                                {{ explode(' ', Auth::user()->name)[0] }}</h2>
                            <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest mt-0.5">Student
                                Account</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-white/40 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            @else
                <div class="p-8 border-b flex justify-between items-center bg-gray-50/50">
                    <img src="{{ asset('img/logo-jti.png') }}" class="h-8 w-auto">
                </div>
                <div class="p-8 flex flex-col gap-3 border-b border-gray-50">
                    <a href="{{ route('filament.admin.auth.login') }}"
                        class="text-center py-4 font-semibold text-xs uppercase tracking-widest border-2 border-jti-primary text-jti-primary rounded-2xl active:scale-95 transition-all">Log
                        in</a>
                    <a href="/register"
                        class="text-center py-4 font-semibold text-xs uppercase tracking-widest bg-jti-primary text-white rounded-2xl shadow-lg shadow-jti-primary/20 active:scale-95 transition-all">Sign
                        up</a>
                </div>
            @endif

            {{-- Navigasi Explore: Kelas & Course --}}
            <div class="py-6 grow overflow-y-auto px-4 space-y-2">
                <h3 class="px-4 py-2 text-[10px] text-gray-400 uppercase tracking-[0.2em] font-semibold">Mode Belajar
                </h3>

                {{-- Item Kursus --}}
                <a href="{{ route('courses.index') }}"
                    class="group flex items-center gap-4 px-4 py-4 rounded-2xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                    <div
                        class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-jti-primary uppercase tracking-tight">Semua Kursus</p>
                        <p class="text-[10px] text-gray-400 font-medium tracking-tight">Belajar mandiri via video</p>
                    </div>
                </a>

                {{-- Item Kelas --}}
                <a href="{{ route('classes.index') }}"
                    class="group flex items-center gap-4 px-4 py-4 rounded-2xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                    <div
                        class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-jti-primary uppercase tracking-tight">Kelas Pelatihan</p>
                        <p class="text-[10px] text-gray-400 font-medium tracking-tight">CATC & Pelindo Program</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- LAYER 2: Submenu Profil --}}
        @if (Auth::check() && Auth::user()->hasVerifiedEmail())
            <div class="absolute inset-0 bg-white flex flex-col transition-transform duration-300 z-50 rounded-r-[2rem]"
                x-show="subProfile" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full" style="display: none;">

                <div @click="subProfile = false"
                    class="p-8 flex items-center gap-4 border-b border-gray-50 bg-gray-50/50 cursor-pointer text-[10px] tracking-[0.2em] text-jti-primary font-semibold uppercase">
                    <div
                        class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-jti-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    Kembali
                </div>

                <div class="py-4 grow overflow-y-auto px-4">
                    <div class="px-4 py-6 border-b border-gray-50 mb-4 bg-jti-primary/5 rounded-[1.5rem]">
                        <p class="text-jti-primary truncate text-lg font-semibold tracking-tight">
                            {{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-gray-400 truncate mt-0.5 font-bold italic">{{ Auth::user()->email }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        @php
                            $userMenus = [
                                ['route' => 'student.my-courses', 'label' => 'Kursus Saya'],
                                ['route' => 'student.my-classes', 'label' => 'Kelas Saya'],
                                ['route' => 'student.cart', 'label' => 'Keranjang Belanja', 'count' => true],
                                ['route' => 'student.transactions', 'label' => 'Riwayat Transaksi'],
                                ['route' => 'student.profile', 'label' => 'Pengaturan Profil'],
                            ];
                        @endphp

                        @foreach ($userMenus as $m)
                            <a href="{{ route($m['route']) }}"
                                class="flex items-center justify-between px-5 py-4 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-2xl transition-all">
                                <span>{{ $m['label'] }}</span>
                                @if (isset($m['count']) && $cartCount > 0)
                                    <span
                                        class="bg-jti-orange text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-lg font-semibold shadow-sm">{{ $cartCount }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="p-6 mt-auto border-t border-gray-50 bg-gray-50/50">
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-3 px-4 py-4 text-xs text-red-500 font-semibold uppercase tracking-widest rounded-2xl hover:bg-red-50 transition-all border-2 border-transparent hover:border-red-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1h8M7 4H4v16h3" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

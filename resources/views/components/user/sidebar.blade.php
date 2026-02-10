{{-- resources/views/components/user/layout/sidebar.blade.php --}}
@props(['user'])

<div
    class="bg-white rounded-lg  border-2 border-gray-300 overflow-hidden transition-all duration-500">
    {{-- Profile Section Style Horizontal --}}
    <div
        class="relative p-6 border-b border-gray-50 bg-gradient-to-r from-gray-50/50 to-transparent group/profile cursor-pointer hover:bg-gray-50/80 transition-all duration-300">
        <div class="flex items-center gap-4">
            {{-- Avatar Section --}}
            <div class="relative shrink-0">
                <div
                    class="w-16 h-16 bg-jti-primary rounded-full flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-jti-primary/20 transform group-hover/profile:scale-105 transition-transform duration-500">
                    {{ substr($user->name, 0, 1) }}
                </div>
                {{-- Online Indicator / Badge --}}
                <div class="absolute -top-1 -right-1 w-5 h-5 bg-jti-orange border-4 border-white rounded-full shadow-sm">
                </div>
            </div>

            {{-- Text Section --}}
            <div class="flex-1 min-w-0">
                <h2 class="text-jti-primary font-black text-lg leading-tight truncate">Hi,
                    {{ explode(' ', $user->name)[0] }}</h2>
                <p class="text-gray-400 text-xs font-medium mt-0.5">Welcome back</p>
            </div>

            {{-- Arrow Icon --}}
            <div
                class="text-gray-300 group-hover/profile:text-jti-primary group-hover/profile:translate-x-1 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="p-4 space-y-2">
        @php
            $menus = [
                [
                    'route' => 'user.index',
                    'label' => 'Dashboard Saya',
                    'icon' =>
                        'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                ],
                [
                    'route' => 'user.enrolled',
                    'label' => 'Kursus Saya',
                    'icon' =>
                        'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                ],
                // MENU KERANJANG BARU
                [
                    'route' => 'user.cart',
                    'label' => 'Keranjang Saya',
                    'icon' =>
                        'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                    'count' => true,
                ],
                [
                    'route' => 'user.transactions',
                    'label' => 'Riwayat Transaksi',
                    'icon' =>
                        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                ],
                [
                    'route' => 'user.profile',
                    'label' => 'Edit Profil',
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                ],
            ];
        @endphp

        @foreach ($menus as $menu)
            @php $isActive = request()->routeIs($menu['route']); @endphp
            <a href="{{ route($menu['route']) }}"
                class="group flex items-center justify-between px-5 py-4 rounded-[1.25rem] text-sm font-bold transition-all duration-300 {{ $isActive ? 'bg-jti-primary text-white shadow-xl shadow-jti-primary/20 scale-[1.02]' : 'text-gray-400 hover:bg-gray-50 hover:text-jti-primary' }}">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <svg class="w-5 h-5 transition-all duration-300 {{ $isActive ? 'scale-110' : 'group-hover:scale-110 group-hover:text-jti-orange' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $menu['icon'] }}" />
                        </svg>
                        {{-- Badge angka jika menu Keranjang --}}
                        @if (isset($menu['count']) && $cartCount > 0)
                            <span class="absolute -top-2 -right-2 flex h-4 w-4">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-jti-orange opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-4 w-4 bg-jti-orange text-[9px] text-white items-center justify-center font-black">{{ $cartCount }}</span>
                            </span>
                        @endif
                    </div>
                    <span class="tracking-tight">{{ $menu['label'] }}</span>
                </div>

                @if ($isActive)
                    <div class="w-1.5 h-1.5 bg-jti-orange rounded-full shadow-[0_0_10px_#f25c32]"></div>
                @endif
            </a>
        @endforeach

        <div class="pt-6 mt-4 border-t border-gray-50">
            <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                @csrf
                <button type="submit"
                    class="w-full group flex items-center gap-4 px-5 py-4 rounded-[1.25rem] text-sm font-bold text-rose-500 hover:bg-rose-50 transition-all duration-300">
                    <div
                        class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center group-hover:bg-white transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1h8M7 4H4v16h3" />
                        </svg>
                    </div>
                    <span class="tracking-tight">Keluar Aplikasi</span>
                </button>
            </form>
        </div>
    </nav>
</div>

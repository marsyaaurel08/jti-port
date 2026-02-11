{{-- resources/views/components/user/layout/sidebar.blade.php --}}
@props(['user', 'cartCount' => 0])

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    {{-- Profile Section --}}
    <div class="relative p-6 border-b border-slate-50 bg-gradient-to-br from-slate-50 to-white group/profile cursor-pointer hover:bg-slate-50 transition-all duration-300">
        <div class="flex items-center gap-4">
            {{-- Avatar Section --}}
            <div class="relative shrink-0">
                <div class="w-14 h-14 bg-jti-primary rounded-full flex items-center justify-center text-white text-xl font-extrabold shadow-md shadow-jti-primary/20 transform group-hover/profile:scale-105 transition-transform duration-500">
                    {{ substr($user->name, 0, 1) }}
                </div>
                {{-- Online Indicator --}}
                <div class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full shadow-sm"></div>
            </div>

            {{-- Text Section --}}
            <div class="flex-1 min-w-0">
                <h2 class="text-slate-900 font-extrabold text-base leading-tight truncate tracking-tight">
                    Hi, {{ explode(' ', $user->name)[0] }}
                </h2>
                <p class="text-slate-400 text-[13px] font-medium mt-0.5">Welcome back</p>
            </div>

            {{-- Arrow Icon --}}
            <div class="text-slate-300 group-hover/profile:text-jti-primary group-hover/profile:translate-x-1 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="p-4 space-y-1.5">
        @php
            $menus = [
                [
                    'route' => 'student.cart',
                    'label' => 'Keranjang Saya',
                    'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                    'count' => true,
                ],
                [
                    'route' => 'student.transactions',
                    'label' => 'Riwayat Transaksi',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                ],
                [
                    'route' => 'student.profile',
                    'label' => 'Edit Profil',
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                ],
            ];
        @endphp

        @foreach ($menus as $menu)
            @php $isActive = request()->routeIs($menu['route']); @endphp
            <a href="{{ route($menu['route']) }}"
                class="group flex items-center justify-between px-4 py-3.5 rounded-xl text-[14px] transition-all duration-200
                {{ $isActive
                    ? 'bg-jti-primary text-white shadow-lg shadow-jti-primary/25 font-semibold'
                    : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 font-semibold' }}">

                <div class="flex items-center gap-3.5">
                    <div class="relative">
                        <svg class="w-5 h-5 transition-transform duration-300 {{ $isActive ? 'scale-110' : 'group-hover:scale-110 group-hover:text-jti-orange' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}" />
                        </svg>

                        @if (isset($menu['count']) && $cartCount > 0)
                            <span class="absolute -top-2 -right-2 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-jti-orange opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-jti-orange text-[9px] text-white items-center justify-center font-black border border-white">
                                    {{ $cartCount }}
                                </span>
                            </span>
                        @endif
                    </div>
                    <span class="tracking-tight">{{ $menu['label'] }}</span>
                </div>

                @if ($isActive)
                    <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full shadow-[0_0_8px_rgba(250,204,21,0.6)]"></div>
                @endif
            </a>
        @endforeach

        {{-- Logout Section --}}
        <div class="pt-4 mt-4 border-t border-slate-100">
            <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                @csrf
                <button type="submit"
                    class="w-full group flex items-center gap-3.5 px-4 py-3 rounded-xl text-[14px] font-semibold text-rose-500 hover:bg-rose-50 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1h8M7 4H4v16h3" />
                        </svg>
                    </div>
                    <span class="tracking-tight">Keluar Aplikasi</span>
                </button>
            </form>
        </div>
    </nav>
</div>

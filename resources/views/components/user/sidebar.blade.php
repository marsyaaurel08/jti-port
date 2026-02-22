{{-- resources/views/components/user/layout/sidebar.blade.php --}}
@props(['user', 'cartCount' => 0])

<div class="bg-white min-h-[400px] flex flex-col">
    {{-- Header: Profil Ringkas --}}
    <div class="px-6 py-8">
        <div class="flex items-center gap-4">
            <div class="relative">
                {{-- Avatar dengan inisial yang bersih --}}
                <div class="w-11 h-11 bg-slate-100 text-slate-700 flex items-center justify-center rounded-full text-sm font-bold border border-slate-200">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-slate-900 font-bold text-sm tracking-tight truncate">
                    {{ $user->name }}
                </span>
                <span class="text-slate-400 text-[11px] font-medium tracking-wide uppercase">
                    Mahasiswa
                </span>
            </div>
        </div>
    </div>

    {{-- Menu Utama --}}
    <nav class="flex-1 px-4 space-y-0.5">
        @php
            $menus = [
                [
                    'route' => 'student.cart',
                    'label' => 'Keranjang Saya',
                    'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                    'count' => $cartCount
                ],
                [
                    'route' => 'student.transactions',
                    'label' => 'Transaksi',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                ],
                [
                    'route' => 'student.profile',
                    'label' => 'Profil',
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                ],
            ];
        @endphp

        @foreach ($menus as $menu)
            @php $isActive = request()->routeIs($menu['route']); @endphp
            <a href="{{ route($menu['route']) }}"
               class="group flex items-center justify-between px-4 py-2.5 rounded-lg transition-all duration-200
               {{ $isActive
                  ? 'bg-slate-50 text-jti-primary'
                  : 'text-slate-500 hover:bg-slate-50/80 hover:text-slate-900' }}">

                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 transition-colors {{ $isActive ? 'text-jti-primary' : 'text-slate-400 group-hover:text-slate-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu['icon'] }}" />
                    </svg>
                    <span class="text-[13.5px] font-medium tracking-tight">{{ $menu['label'] }}</span>
                </div>

                @if(isset($menu['count']) && $menu['count'] > 0)
                    <span class="bg-jti-orange/10 text-jti-orange text-[10px] font-bold px-2 py-0.5 rounded-full">
                        {{ $menu['count'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Footer: Logout yang Menutup --}}
    <div class="p-4 mt-auto border-t border-slate-50">
        <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-slate-400 hover:text-rose-500 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                <span class="text-[13.5px] font-medium">Log out</span>
            </button>
        </form>
    </div>
</div>

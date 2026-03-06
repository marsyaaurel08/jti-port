{{-- resources/views/components/user/layout/sidebar.blade.php --}}
@props(['user', 'cartCount' => 0])

<div class="bg-white min-h-[400px] flex flex-col border border-slate-200 rounded-xl shadow-sm">
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
                    'icon' => 'shopping-cart',
                    'count' => $cartCount
                ],
                [
                    'route' => 'student.transactions',
                    'label' => 'Transaksi',
                    'icon' => 'receipt',           // alternatif: 'receipt-text' kalau mau lebih detail
                ],
                [
                    'route' => 'student.profile',
                    'label' => 'Profil',
                    'icon' => 'user',
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
                    <i data-lucide="{{ $menu['icon'] }}"
                       class="w-5 h-5 transition-colors {{ $isActive ? 'text-jti-primary' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
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
        <form method="POST" action="{{ route('filament.user.auth.logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-slate-400 hover:text-rose-500 rounded-lg transition-colors duration-200">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span class="text-[13.5px] font-medium">Log out</span>
            </button>
        </form>
    </div>
</div>

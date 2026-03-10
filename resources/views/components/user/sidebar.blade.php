{{-- resources/views/components/user/layout/sidebar.blade.php --}}
@props(['user', 'cartCount' => 0])

<aside class="w-full sticky top-6 flex flex-col gap-4 group">
    {{-- Card Utama --}}
    <div
        class="bg-white rounded-[2rem] shadow-[0_10px_40px_rgba(20,33,61,0.03)] border border-slate-100 overflow-hidden flex flex-col">

        {{-- Header: Profil Ringkas --}}
        <div class="px-6 py-10 flex flex-col items-center">
            <div class="relative mb-4">
                {{-- Lingkaran Aksen Kuning --}}
                <div class="absolute inset-0 bg-jti-yellow/20 rounded-full scale-125 blur-xl"></div>

                {{-- Avatar --}}
                <div
                    class="relative w-16 h-16 bg-white flex items-center justify-center rounded-2xl border border-slate-100 shadow-sm text-jti-primary text-xl font-semibold">
                    {{ substr($user->name, 0, 1) }}
                </div>

                {{-- Status Online Dot --}}
                <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full">
                </div>
            </div>

            <div class="text-center">
                <p class="text-slate-900 font-semibold text-base tracking-tight leading-tight">
                    {{ $user->name }}
                </p>
                <p class="text-slate-400 text-xs mt-1 font-medium">
                    Mahasiswa Terdaftar
                </p>
            </div>
        </div>

        {{-- Navigasi --}}
        <nav class="flex-1 px-4 pb-6 space-y-1">
            @php
                $menus = [
                    [
                        'route' => 'student.cart',
                        'label' => 'Keranjang saya',
                        'icon' => 'shopping-bag',
                        'count' => $cartCount,
                    ],
                    [
                        'route' => 'student.orders',
                        'label' => 'Pesanan saya',
                        'icon' => 'box',
                    ],
                    [
                        'route' => 'student.transactions',
                        'label' => 'Riwayat transaksi',
                        'icon' => 'receipt',
                    ],
                    [
                        'route' => 'student.profile',
                        'label' => 'Pengaturan profil',
                        'icon' => 'user-circle',
                    ],
                ];
            @endphp

            @foreach ($menus as $menu)
                @php $isActive = request()->routeIs($menu['route']); @endphp

                <a href="{{ route($menu['route']) }}"
                    class="group/item flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300
                   {{ $isActive ? 'bg-jti-yellow/10 text-jti-primary' : 'text-slate-500 hover:bg-slate-50 hover:text-jti-primary' }}">

                    <div class="flex items-center gap-3">
                        {{-- Icon dengan aksen kuning saat aktif --}}
                        <div class="flex items-center justify-center transition-colors">
                            <i data-lucide="{{ $menu['icon'] }}"
                                class="w-5 h-5 {{ $isActive ? 'text-jti-yellow fill-jti-yellow/20' : 'text-slate-400 group-hover/item:text-jti-primary' }}"></i>
                        </div>
                        <span class="text-[13.5px] font-medium tracking-tight">{{ $menu['label'] }}</span>
                    </div>

                    @if (isset($menu['count']) && $menu['count'] > 0)
                        <span
                            class="{{ $isActive ? 'bg-jti-yellow' : 'bg-jti-yellow/20' }} text-jti-primary text-[10px] font-bold px-2 py-0.5 rounded-lg transition-colors">
                            {{ $menu['count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Tombol Logout (Floating Terpisah) --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-1.5 shadow-sm">
        <form method="POST" action="{{ route('filament.user.auth.logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-2.5 text-slate-400 hover:text-jti-maroon hover:bg-rose-50/50 rounded-xl transition-all duration-200">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span class="text-[13px] font-medium">Keluar</span>
            </button>
        </form>
    </div>
</aside>

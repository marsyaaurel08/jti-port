@guest
    <div class="hidden md:flex items-center gap-3">
        <a href="{{ route('filament.admin.auth.login') }}"
            class="px-6 py-2.5 text-sm font-semibold border border-jti-yellow text-jti-yellow hover:bg-jti-yellow hover:text-gray-800 transition-all rounded-lg">Log
            in</a>
        <a href="/register"
            class="px-6 py-2.5 text-sm font-semibold bg-jti-yellow text-white hover:bg-jti-maroon transition-all shadow-lg rounded-lg">Sign
            up</a>
    </div>
@endguest
@auth
    {{-- Profile Dropdown Desktop --}}
    <div class="relative hidden md:block" x-data="{ openProfile: false }" @mouseenter="openProfile = true"
        @mouseleave="openProfile = false">
        <button class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 transition-all">
            <div
                class="w-10 h-10 rounded-full bg-jti-primary flex items-center justify-center text-white shadow-sm ring-2 ring-transparent hover:ring-jti-orange/20 transition-all">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </button>
        {{-- Isi Dropdown (Sesuai kode Anda sebelumnya) --}}
        <div x-show="openProfile" x-transition
            class="absolute right-0 mt-2 w-72 bg-white border border-gray-100 shadow-2xl z-50 rounded-xl overflow-hidden">
            <a href="{{ route('user.index') }}"
                class="px-5 py-4 flex items-center gap-3 border-b border-gray-50 hover:bg-gray-50/50 group transition-colors">
                <div
                    class="w-12 h-12 rounded-full bg-jti-primary flex items-center justify-center text-white text-xl group-hover:bg-jti-orange transition-colors">
                    {{ substr(Auth::user()->name, 0, 1) }}</div>
                <div class="overflow-hidden">
                    <p class="text-jti-primary leading-tight truncate group-hover:text-jti-orange transition-colors">
                        {{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </a>
            <div class="py-2">
                <a href="{{ route('user.index') }}"
                    class="block px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30 hover:text-jti-orange transition-colors">Dashboard
                    Saya</a>
                <a href="{{ route('user.enrolled') }}"
                    class="block px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30 hover:text-jti-orange transition-colors">Kursus
                    Saya</a>
                <a href="/cart"
                    class="flex items-center justify-between px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30 hover:text-jti-orange transition-colors">
                    <span>Keranjang Belanja</span>
                    <span
                        class="bg-jti-orange text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full">9</span>
                </a>
                <a href="{{ route('user.transactions') }}"
                    class="block px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30 hover:text-jti-orange transition-colors">Riwayat
                    Transaksi</a>
                <a href="{{ route('user.profile') }}"
                    class="block px-6 py-3 text-sm font-semibold text-gray-600 hover:bg-jti-light/30 hover:text-jti-orange transition-colors">Edit
                    Profil</a>
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                    @csrf
                    <button type="submit" class="block px-6 py-3 text-sm font-semibold text-red-500 hover:bg-jti-light/30 hover:text-red-600 transition-colors w-full text-left cursor-pointer">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
@endauth

@guest
    <div class="hidden md:flex items-center gap-3">
        {{-- Login diarahkan ke login Filament --}}
        <a href="{{ route('filament.admin.auth.login') }}"
            class="px-6 py-2.5 text-sm font-semibold border border-jti-yellow text-jti-yellow hover:bg-jti-yellow hover:text-gray-800 transition-all rounded-lg">Log
            in</a>

        {{-- Register (Jika Filament tidak menyediakan, tetap ke rute custom) --}}
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

        {{-- Isi Dropdown --}}
        <div x-show="openProfile" x-transition
            class="absolute right-0 mt-2 w-72 bg-white border border-gray-100 shadow-2xl z-50 rounded-xl overflow-hidden">

            {{-- Link Profil diarahkan ke Dashboard Filament --}}
            <a href="{{ url('/staff') }}"
                class="px-5 py-4 flex items-center gap-3 border-b border-gray-50 hover:bg-gray-50/50 group transition-colors">
                <div
                    class="w-12 h-12 rounded-full bg-jti-primary flex items-center justify-center text-white text-xl group-hover:bg-jti-orange transition-colors">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-jti-primary leading-tight truncate group-hover:text-jti-orange transition-colors">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[11px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </a>

            <div class="py-2">
                {{-- Link ke Dashboard utama Filament --}}
                <a href="{{ url('/staff') }}"
                    class="block px-6 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    Dashboard Admin
                </a>

                {{-- Logout menggunakan rute Filament --}}
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                    @csrf
                    <button type="submit"
                        class="block px-6 py-3 text-sm font-semibold text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors w-full text-left cursor-pointer">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
@endauth

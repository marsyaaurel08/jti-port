@if (Auth::check() && Auth::user()->hasVerifiedEmail())
    <div class="relative hidden md:block" x-data="{ openProfile: false }" @mouseenter="openProfile = true"
        @mouseleave="openProfile = false">
        <button class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 transition-all">
            <div
                class="w-10 h-10 rounded-full bg-jti-primary flex items-center justify-center text-white font-black shadow-sm ring-2 ring-transparent hover:ring-jti-orange/20 transition-all">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </button>

        <div x-show="openProfile" x-transition
            class="absolute right-0 mt-2 w-72 bg-white border border-gray-100 shadow-2xl z-50 rounded-xl overflow-hidden text-gray-700">
            {{-- Header Info --}}
            <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-full bg-jti-primary flex items-center justify-center text-white text-xl font-black">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-jti-primary font-bold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            {{-- Links --}}
            <div class="py-2">
                <a href="{{ route('student.enrolled') }}"
                    class="block px-6 py-2 text-sm font-semibold hover:bg-jti-light/30">Kursus Saya</a>
                <hr class="my-1 border-gray-50">
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-6 py-2 text-sm font-bold text-red-500 hover:bg-red-50">Logout</button>
                </form>
            </div>
        </div>
    </div>
@else
    <div class="hidden md:flex items-center gap-3">
        <a href="{{ route('filament.admin.auth.login') }}" class="text-sm font-semibold text-jti-yellow">Log in</a>
        <a href="/register" class="px-6 py-2.5 text-sm font-semibold bg-jti-yellow text-white rounded-lg">Sign up</a>
    </div>
@endif

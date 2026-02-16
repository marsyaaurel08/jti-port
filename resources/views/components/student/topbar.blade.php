<header class="border-b bg-white">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

        {{-- Left --}}
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2 font-bold">
                🎓 <span>JTI PORT</span>
            </div>

            <nav class="flex gap-6 text-sm">
                <a href="{{ url('/dashboard') }}" class="text-gray-400 hover:text-black">Dashboard</a>
                <a href="{{ route('student.my-courses') }}" class="text-yellow-500 font-semibold">My Courses</a>
            </nav>
        </div>

        {{-- Right --}}
        <div class="flex items-center gap-4">
            <input type="text" placeholder="Search Courses..."
                class="border rounded-md px-3 py-1 text-sm focus:outline-none">

            @if (Auth::check() && Auth::user()->hasVerifiedEmail())
                <div class="flex items-center gap-2 text-sm">
                    @if (auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover"
                            alt="{{ auth()->user()->name }}">
                    @else
                        <div
                            class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="leading-tight">
                        <p class="font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ ucfirst(auth()->user()->roles->first()?->name ?? 'Student') }}</p>
                    </div>
                </div>
            @endif
        </div>

    </div>
</header>

<x-student.layout.index :user="$user">
    <div class="px-6 md:px-48">

        {{-- Header: Perbaikan kontras dan spacing --}}
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Kelas Saya</h1>
            <p class="mt-2 text-base text-slate-500 max-w-2xl">
                Kelola dan pantau perkembangan sertifikasi Anda di JTI PORT.
            </p>
        </div>

        {{-- Tabs: Perbaikan font-weight dan interaksi --}}
        <div class="flex flex-wrap gap-3 mb-10">
            @php
                $activeFilter = request('filter', 'all');
                $tabs = [
                    'all' => 'Semua Kelas',
                    'in-progress' => 'Sedang Berlangsung',
                    'completed' => 'Selesai'
                ];
            @endphp

            @foreach($tabs as $key => $label)
                <a href="{{ route('student.my-classes', ['filter' => $key]) }}"
                    class="px-5 py-2.5 rounded-full text-sm transition-all duration-200
                    {{ $activeFilter === $key
                        ? 'bg-yellow-400 text-yellow-950 font-bold shadow-sm ring-1 ring-yellow-500/20'
                        : 'bg-white border border-slate-200 text-slate-600 font-medium hover:border-slate-300 hover:bg-slate-50'
                    }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Grid: Spacing antar card diperluas sedikit --}}
        @if ($classes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach ($classes as $class)
                {{-- @dd($class) --}}
                    <x-student.class-card
                        :image="$class['thumbnail_url']"
                        :title="$class['title']"
                        :description="$class['title']"
                        :progress="$class['progress']"
                        :status="$class['status']"
                        :slug="$class['slug'] ?? ''"
                        :first_lesson_slug="$class['first_lesson_slug'] ?? null"
                        :last_accessed_lesson_slug="$class['last_accessed_lesson_slug'] ?? null"
                    />
                @endforeach
            </div>
        @else
            {{-- Empty State: Dibuat lebih emosional dengan tipografi --}}
            <div class="flex flex-col items-center justify-center py-20 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                <p class="text-xl font-semibold text-slate-700">No classes found</p>
                <p class="text-slate-500 mt-2 text-center max-w-sm px-4">
                    It looks like you haven't started any classes yet. Explore our catalog to begin your journey!
                </p>
                <a href="{{ route('classes.index') }}" class="mt-6 text-sm font-bold text-yellow-600 hover:text-yellow-700 underline underline-offset-4">
                    Browse Classes
                </a>
            </div>
        @endif

    </div>
</x-student.layout.index>

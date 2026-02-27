<x-student.layout.index :user="$user">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Header: Perbaikan kontras dan spacing --}}
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">My Courses</h1>
            <p class="mt-2 text-base text-slate-500 max-w-2xl">
                Manage and track your certification progress at JTI PORT.
            </p>
        </div>

        {{-- Tabs: Perbaikan font-weight dan interaksi --}}
        <div class="flex flex-wrap gap-3 mb-10">
            @php
                $activeFilter = request('filter', 'all');
                $tabs = [
                    'all' => 'All Courses',
                    'in-progress' => 'In Progress',
                    'completed' => 'Completed'
                ];
            @endphp

            @foreach($tabs as $key => $label)
                <a href="{{ route('student.my-courses', ['filter' => $key]) }}"
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
        @if ($courses->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($courses as $course)
                    <x-student.course-card
                        :image="$course['image']"
                        :title="$course['title']"
                        :description="$course['description']"
                        :progress="$course['progress']"
                        :status="$course['status']"
                        :slug="$course['slug'] ?? ''"
                        :first_lesson_slug="$course['first_lesson_slug'] ?? null"
                        :last_accessed_lesson_slug="$course['last_accessed_lesson_slug'] ?? null"
                    />
                @endforeach
            </div>
        @else
            {{-- Empty State: Dibuat lebih emosional dengan tipografi --}}
            <div class="flex flex-col items-center justify-center py-20 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                <p class="text-xl font-semibold text-slate-700">No courses found</p>
                <p class="text-slate-500 mt-2 text-center max-w-sm px-4">
                    It looks like you haven't started any certifications yet. Explore our catalog to begin your journey!
                </p>
                <a href="{{ route('courses.index') }}" class="mt-6 text-sm font-bold text-yellow-600 hover:text-yellow-700 underline underline-offset-4">
                    Browse Courses
                </a>
            </div>
        @endif

    </div>
</x-student.layout.index>

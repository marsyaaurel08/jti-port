@props(['courses' => null])

{{-- Course Results List with Pagination --}}
<div class="grow">
    <div class="space-y-4">
        @forelse($courses as $course)
            <x-course.list-card-horizontal :course="$course" />
        @empty
            <x-course.empty-state />
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($courses->count() > 0)
        <div class="mt-16 flex justify-center">
            <div class="inline-flex items-center p-1 bg-white border border-gray-100 rounded-2xl shadow-sm">
                {{ $courses->links('components.pagination.custom-tailwind') }}
            </div>
        </div>
    @endif
</div>

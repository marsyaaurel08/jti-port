@props(['classes' => collect()])

{{-- Classes Results List with Pagination --}}
<div class="grow space-y-2">
    @forelse($classes as $class)
        <x-class.class-card-horizontal :class="$class" />
    @empty
        <x-classes.empty-state />
    @endforelse

    {{-- Pagination --}}
    @if ($classes->count() > 0)
        <div class="mt-16 flex justify-center">
            <div class="inline-flex items-center p-1 bg-white border border-gray-100 rounded-2xl shadow-sm">
                {{ $classes->links('components.pagination.custom-tailwind') }}
            </div>
        </div>
    @endif
</div>

@props(['classes' => collect()])

{{-- Classes Results List with Pagination --}}
<div class="grow space-y-5">
    @forelse($classes as $class)
        <x-class.class-card-horizontal :class="$class" />
    @empty
        <x-classes.empty-state />
    @endforelse

    {{-- Pagination --}}
    @if ($classes->count() > 0)
        <div class="mt-12 flex justify-center">
            {{ $classes->links('components.pagination.custom-tailwind') }}
        </div>
    @endif
</div>

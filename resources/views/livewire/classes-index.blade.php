<div class="max-w-[1440px] mx-auto px-10 lg:px-72 py-12" wire:key="classes-index-root" x-data="{ showMobileFilter: false }">
    {{-- Header Section --}}
    <x-classes.header :totalClasses="$classes->total()" :hasActiveFilters="!empty($selectedCategories) || !empty($selectedLevels)" :currentSort="$sort" />

    <div class="flex flex-col md:flex-row gap-10">
        {{-- Sidebar Filter --}}
        <x-classes.filters :categories="$categories" :selectedCategories="$selectedCategories" :selectedLevels="$selectedLevels" />

        {{-- Main Content: List Hasil --}}
        <div class="grow space-y-5">
            @forelse($classes as $class)
                <x-class.class-card-horizontal :class="$class" />
            @empty
                <x-classes.empty-state />
            @endforelse

            {{-- Pagination --}}
            <x-classes.pagination :paginator="$classes" />
        </div>
    </div>
</div>

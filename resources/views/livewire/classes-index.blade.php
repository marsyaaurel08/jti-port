<div class="max-w-[1440px] mx-auto px-10 lg:px-72 py-12" wire:key="classes-index-root">
    {{-- Header Section --}}
    <x-classes.header-section :total="$classes->total()" :selectedCategories="$selectedCategories" :selectedLevels="$selectedLevels" :isFree="$isFree" :isPaid="$isPaid"
        :sort="$sort" />

    <div class="flex flex-col md:flex-row gap-10">
        {{-- Sidebar Filter --}}
        <x-classes.filters :categories="$categories" :selectedCategories="$selectedCategories" :selectedLevels="$selectedLevels" :isFree="$isFree" :isPaid="$isPaid" />

        {{-- Results List --}}
        <x-classes.results-list :classes="$classes" />
    </div>
</div>

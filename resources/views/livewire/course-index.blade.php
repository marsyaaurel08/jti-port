<div class="max-w-[1440px] mx-auto px-10 lg:px-72 py-12">
    {{-- Header Section --}}
    <x-course.header-section :total="$courses->total()" :selectedRatings="$selectedRatings" :selectedLevels="$selectedLevels" :isFree="$isFree" :isPaid="$isPaid"
        :selectedCategories="$selectedCategories" :sort="$sort" />

    <div class="flex flex-col md:flex-row gap-10">
        {{-- Sidebar Filter --}}
        <x-course.filters :categories="$categories" :selectedCategories="$selectedCategories" :selectedRatings="$selectedRatings" :selectedLevels="$selectedLevels" :isFree="$isFree"
            :isPaid="$isPaid" />

        {{-- Results List --}}
        <x-course.results-list :courses="$courses" />
    </div>
</div>

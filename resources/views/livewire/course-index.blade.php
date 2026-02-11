<div class="max-w-[1440px] mx-auto px-10 lg:px-72 py-12" x-data="{ showMobileFilter: false }">
    {{-- Header: Title, Info & Sorting --}}
    <x-course.header :totalCourses="$courses->total()" :hasActiveFilters="!empty($selectedRatings) || !empty($selectedLevels) || $isFree || $isPaid || !empty($selectedCategories)" :currentSort="$sort" />

    <div class="flex flex-col md:flex-row gap-10">
        {{-- Sidebar Filter --}}
        <x-course.filters :categories="$categories" :selectedCategories="$selectedCategories" :selectedRatings="$selectedRatings" :selectedLevels="$selectedLevels" :isFree="$isFree"
            :isPaid="$isPaid" />

        {{-- Main Content: List Hasil --}}
        <div class="grow">
            <div class="space-y-5">
                @forelse($courses as $course)
                    <x-course.list-card-horizontal :course="$course" />
                @empty
                    <x-course.empty-state />
                @endforelse
            </div>

            {{-- Pagination --}}
            <x-course.pagination :paginator="$courses" />
        </div>
    </div>
</div>

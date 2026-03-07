{{-- Container Utama dengan Background subtle agar card putih lebih menonjol --}}
<div class="min-h-screen bg-[#F8FAFC]">
    <div class="max-w-[1440px] mx-auto px-10 lg:px-72 py-8">

        {{-- Header Section: Diberi margin bottom yang lebih besar agar layout bernapas --}}
        <div class="mb-4">
            <x-course.header-section :total="$courses->total()" :selectedRatings="$selectedRatings" :selectedLevels="$selectedLevels" :isFree="$isFree"
                :isPaid="$isPaid" :selectedCategories="$selectedCategories" :sort="$sort" />
        </div>

        <div class="flex flex-col lg:flex-row gap-12 items-start">

            {{-- Sidebar Filter: Sticky agar tetap terlihat saat scroll --}}
            <aside class="w-full lg:w-[280px] shrink-0 lg:sticky lg:top-26">
                <x-course.filters :categories="$categories" :selectedCategories="$selectedCategories" :selectedRatings="$selectedRatings" :selectedLevels="$selectedLevels"
                    :isFree="$isFree" :isPaid="$isPaid" />

                {{-- Info Tambahan di bawah filter (Khas Academix) --}}
                <div
                    class="mt-8 p-6 bg-jti-primary rounded-[2rem] text-white overflow-hidden relative group cursor-pointer transition-all hover:shadow-xl hover:shadow-jti-primary/10 active:scale-[0.98]">
                    {{-- Konten Utama --}}
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <p class="text-[11px] font-semibold text-jti-yellow/90 opacity-90 capitalize">butuh bantuan?</p>
                        </div>
                        <p class="text-sm font-medium leading-relaxed pr-8">
                            Konsultasikan jalur sertifikasi yang tepat untuk karier anda bersama tim ahli kami.
                        </p>
                    </div>

                    {{-- Dekorasi Abstract & Ikon --}}
                    <div
                        class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full group-hover:scale-150 transition-transform duration-700">
                    </div>
                    <div
                        class="absolute right-4 top-1/2 -translate-y-1/2 opacity-20 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-500">
                        <i data-lucide="arrow-right-circle" class="w-6 h-6 text-jti-yellow"></i>
                    </div>
                </div>
            </aside>

            {{-- Results List: Mengambil sisa space yang ada --}}
            <main class="grow min-w-0">
                <x-course.results-list :courses="$courses" />
            </main>

        </div>
    </div>
</div>

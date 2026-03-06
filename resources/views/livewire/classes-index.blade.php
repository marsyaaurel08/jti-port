{{-- Container Utama dengan Background subtle agar card putih lebih menonjol --}}
<div class="min-h-screen bg-[#F8FAFC]">
    <div class="max-w-[1440px] mx-auto px-10 lg:px-72 py-8">

        {{-- Header Section: Diberi margin bottom yang lebih besar agar layout bernapas --}}
        <div class="mb-4">
            <x-classes.header-section :total="$classes->total()" :selectedLevels="$selectedLevels" :isFree="$isFree" :isPaid="$isPaid"
                :selectedCategories="$selectedCategories" :sort="$sort" />
        </div>

        <div class="flex flex-col lg:flex-row gap-12 items-start">

            {{-- Sidebar Filter: Sticky agar tetap terlihat saat scroll --}}
            <aside class="w-full lg:w-[280px] shrink-0 lg:sticky lg:top-26">
                <x-classes.filters :categories="$categories" :selectedCategories="$selectedCategories" :selectedLevels="$selectedLevels" :isFree="$isFree"
                    :isPaid="$isPaid" />

                {{-- Info Tambahan di bawah filter (Khas JTI) --}}
                <div class="mt-8 p-6 bg-jti-primary rounded-3xl text-white overflow-hidden relative group">
                    <div class="relative z-10">
                        <p class="text-xs font-bold opacity-80 uppercase tracking-widest mb-1">Butuh Bantuan?</p>
                        <p class="text-sm font-medium leading-relaxed">Konsultasikan jalur pembelajaran yang tepat untuk
                            karier Anda.</p>
                    </div>
                    {{-- Dekorasi Abstract --}}
                    <div
                        class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-700">
                    </div>
                </div>
            </aside>

            {{-- Results List: Mengambil sisa space yang ada --}}
            <main class="grow min-w-0">
                <x-classes.results-list :classes="$classes" />
            </main>

        </div>
    </div>
</div>

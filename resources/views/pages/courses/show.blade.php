<x-layouts.app>
    <x-course.details-header :course="$course" />
    {{-- Header / Hero Section ala Udemy --}}

    <div class="max-w-[1440px] mx-auto px-4 md:px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">

            {{-- Kolom Kiri: Konten Utama --}}
            <div class="lg:col-span-3 space-y-12">
                {{-- Deskripsi --}}
                <section>
                    <h2 class="text-2xl font-bold text-jti-primary mb-4">Deskripsi</h2>
                    <div class="prose max-w-none text-gray-600">
                        {!! $course->description !!}
                    </div>
                </section>

                {{-- Kurikulum (Accordion) --}}
                <section>
                    <h2 class="text-2xl font-bold text-jti-primary mb-6">Kurikulum Kursus</h2>
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        @foreach ($course->chapters as $chapter)
                            <x-course.chapter-accordion :chapter="$chapter" />
                        @endforeach
                    </div>
                </section>
            </div>

            {{-- Kolom Kanan: Sidebar Floating Card (Desktop Only) --}}
            <div class="relative hidden lg:block">
                <div class="sticky top-24">
                    <x-course.purchase-card :course="$course" />
                </div>
            </div>

        </div>
    </div>

    {{-- Mobile Checkout Bar --}}
    <x-course.mobile-checkout-bar :course="$course" />
</x-layouts.app>

<div>
    {{-- Header / Hero Section --}}
    @if ($course)
        <x-course.details-header :course="$course" />

        <div class="bg-[#FAFBFC] min-h-screen">
            {{-- Padding disesuaikan agar simetris dengan header sebelumnya --}}
            <div class="max-w-[1440px] mx-auto px-6 2xl:px-48 py-16">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 lg:gap-16">

                    {{-- Kolom Kiri: Konten Utama --}}
                    <div class="lg:col-span-3 space-y-16">

                        {{-- Deskripsi --}}
                        <section class="relative">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="w-2 h-6 bg-jti-yellow rounded-full"></span>
                                <h2 class="text-xl font-bold text-gray-900 tracking-tight">Deskripsi Kursus</h2>
                            </div>

                            {{-- Typography yang bersih dengan line-height yang lega --}}
                            <div
                                class="prose max-w-none prose-p:text-gray-600 prose-headings:text-gray-900 prose-strong:text-jti-primary leading-relaxed">
                                {!! $course->description !!}
                            </div>
                        </section>

                        {{-- Kurikulum (Accordion) --}}
                        <section class="relative">
                            <div class="flex items-center justify-between mb-8">
                                <div class="flex items-center gap-3">
                                    <span class="w-2 h-6 bg-jti-yellow rounded-full"></span>
                                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Kurikulum Pembelajaran
                                    </h2>
                                </div>
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.15em]">
                                    {{ $course->chapters->count() }} Modul
                                </span>
                            </div>

                            {{-- Accordion Container: Menggunakan pembungkus minimalis --}}
                            <div class="space-y-3">
                                @foreach ($course->chapters as $chapter)
                                    <x-course.chapter-accordion :chapter="$chapter" />
                                @endforeach
                            </div>
                        </section>

                        {{-- Section Tambahan (Opsional: Persyaratan) --}}
                        @if ($course->requirements)
                            <section class="p-8 bg-white border border-gray-100 rounded-2xl">
                                <h2 class="text-lg font-bold text-gray-900 mb-4">Persyaratan Belajar</h2>
                                <div class="text-gray-600 text-sm">
                                    {!! $course->requirements !!}
                                </div>
                            </section>
                        @endif
                    </div>

                    {{-- Kolom Kanan: Sidebar Floating Card --}}
                    <div class="relative hidden lg:block">
                        <div class="sticky top-24">
                            {{-- Soft shadow untuk efek kedalaman --}}
                            <div class="absolute -inset-4 bg-gray-200/20 blur-2xl rounded-[32px] -z-10"></div>
                            <x-course.purchase-card :course="$course" :is_user_enrolled="$is_user_enrolled" />
                        </div>
                    </div>

                </div>
            </div>
            <x-course.mobile-checkout-bar :course="$course" :is_user_enrolled="$is_user_enrolled" />
        </div>

        {{-- Pending Order Modal Component --}}
        <x-course.pending-order-modal :course="$course" :pendingOrder="$pendingOrder" />
    @else
        <div class="w-full h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Kursus tidak ditemukan</h1>
                <p class="text-gray-600 mb-8">Maaf, kursus yang Anda cari tidak ada atau telah dihapus.</p>
                <a href="{{ route('courses.index') }}"
                    class="inline-block px-6 py-3 bg-jti-yellow text-jti-primary font-semibold rounded-lg hover:bg-amber-400 transition">
                    Kembali ke Daftar Kursus
                </a>
            </div>
        </div>
    @endif
</div>

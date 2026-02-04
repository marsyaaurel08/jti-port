<x-layouts.app>
    <section class="bg-jti-primary py-20 px-6">
        <div class="max-w-[1440px] mx-auto flex flex-col md:flex-row items-center gap-10">
            <div class="md:w-1/2 text-white">
                <h1 class="text-4xl md:text-6xl font-black leading-tight">
                    Tingkatkan Skill IT & <span class="text-jti-yellow">Sertifikasi</span> Bersama Jtiport
                </h1>
                <p class="mt-6 text-gray-300 text-lg">
                    Akses materi eksklusif dari instruktur Polinema untuk persiapan karir global Anda.
                </p>
                <div class="mt-10 flex gap-4">
                    <a href="#course-list"
                        class="bg-jti-orange hover:bg-jti-maroon px-8 py-4 rounded-xl font-bold transition-all shadow-lg shadow-jti-orange/20 inline-block">
                        Mulai Belajar
                    </a>
                </div>
            </div>
            <div class="md:w-1/2">
                <img src="https://placehold.co/600x400/14213D/FFFFFF?text=Jtiport+Hero"
                    class="rounded-2xl shadow-2xl border-4 border-jti-orange/20">
            </div>
        </div>
    </section>

    <main class="max-w-[1440px] mx-auto px-6 py-16">
        <section id="course-list" class="scroll-mt-24">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-jti-primary flex items-center gap-3">
                    <span class="w-2 h-8 bg-jti-maroon rounded-full"></span>
                    Daftar Kursus
                </h2>
                <a href="/courses"
                    class="text-sm font-bold text-jti-maroon hover:text-jti-orange transition-colors">Lihat Semua →</a>
            </div>

            <x-course.grid>
                @foreach ($popularCourses as $course)
                    <x-course.card :course="$course" />
                @endforeach
            </x-course.grid>
        </section>

    </main>
</x-layouts.app>

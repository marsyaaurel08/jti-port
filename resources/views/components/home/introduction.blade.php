<section id="about-section" class="py-20 md:py-32 bg-white overflow-hidden px-6 md:px-48">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">

            {{-- Sisi Kiri: Image Stack dengan Animasi AOS --}}
            <div class="lg:w-1/2 relative">
                {{-- Gambar Utama (Belakang) --}}
                <div class="w-4/5 rounded-sm overflow-hidden shadow-sm"
                     data-aos="fade-right"
                     data-aos-duration="1000">
                    <img src="https://placehold.co/600x500/14213d/FFFFFF?text=Polinema+Campus" alt="Kampus Polinema"
                        class="w-full h-auto object-cover">
                </div>

                {{-- Gambar Depan (Overlay) --}}
                <div class="absolute top-20 right-0 w-3/5 rounded-sm overflow-hidden shadow-2xl border-[8px] border-white"
                     data-aos="fade-up"
                     data-aos-delay="400"
                     data-aos-duration="1000">
                    <img src="https://placehold.co/500x600/ffc107/14213d?text=Jtiport+Platform" alt="Jtiport Interface"
                        class="w-full h-auto object-cover">
                </div>

                {{-- Floating Experience Box --}}
                <div class="absolute bottom-10 left-10 bg-jti-yellow p-6 rounded-sm shadow-xl"
                     data-aos="zoom-in"
                     data-aos-delay="800">
                    <p class="text-4xl font-black text-jti-primary leading-none">CATC</p>
                    <p class="text-[10px] text-jti-primary/70 font-bold uppercase tracking-wider mt-2">Authorized Center</p>
                </div>
            </div>

            {{-- Sisi Kanan: Konten Teks dengan Staggered Animation --}}
            <div class="lg:w-1/2 space-y-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3" data-aos="fade-down">
                        <span class="w-8 h-1 bg-jti-yellow"></span>
                        <span class="text-jti-primary font-bold text-xs uppercase tracking-[0.25em]">About Jtiport</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-bold text-jti-primary leading-tight"
                        data-aos="fade-up"
                        data-aos-delay="200">
                        Platform Pelatihan & <br>
                        <span class="relative">
                            Ujian Sertifikasi
                            <span class="absolute bottom-1 left-0 w-full h-3 bg-jti-yellow/40 -z-10"></span>
                        </span>
                    </h2>
                </div>

                <div class="border-l-4 border-jti-yellow pl-6 italic text-gray-700 text-lg bg-jti-light/50 py-4"
                     data-aos="fade-left"
                     data-aos-delay="400">
                    "Memastikan setiap mahasiswa memiliki standar kompetensi global melalui ekosistem digital yang terintegrasi."
                </div>

                <p class="text-gray-600 leading-relaxed" data-aos="fade-up" data-aos-delay="500">
                    Sebagai **Certiport Authorized Testing Center (CATC)**, Jurusan Teknologi Informasi menyediakan
                    platform **Jtiport** untuk mendukung program pelatihan yang efisien. Kami menjembatani kebutuhan
                    industri dengan kurikulum praktis yang dapat diakses kapan saja.
                </p>

                {{-- Fitur Poin-Poin --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-4 p-4 border border-gray-50 hover:border-jti-yellow/30 transition-colors rounded-md"
                         data-aos="zoom-in-up"
                         data-aos-delay="600">
                        <div class="text-jti-yellow mt-1">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-jti-primary text-sm uppercase">Kurikulum CATC</h4>
                            <p class="text-xs text-gray-500 mt-1">Materi standar internasional dari vendor resmi.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 border border-gray-50 hover:border-jti-yellow/30 transition-colors rounded-md"
                         data-aos="zoom-in-up"
                         data-aos-delay="700">
                        <div class="text-jti-yellow mt-1">
                            <i data-lucide="layout" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-jti-primary text-sm uppercase">Jtiport Hub</h4>
                            <p class="text-xs text-gray-500 mt-1">Satu pintu untuk semua data pelatihan Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4" data-aos="fade-up" data-aos-delay="800">
                    <a href="https://certiport.pearsonvue.com/CATCs.aspx" target="_blank"
                        class="inline-flex items-center gap-3 bg-jti-yellow text-jti-primary px-10 py-4 rounded-sm font-bold text-xs uppercase tracking-widest hover:bg-jti-primary hover:text-white transition-all duration-300 shadow-lg shadow-jti-yellow/20 group">
                        Tentang CATC
                        <i data-lucide="chevron-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

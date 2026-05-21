<section id="about-section" class="py-20 md:py-32 bg-white overflow-hidden px-6 md:px-48">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">

            {{-- Sisi Kiri: Image Stack dengan Animasi AOS --}}
            <div class="lg:w-1/2 relative">
                {{-- Gambar Utama (Belakang) --}}
                <div class="w-4/5 rounded-sm overflow-hidden shadow-sm"
                     data-aos="fade-right"
                     data-aos-duration="1000">
                    <img src="{{ asset('storage/' . setting('introduction_main_image')) }}" alt="Kampus Polinema"
                        class="w-full h-auto object-cover">
                </div>

                {{-- Gambar Depan (Overlay) --}}
                <div class="absolute top-20 right-0 w-3/5 rounded-sm overflow-hidden shadow-2xl border-[8px] border-white"
                     data-aos="fade-up"
                     data-aos-delay="400"
                     data-aos-duration="1000">
                    <img src="{{ asset('storage/' . setting('introduction_overlay_image')) }}" alt="Jtiport Interface"
                        class="w-full h-auto object-cover">
                </div>

                {{-- Floating Experience Box --}}
                <div class="absolute bottom-10 left-10 bg-jti-yellow p-6 rounded-sm shadow-xl"
                     data-aos="zoom-in"
                     data-aos-delay="800">
                    <p class="text-4xl font-black text-jti-primary leading-none">{{ setting('introduction_floating_title') }}</p>
                    <p class="text-[10px] text-jti-primary/70 font-bold uppercase tracking-wider mt-2">{{ setting('introduction_floating_subtitle') }}</p>
                </div>
            </div>

            {{-- Sisi Kanan: Konten Teks dengan Staggered Animation --}}
            <div class="lg:w-1/2 space-y-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3" data-aos="fade-down">
                        <span class="w-8 h-1 bg-jti-yellow"></span>
                        <span class="text-jti-primary font-bold text-xs uppercase tracking-[0.25em]">{{ setting('introduction_badge_text') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-bold text-jti-primary leading-tight"
                        data-aos="fade-up"
                        data-aos-delay="200">
                        {{ setting('introduction_title_line_1') }} <br>
                        <span class="relative">
                            {{ setting('introduction_title_highlight') }}
                            <span class="absolute bottom-1 left-0 w-full h-3 bg-jti-yellow/40 -z-10"></span>
                        </span>
                    </h2>
                </div>

                <div class="border-l-4 border-jti-yellow pl-6 italic text-gray-700 text-lg bg-jti-light/50 py-4"
                     data-aos="fade-left"
                     data-aos-delay="400">
                    "{{ setting('introduction_quote') }}"
                </div>

                <p class="text-gray-600 leading-relaxed" data-aos="fade-up" data-aos-delay="500">
                    {{ setting('introduction_description') }}
                </p>

                {{-- Fitur Poin-Poin --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-4 p-4 border border-gray-50 hover:border-jti-yellow/30 transition-colors rounded-md"
                         data-aos="zoom-in-up"
                         data-aos-delay="600">
                        <div class="text-jti-yellow mt-1">
                            <i data-lucide="{{ setting('introduction_feature_1_icon') }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-jti-primary text-sm uppercase">{{ setting('introduction_feature_1_title') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ setting('introduction_feature_1_description') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 border border-gray-50 hover:border-jti-yellow/30 transition-colors rounded-md"
                         data-aos="zoom-in-up"
                         data-aos-delay="700">
                        <div class="text-jti-yellow mt-1">
                            <i data-lucide="{{ setting('introduction_feature_2_icon') }}" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-jti-primary text-sm uppercase">{{ setting('introduction_feature_2_title') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ setting('introduction_feature_2_description') }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4" data-aos="fade-up" data-aos-delay="800">
                    <a href="{{ setting('introduction_button_link') }}" target="_blank"
                        class="inline-flex items-center gap-3 bg-jti-yellow text-jti-primary px-10 py-4 rounded-sm font-bold text-xs uppercase tracking-widest hover:bg-jti-primary hover:text-white transition-all duration-300 shadow-lg shadow-jti-yellow/20 group">
                        {{ setting('introduction_button_text') }}
                        <i data-lucide="chevron-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

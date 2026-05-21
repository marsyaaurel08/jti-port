{{-- CTA SECTION --}}
<section class="relative py-24 pb-44 bg-white overflow-hidden p-6 md:px-48">
    {{-- Aksen Latar Belakang --}}
    <div class="absolute top-0 right-0 w-1/3 h-full bg-jti-light/50 -skew-x-12 translate-x-20" data-aos="fade-left"
        data-aos-duration="1500"></div>

    <div class="max-w-[1440px] mx-auto px-6 lg:px-12 relative z-10">
        <div class="relative flex flex-col lg:flex-row items-stretch">

            {{-- Sisi Kiri: Branding & Visual --}}
            <div class="lg:w-5/12 bg-jti-primary p-12 md:p-16 flex flex-col justify-between rounded-l-sm"
                data-aos="fade-right" data-aos-duration="1000">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2" data-aos="fade-right" data-aos-delay="200">
                        <span class="w-8 h-[2px] bg-jti-yellow"></span>
                        <span class="text-jti-yellow font-bold text-xs uppercase tracking-[0.3em]">{{ setting('cta_badge_text') }}</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-white leading-[1.1] uppercase tracking-tighter"
                        data-aos="fade-up" data-aos-delay="400">
                        {{ setting('cta_title_line_1') }} <br> {{ setting('cta_title_line_2') }} <span class="text-jti-yellow">{{ setting('cta_title_highlight') }}</span>
                    </h2>
                </div>

                {{-- Elemen Badge Melingkar --}}
                <div class="mt-12 lg:mt-0 relative w-24 h-24 animate-spin-slow" data-aos="zoom-in" data-aos-delay="600">
                    <svg viewBox="0 0 100 100" class="fill-white/20">
                        <path id="ctaCirclePath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"
                            fill="transparent" />
                        <text class="text-[10px] font-bold uppercase tracking-[2px]">
                            <textPath xlink:href="#ctaCirclePath">{{ setting('cta_rotating_text') }}</textPath>
                        </text>
                    </svg>
                </div>
            </div>

            {{-- Sisi Kanan: Konten & Action --}}
            <div class="lg:w-7/12 bg-white p-12 md:p-16 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border-y border-r border-gray-100 rounded-r-sm flex flex-col justify-center"
                data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <div class="max-w-xl">
                    <h3 class="text-2xl md:text-3xl font-extrabold text-jti-primary mb-6 leading-tight"
                        data-aos="fade-up" data-aos-delay="400">
                        {{ setting('cta_heading') }}
                    </h3>
                    <p class="text-gray-500 text-lg leading-relaxed mb-10" data-aos="fade-up" data-aos-delay="500">
                        {{ setting('cta_description') }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4" data-aos="fade-up" data-aos-delay="600">
                        <a href="{{ setting('cta_primary_button_link') }}"
                            class="inline-flex items-center justify-center gap-3 bg-jti-yellow text-jti-primary px-10 py-5 rounded-sm font-bold uppercase text-[11px] tracking-[0.2em] hover:bg-jti-primary hover:text-white transition-all duration-500 shadow-lg shadow-jti-yellow/20 group">
                            {{ setting('cta_primary_button_text') }}
                            <i data-lucide="user-plus" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
                        </a>

                        <a href="https://wa.me/{{ setting('cta_whatsapp_number') }}"
                            class="inline-flex items-center justify-center gap-3 bg-white border-2 border-gray-200 text-jti-primary px-10 py-5 rounded-sm font-bold uppercase text-[11px] tracking-[0.2em] hover:border-jti-yellow transition-all duration-300">
                            {{ setting('cta_secondary_button_text') }}
                            <i data-lucide="message-circle" class="w-4 h-4 text-jti-yellow"></i>
                        </a>
                    </div>

                    {{-- Footer Kecil di CTA --}}
                    <div class="mt-8 pt-8 border-t border-gray-50 flex items-center gap-4" data-aos="fade-in"
                        data-aos-delay="800">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-200"></div>
                            <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-300"></div>
                            <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-400"></div>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">{{ setting('cta_footer_text') }} <span
                                class="text-jti-primary font-bold">{{ setting('cta_footer_highlight') }}</span> {{ setting('cta_footer_suffix') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

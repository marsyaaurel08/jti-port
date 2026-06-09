<section class="py-12 bg-white overflow-hidden p-6 md:p-48 md:py-20">
    <div class="max-w-[1440px] mx-auto text-center relative">

        {{-- Header Section --}}
        <div class="max-w-3xl mx-auto mb-8 space-y-4">
            <h2 class="text-3xl md:text-5xl font-bold text-jti-primary leading-tight tracking-tight" data-aos="fade-up"
                data-aos-duration="1000">
                {{ setting('instructors_title_line_1') }} <br>
                <span class="relative inline-block">
                    {{ setting('instructors_title_highlight') }}
                    <span class="absolute bottom-2 left-0 w-full h-3 bg-jti-yellow/30 -z-10"></span>
                </span>
            </h2>
            <p class="text-gray-500 text-sm md:text-base max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                {{ setting('instructors_description') }}
            </p>
        </div>

        {{-- Carousel Container --}}
        <div class="relative px-0" data-aos="fade-up" data-aos-delay="500">
            <div id="instructor-carousel" class="splide" aria-label="Instructor Carousel" data-splide-per-page="4"
                data-splide-type="loop" data-splide-autoplay="true" data-splide-interval="3500"
                data-splide-gap="1.5rem">

                {{-- Track Slider --}}
                <div class="splide__track overflow-visible! md:overflow-hidden!">
                    <ul class="splide__list">

                        @php
                            use App\Models\HomeInstructor;
                            $instructors = HomeInstructor::active()->orderBy('sort_order')->get();
                        @endphp

                        @foreach ($instructors as $instructor)
                            <li class="splide__slide px-3 py-6">
                                <div
                                    class="group relative overflow-hidden bg-white rounded-sm shadow-md transition-all duration-500 h-full hover:-translate-y-1 hover:shadow-xl">

                                    {{-- Image --}}
                                    <div class="relative aspect-[4/5] overflow-hidden bg-jti-primary">
                                        <img src="{{ asset('storage/' . $instructor['image']) }}" alt="{{ $instructor['name'] }}"
                                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                            loading="lazy">

                                        {{-- Gradient overlay --}}
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-jti-primary/80 via-transparent to-transparent opacity-60 group-hover:opacity-0 transition-opacity duration-500">
                                        </div>

                                        {{-- Info overlay --}}
                                        <div
                                            class="absolute bottom-0 left-0 w-full p-6 bg-jti-primary border-t-4 border-jti-yellow transform translate-y-6 group-hover:translate-y-0 transition-transform duration-500 z-10">
                                            <p
                                                class="text-jti-yellow text-[10px] font-bold uppercase tracking-[0.2em] mb-1">
                                                {{ $instructor['role'] }}
                                            </p>
                                            <h4
                                                class="text-white font-bold text-lg leading-tight uppercase tracking-wide">
                                                {{ $instructor['name'] }}
                                            </h4>
                                            <div
                                                class="w-0 group-hover:w-12 h-[2px] bg-jti-yellow mt-3 transition-all duration-700 delay-100">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>

        {{-- CTA --}}
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-6" data-aos="zoom-in"
            data-aos-delay="1200">
            <a href="{{ setting('instructors_button_link') }}"
                class="inline-flex items-center gap-3 bg-jti-yellow text-jti-primary px-10 py-4 rounded-sm font-bold text-xs uppercase tracking-widest hover:bg-jti-primary hover:text-white transition-all duration-300 shadow-xl shadow-jti-yellow/20 group">
                {{ setting('instructors_button_text') }}
                <i data-lucide="book-open" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
            </a>
        </div>

    </div>
</section>

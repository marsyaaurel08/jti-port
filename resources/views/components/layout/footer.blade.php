<footer class="bg-jti-primary text-white pt-20 pb-10 border-t-4 border-jti-yellow">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">

            {{-- Brand Section --}}
            <div class="space-y-6">
                <a href="/" class="inline-block transition-opacity hover:opacity-80 group">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-sm shadow-lg">
                            <img src="{{ asset('img/logo-jti.png') }}" class="h-9 w-auto object-contain" alt="Logo JTI">
                        </div>
                        <span class="text-2xl font-bold tracking-tight">Jti<span
                                class="text-jti-yellow">port</span></span>
                    </div>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Learning Management System resmi JTI Polinema sebagai
                    <span class="text-jti-yellow font-semibold italic">Certiport Authorized Testing Center
                        (CATC)</span>.
                    Membantu melatih mahasiswa untuk memperoleh sertifikasi internasional.
                </p>
                <div class="flex gap-3" wire:ignore>
                    <a href="#"
                        class="w-9 h-9 rounded-sm bg-white/5 flex items-center justify-center hover:bg-jti-yellow hover:text-jti-primary transition-all duration-300">
                        <x-lucide-instagram class="w-4 h-4" />
                    </a>
                    <a href="#"
                        class="w-9 h-9 rounded-sm bg-white/5 flex items-center justify-center hover:bg-jti-yellow hover:text-jti-primary transition-all duration-300">
                        <x-lucide-youtube class="w-4 h-4" />
                    </a>
                    <a href="#"
                        class="w-9 h-9 rounded-sm bg-white/5 flex items-center justify-center hover:bg-jti-yellow hover:text-jti-primary transition-all duration-300">
                        <x-lucide-facebook class="w-4 h-4" />
                    </a>
                </div>
            </div>

            {{-- Ecosystem Links --}}
            <div>
                <h4 class="text-white font-bold tracking-[0.15em] text-[11px] uppercase mb-8 flex items-center gap-2">
                    <span class="w-3 h-[2px] bg-jti-yellow"></span>
                    Ekosistem Kami
                </h4>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li><a href="https://polinema.ac.id" target="_blank"
                            class="hover:text-jti-yellow transition-colors flex items-center gap-2 group">
                            <x-lucide-chevron-right
                                class="w-3 h-3 text-jti-yellow opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all"/>
                            Politeknik Negeri Malang
                        </a></li>
                    <li><a href="https://jti.polinema.ac.id" target="_blank"
                            class="hover:text-jti-yellow transition-colors flex items-center gap-2 group">
                            <x-lucide-chevron-right
                                class="w-3 h-3 text-jti-yellow opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all"/>
                            Jurusan Teknologi Informasi
                        </a></li>
                    <li><a href="https://certiport.pearsonvue.com/" target="_blank"
                            class="hover:text-jti-yellow transition-colors flex items-center gap-2 group">
                            <x-lucide-chevron-right
                                class="w-3 h-3 text-jti-yellow opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all"/>
                            Certiport Global
                        </a></li>
                </ul>
            </div>

            {{-- Navigation Links --}}
            <div>
                <h4 class="text-white font-bold tracking-[0.15em] text-[11px] uppercase mb-8 flex items-center gap-2">
                    <span class="w-3 h-[2px] bg-jti-yellow"></span>
                    Navigasi
                </h4>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li><a href="/courses"
                            class="hover:text-jti-yellow transition-colors flex items-center gap-2 group">
                            <x-lucide-chevron-right
                                class="w-3 h-3 text-jti-yellow opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all"/>

                            Katalog Kursus
                        </a></li>
                    <li><a href="#classes-list"
                            class="hover:text-jti-yellow transition-colors flex items-center gap-2 group">
                            <x-lucide-chevron-right
                                class="w-3 h-3 text-jti-yellow opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all"/>
                            Persiapan Sertifikasi
                        </a></li>
                    <li><a href="#" class="hover:text-jti-yellow transition-colors flex items-center gap-2 group">
                            <x-lucide-chevron-right
                                class="w-3 h-3 text-jti-yellow opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all"/>
                            Bantuan & FAQ
                        </a></li>
                </ul>
            </div>

            {{-- Contact Section --}}
            <div class="space-y-8">
                <h4 class="text-white font-bold tracking-[0.15em] text-[11px] uppercase mb-8 flex items-center gap-2">
                    <span class="w-3 h-[2px] bg-jti-yellow"></span>
                    Kontak Kami
                </h4>
                <div class="space-y-5">
                    <div class="flex items-start gap-4">
                        <div class="bg-jti-yellow/10 p-2 rounded-sm text-jti-yellow">
                            <x-lucide-map-pin class="w-4 h-4"/>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            Gedung Sipil Lt. 7-8, JTI Polinema. Malang, Jawa Timur 65141.
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="bg-jti-yellow/10 p-2 rounded-sm text-jti-yellow">
                            <x-lucide-mail class="w-4 h-4"/>
                        </div>
                        <a href="mailto:jtiport@polinema.ac.id"
                            class="text-sm text-gray-400 hover:text-jti-yellow transition-colors">
                            jtiport@polinema.ac.id
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Copyright --}}
        <div class="border-t border-white/5 pt-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-[11px] text-gray-500 tracking-wider uppercase">
                © 2026 <span class="text-jti-yellow font-semibold">Jtiport Polinema</span>. Seluruh Hak Cipta
                Dilindungi.
            </p>
            <div class="flex gap-8 text-[11px] text-gray-500 tracking-wider uppercase font-medium">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

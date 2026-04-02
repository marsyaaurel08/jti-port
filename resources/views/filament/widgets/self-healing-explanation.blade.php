<x-filament-widgets::widget class="col-span-2">
    <x-filament::section>
        {{-- Header Section dengan Ikon --}}
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-shield-check class="w-6 h-6 text-success-500" />
                <span>Teknologi Self-Healing Sistem</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Sistem perlindungan mandiri untuk menjaga stabilitas layanan secara otomatis.
        </x-slot>

        {{-- Konten Utama --}}
        <div class="space-y-6">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                <h4 class="text-sm font-bold text-gray-950 dark:text-white mb-2">
                    Apa itu Self-Healing?
                </h4>
                <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                    <strong>Self-Healing</strong> adalah kemampuan sistem untuk mendeteksi gangguan secara otomatis.
                    Jika transaksi mengalami error fatal atau berhenti mendadak karena kesalahan jaringan,maka
                    <span class="text-primary-600 dark:text-primary-400 font-medium">sistem secara otomatis memulihkan transaksi tersebut</span>
                    agar data transaksi user dan sistem sesuai dan tanpa perlu campur tangan manual dari tim IT.
                </p>
            </div>

            {{-- Grid Manfaat --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-success-50 dark:bg-success-500/10 rounded-lg">
                        <x-heroicon-m-bolt class="w-5 h-5 text-success-600" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Minim Downtime</p>
                        <p class="text-xs text-gray-500">Mencegah sistem mati lama saat terjadi lonjakan beban.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 bg-primary-50 dark:bg-primary-500/10 rounded-lg">
                        <x-heroicon-m-magnifying-glass-circle class="w-5 h-5 text-primary-600" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Deteksi Proaktif</p>
                        <p class="text-xs text-gray-500">Memantau transaksi user secara real-time 24/7.</p>
                    </div>
                </div>
            </div>

            {{-- Indikator Status --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-white/5">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status Proteksi Jtiport</span>
                <div class="flex items-center gap-2 px-3 py-1 bg-success-50 dark:bg-success-500/10 rounded-full">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-success-500"></span>
                    </span>
                    <span class="text-xs font-bold text-success-700 dark:text-success-400">AKTIF</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

{{-- Candidate Self-Healing Explanation --}}
<x-filament-widgets::widget class="col-span-2">
    <x-filament::section
        icon="heroicon-o-arrow-path-rounded-square"
        icon-size="md"
    >
        <x-slot name="heading">
            Mekanisme Rekonsiliasi Otomatis (Self-Healing)
        </x-slot>

        <x-slot name="description">
            Daftar ini menampilkan order tertunda yang masuk dalam antrean pengecekan otomatis sistem.
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">

            {{-- Bagian Kriteria (Kiri) --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-4 bg-primary-500 rounded-full"></div>
                    <h4 class="text-sm font-semibold tracking-tight text-gray-900 dark:text-white">
                        Kriteria Pemilihan
                    </h4>
                </div>

                <ul class="space-y-2.5 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-start gap-2.5">
                        <x-heroicon-m-check-circle class="w-5 h-5 text-primary-500 shrink-0" />
                        <span>Status order masih <strong>pending</strong>.</span>
                    </li>
                    {{-- <li class="flex items-start gap-2.5">
                        <x-heroicon-m-check-circle class="w-5 h-5 text-primary-500 shrink-0" />
                        <span>Gateway: <strong>BNI</strong> atau <strong>Midtrans</strong> (otomatis).</span>
                    </li> --}}
                    <li class="flex items-start gap-2.5">
                        <x-heroicon-m-check-circle class="w-5 h-5 text-primary-500 shrink-0" />
                        <span>Tidak menggunakan <em>fallback</em> Virtual Account (VA).</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <x-heroicon-m-check-circle class="w-5 h-5 text-primary-500 shrink-0" />
                        <span>Belum menerima <em>webhook / callback</em> dari gateway.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <x-heroicon-m-check-circle class="w-5 h-5 text-primary-500 shrink-0" />
                        <span>Umur order berada di rentang <em>backoff window</em>.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <x-heroicon-m-check-circle class="w-5 h-5 text-primary-500 shrink-0" />
                        <span>Percobaan &lt; 6 kali dan hasil terakhir bukan <strong>paid</strong>.</span>
                    </li>
                </ul>
            </div>

            {{-- Bagian Jadwal & Info Tabel (Kanan) --}}
            <div class="space-y-6">

                {{-- Jadwal Backoff --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-4 bg-gray-500 dark:bg-gray-400 rounded-full"></div>
                        <h4 class="text-sm font-semibold tracking-tight text-gray-900 dark:text-white">
                            Jadwal Backoff (Waktu Tunggu)
                        </h4>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800">
                            <span class="flex items-center justify-center w-5 h-5 rounded bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white">1</span>
                            <span>5 Menit</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800">
                            <span class="flex items-center justify-center w-5 h-5 rounded bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white">2</span>
                            <span>30 Menit</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800">
                            <span class="flex items-center justify-center w-5 h-5 rounded bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white">3</span>
                            <span>1.5 Jam</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800">
                            <span class="flex items-center justify-center w-5 h-5 rounded bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white">4</span>
                            <span>5 Jam</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800">
                            <span class="flex items-center justify-center w-5 h-5 rounded bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white">5</span>
                            <span>10 Jam</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800">
                            <span class="flex items-center justify-center w-5 h-5 rounded bg-white dark:bg-gray-800 shadow-sm text-gray-900 dark:text-white">6</span>
                            <span>24 Jam</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer / Metadata Box --}}
        <div class="mt-6 flex flex-col gap-3 p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm">
                <div class="space-y-1">
                    <p class="text-gray-900 dark:text-white font-medium">Panduan Membaca Tabel:</p>
                    <ul class="flex flex-col sm:flex-row gap-2 sm:gap-6 text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-1.5">
                            <x-heroicon-m-hashtag class="w-4 h-4 text-gray-400" />
                            <span><strong>Inquiry Count:</strong> Jumlah percobaan tercatat.</span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <x-heroicon-m-clock class="w-4 h-4 text-gray-400" />
                            <span><strong>Next Healing:</strong> Perkiraan eksekusi berikutnya.</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- <div class="border-t border-gray-200 dark:border-gray-800 pt-3 flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                <x-heroicon-m-information-circle class="w-4 h-4 shrink-0 mt-0.5" />
                <p>
                    Logika rekonsiliasi dieksekusi secara terpusat oleh <code>SelfHealingService</code>. Widget ini difungsikan sebagai panel informasi; untuk memodifikasi parameter dan perilaku <em>backoff</em>, silakan sesuaikan pada level <em>service</em>.
                </p>
            </div> --}}
        </div>

    </x-filament::section>
</x-filament-widgets::widget>

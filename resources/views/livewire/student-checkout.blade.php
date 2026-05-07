<div class="space-y-8 py-10  px-6 md:px-48">
    {{-- Header --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end md:justify-between gap-4 border-b border-slate-200 pb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-jti-primary tracking-tight">Checkout</h1>
            <p class="text-slate-500 mt-3 text-lg">Selesaikan pembelian Anda dengan proses yang mudah dan aman</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- 1. Order Summary Section --}}
            <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-lg font-bold text-jti-primary flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-jti-yellow"></i>
                        Ringkasan pesanan
                    </h2>
                </div>

                <div class="p-8">
                    <div class="flex flex-col sm:flex-row gap-8">
                        {{-- Product Image --}}
                        <div class="relative group mx-auto sm:mx-0">
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}"
                                class="w-32 h-32 object-cover rounded-xl shadow-md transition-transform group-hover:scale-105">
                        </div>

                        {{-- Product Info --}}
                        <div class="flex-1 flex flex-col justify-center text-center sm:text-left">
                            <h3 class="text-xl font-bold text-jti-primary mb-2 leading-tight">{{ $product->title }}
                            </h3>
                            <div class="flex flex-wrap justify-center sm:justify-start gap-3 mb-4">
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">
                                    @if ($productType === 'course')
                                        {{ $product->category->name ?? 'Kategori' }} •
                                        {{ $product->total_lessons ?? 0 }} Video
                                    @else
                                        Kelas • {{ $product->category->name ?? 'Kategori' }}
                                    @endif
                                </span>
                            </div>

                            @if ($productType === 'class')
                                <div class="grid grid-cols-2 gap-4 text-xs text-slate-500 max-w-xs mx-auto sm:mx-0">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="calendar" class="w-4 h-4 text-jti-yellow"></i>
                                        <span>{{ $product->start_date->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="users" class="w-4 h-4 text-jti-yellow"></i>
                                        <span>{{ $product->current_enrolled }}/{{ $product->max_capacity }}
                                            peserta</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Price Desktop --}}
                        <div class="hidden sm:block text-right self-center">
                            <p class="text-xs font-semibold text-slate-400 mb-1">Harga paket</p>
                            <p class="text-2xl font-bold text-jti-primary">
                                Rp{{ number_format($productPrice, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 2. User Information Section --}}
            <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-jti-primary flex items-center gap-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-jti-yellow"></i>
                        Data diri anda
                    </h2>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Full Name --}}
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Nama lengkap <span
                                    class="text-red-500">*</span></label>
                            <input type="text" wire:model="fullName" placeholder="Masukkan nama lengkap" disabled
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-jti-yellow focus:bg-white focus:border-transparent outline-none transition duration-200 placeholder:text-slate-400">
                            @error('fullName')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" wire:model="email" placeholder="contoh@email.com" disabled
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-jti-yellow focus:bg-white focus:border-transparent outline-none transition duration-200 placeholder:text-slate-400">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Phone Number --}}
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Nomor HP / WhatsApp <span
                                class="text-red-500">*</span></label>
                        <div class="flex gap-0 group">
                            <div
                                class="flex items-center px-4 bg-slate-100 border border-slate-200 border-r-0 rounded-l-xl text-sm font-bold text-slate-600">
                                +62
                            </div>
                            <input type="tel" wire:model="phoneNumber" placeholder="812xxxxxx"
                                class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-r-xl focus:ring-2 focus:ring-jti-yellow focus:bg-white focus:border-transparent outline-none transition duration-200 placeholder:text-slate-400"
                                inputmode="numeric">
                        </div>
                        <p class="text-[10px] text-slate-400 italic">Pastikan nomor aktif untuk menerima notifikasi
                            pembayaran.</p>
                        @error('phoneNumber')
                            <div class="flex items-start gap-2 mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-circle-alert-icon lucide-circle-alert">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" x2="12" y1="8" y2="12" />
                                    <line x1="12" x2="12.01" y1="16" y2="16" />
                                </svg>
                                <p class="text-red-600 text-xs font-medium">Nomor HP tidak valid</p>
                            </div>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- 3. Promo & Payment Combined for cleaner look --}}
            <div class="grid grid-cols-1 gap-8">
                {{-- Promo Code Section --}}
                <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-8 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <h2 class="text-md font-bold text-jti-primary flex items-center gap-2">
                            <i data-lucide="ticket" class="w-5 h-5 text-jti-yellow"></i>
                            Gunakan kode promo
                        </h2>
                    </div>
                    <div class="p-8 text-sm">
                        @if (!$promoApplied)
                            <div class="flex gap-3">
                                <input type="text" wire:model="promoCode"
                                    placeholder="Punya kode promo? Masukkan di sini"
                                    class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-jti-yellow outline-none transition placeholder:text-slate-400">
                                <button wire:click="applyPromoCode"
                                    class="px-6 py-3 bg-jti-primary text-white font-bold rounded-xl hover:bg-slate-800 transition shadow-sm active:scale-95">
                                    Terapkan
                                </button>
                            </div>
                        @else
                            <div
                                class="flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-xl p-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-emerald-500 rounded-full p-1 text-white">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-emerald-900 leading-none mb-1">{{ $promoCode }}
                                        </p>
                                        <p class="text-xs text-emerald-600 font-medium">{{ $promoMessage }}</p>
                                    </div>
                                </div>
                                <button wire:click="removePromoCode" type="button"
                                    class="text-emerald-400 hover:text-red-500 transition">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- 4. Transfer Method Section --}}
                <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-lg font-bold text-jti-primary flex items-center gap-2">
                            <i data-lucide="credit-card" class="w-5 h-5 text-jti-yellow"></i>
                            Pilih metode pembayaran
                        </h2>
                    </div>

                    <div class="p-8 space-y-4">
                        @foreach ($transferMethods as $key => $method)
                            <div
                                class="group relative rounded-2xl transition-all duration-300 {{ $transferMethod === $key ? 'ring-2 ring-jti-yellow border-transparent bg-yellow-50/30 shadow-md' : 'border border-slate-200 bg-white hover:border-jti-yellow hover:bg-slate-50' }}">
                                <label class="flex items-center gap-4 p-4 cursor-pointer w-full">

                                    <!-- 1. Radio Button -->
                                    <div class="flex-shrink-0 flex items-center">
                                        <input type="radio" name="transferMethod" wire:model.live="transferMethod"
                                            value="{{ $key }}"
                                            class="w-5 h-5 text-jti-yellow border-slate-300 focus:ring-jti-yellow cursor-pointer transition-colors">
                                    </div>

                                    <!-- 2. Logo Container -->
                                    <div
                                        class="flex-shrink-0 w-16 h-12 bg-white border border-slate-100 rounded-lg flex items-center justify-center p-1.5 shadow-sm overflow-hidden">
                                        <img src="{{ $method['logo'] }}" alt="{{ $method['name'] }}"
                                            class="max-w-full max-h-full object-contain mix-blend-multiply"
                                            onerror="this.style.display='none'">
                                        <!-- onerror berguna agar jika gambar gagal dimuat/belum ada, tidak muncul icon broken -->
                                    </div>

                                    <!-- 3. Text Details -->
                                    <div class="flex-1 flex flex-col justify-center">
                                        <p class="font-bold text-slate-900 text-sm md:text-base leading-tight">
                                            {{ $method['name'] }}
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1 leading-snug">
                                            {{ $method['description'] }}
                                        </p>
                                    </div>

                                </label>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            {{-- Warning Box --}}
            <div class="bg-amber-50 border border-amber-200 p-6 rounded-2xl">
                <div class="flex gap-4">
                    <div class="bg-amber-400 p-2 rounded-lg h-fit">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="font-bold text-amber-900 text-base">Informasi penting</p>
                        <p class="text-sm text-amber-800/80 mt-1 leading-relaxed">
                            Pastikan transfer tepat hingga <strong>3 digit terakhir</strong>. Pembayaran yang tidak
                            sesuai nominal akan menghambat proses verifikasi otomatis oleh sistem.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Order Summary --}}
        <div class="lg:col-span-1">
            <div class="sticky top-8 bg-jti-primary rounded-3xl p-8 text-white shadow-2xl overflow-hidden relative">
                {{-- Decorative circles --}}
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/5 rounded-full"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-jti-yellow/10 rounded-full"></div>

                <h3 class="text-xl font-bold mb-8 relative">Ringkasan harga</h3>

                <div class="space-y-5 relative">
                    <div class="flex justify-between items-center text-slate-300">
                        <span class="text-sm">Harga kursus</span>
                        <span class="font-bold text-white">Rp{{ number_format($productPrice, 0, ',', '.') }}</span>
                    </div>

                    @if ($discountAmount > 0)
                        <div class="flex justify-between items-center bg-white/10 p-3 rounded-xl">
                            <span class="text-sm text-emerald-300 flex items-center gap-2 font-medium">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                Potongan harga
                            </span>
                            <span
                                class="font-bold text-emerald-300">-Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="pt-6 border-t border-white/10 mt-6">
                        <p class="text-slate-400 text-xs mb-2">Total yang harus dibayar:</p>
                        <p class="text-4xl font-extrabold text-jti-yellow tracking-tight">
                            Rp{{ number_format($totalAmount, 0, ',', '.') }}
                        </p>
                    </div>

                    <button wire:click="processPayment" wire:loading.attr="disabled"
                        class="w-full mt-8 py-4 bg-jti-yellow text-jti-primary font-bold rounded-xl hover:bg-yellow-400 transition-all duration-300 shadow-lg shadow-jti-yellow/20 flex items-center justify-center gap-3 active:scale-[0.98] disabled:opacity-50">

                        <span wire:loading.remove wire:target="processPayment">Bayar sekarang</span>
                        <span wire:loading wire:target="processPayment" class="flex items-center gap-2">
                            Memproses...
                        </span>
                        <i data-lucide="arrow-right" class="w-5 h-5" wire:loading.remove
                            wire:target="processPayment"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
    {{-- VA Instructions Modal --}}
    @if ($showVAInstructions && !empty($vaData))
        <x-checkout.va-instructions-modal
            :show="$showVAInstructions"
            :vaData="$vaData"
            :transferMethodName="$transferMethods[$transferMethod]['name'] ?? 'Transfer'"
            :instructions="$transferInstructions[$transferMethod] ?? []"
            :redirectRoute="$redirectRoute" />
    @endif
</div>
@script
    <script>
        $wire.on('trigger-midtrans-snap', (event) => {
            window.snap.pay(event.token, {
                onSuccess: function(result) {
                    window.location.href = '/student/transactions';
                },
                onPending: function(result) {
                    window.location.href = '/student/orders';
                },
                onError: function(result) {
                    alert("Pembayaran gagal!");
                }
            });
        });
    </script>
@endscript

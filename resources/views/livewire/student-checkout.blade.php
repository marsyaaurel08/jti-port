<div class="space-y-6 py-6 px-4 sm:px-6 md:px-12 lg:px-24 xl:px-48">
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-3 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-jti-primary tracking-tight">Checkout</h1>
            <p class="text-slate-500 mt-2 text-sm sm:text-lg">Selesaikan pembelian Anda dengan proses yang mudah dan aman
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Order Summary Section --}}
            <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div
                    class="px-4 py-4 sm:px-8 sm:py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-base sm:text-lg font-bold text-jti-primary flex items-center gap-2">
                        <x-lucide-shopping-bag class="w-5 h-5 text-jti-yellow" />
                        Ringkasan pesanan
                    </h2>
                </div>

                <div class="p-4 sm:p-8">
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-8">
                        {{-- Product Image --}}
                        <div class="relative group mx-auto sm:mx-0 shrink-0">
                            <img src="{{ asset('storage/' . $product->thumbnail_url) }}" alt="{{ $product->title }}"
                                class="w-24 h-24 sm:w-32 sm:h-32 object-cover rounded-xl shadow-md transition-transform group-hover:scale-105">
                        </div>

                        {{-- Product Info --}}
                        <div class="flex-1 flex flex-col justify-center text-center sm:text-left">
                            <h3 class="text-lg sm:text-xl font-bold text-jti-primary mb-2 leading-tight">
                                {{ $product->title }}
                            </h3>
                            <div class="flex flex-wrap justify-center sm:justify-start gap-3 mb-3">
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
                                <div
                                    class="grid grid-cols-2 gap-4 text-xs text-slate-500 max-w-xs mx-auto sm:mx-0 mb-3 sm:mb-0">
                                    <div class="flex items-center justify-center sm:justify-start gap-2">
                                        <x-lucide-calendar class="w-4 h-4 text-jti-yellow" />
                                        <span>{{ $product->start_date->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-center sm:justify-start gap-2">
                                        <x-lucide-users class="w-4 h-4 text-jti-yellow" />
                                        <span>{{ $product->current_enrolled }}/{{ $product->max_capacity }}
                                            peserta</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Price Mobile (Hanya muncul di mobile) --}}
                            <div class="block sm:hidden mt-2 pt-3 border-t border-dashed border-slate-100">
                                <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Harga paket</p>
                                <p class="text-xl font-extrabold text-jti-primary">
                                    Rp{{ number_format($productPrice, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Price Desktop (Sembunyi di mobile) --}}
                        <div class="hidden sm:block text-right self-center shrink-0">
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
                <div class="px-4 py-4 sm:px-8 sm:py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base sm:text-lg font-bold text-jti-primary flex items-center gap-2">
                        <x-lucide-user-check class="w-5 h-5 text-jti-yellow" />
                        Data diri anda
                    </h2>
                </div>

                <div class="p-4 sm:p-8 space-y-4 sm:space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        {{-- Full Name --}}
                        <div class="space-y-2 font-semibold tracking-wide text-slate-500">
                            <label class="text-sm font-bold text-slate-700">Nama lengkap <span
                                    class="text-red-500">*</span></label>
                            <input type="text" wire:model="fullName" placeholder="Masukkan nama lengkap" disabled
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-jti-yellow focus:bg-white focus:border-transparent outline-none transition duration-200 placeholder:text-slate-400 text-sm sm:text-base">
                            @error('fullName')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="space-y-2 font-semibold tracking-wide text-slate-500">
                            <label class="text-sm font-bold text-slate-700">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" wire:model="email" placeholder="contoh@email.com" disabled
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-jti-yellow focus:bg-white focus:border-transparent outline-none transition duration-200 placeholder:text-slate-400 text-sm sm:text-base">
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
                                class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-r-xl focus:ring-2 focus:ring-jti-yellow focus:bg-white focus:border-transparent outline-none transition duration-200 placeholder:text-slate-400 text-sm sm:text-base"
                                inputmode="numeric">
                        </div>
                        <p class="text-[10px] text-slate-400 italic">Pastikan nomor aktif untuk menerima notifikasi
                            pembayaran.</p>
                        @error('phoneNumber')
                            <div class="flex items-start gap-2 mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <x-lucide-alert-circle class="w-4 h-4 text-red-500 shrink-0" />
                                <p class="text-red-600 text-xs font-medium">Nomor HP tidak valid</p>
                            </div>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- 3. Promo Section --}}
            <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div
                    class="px-4 py-4 sm:px-8 sm:py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h2 class="text-base font-bold text-jti-primary flex items-center gap-2">
                        <x-lucide-tag class="w-5 h-5 text-jti-yellow" />
                        Gunakan kode promo
                    </h2>
                </div>
                <div class="p-4 sm:p-8 text-sm">
                    @if (!$promoApplied)
                        <div class="flex flex-col gap-2">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input type="text" wire:model.defer="promoCode"
                                    placeholder="Punya kode promo? Masukkan di sini"
                                    class="flex-1 px-4 py-3 bg-slate-50 border @if ($promoMessage && !$promoApplied) border-red-300 @else border-slate-200 @endif rounded-xl focus:ring-2 focus:ring-jti-yellow outline-none transition placeholder:text-slate-400 text-sm">
                                <button wire:click="applyPromoCode" wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-6 py-3 bg-jti-primary text-white font-bold rounded-xl hover:bg-slate-800 transition shadow-sm active:scale-95 text-sm shrink-0 flex items-center justify-center gap-2 disabled:opacity-75 tracking-wide">
                                    <span wire:loading.remove wire:target="applyPromoCode">Terapkan</span>
                                    <span wire:loading wire:target="applyPromoCode">Memeriksa...</span>
                                </button>
                            </div>
                            @if ($promoMessage)
                                <div
                                    class="flex items-start gap-1 flex-row text-xs font-semibold text-red-500 mt-1 bg-red-50 px-3 py-2 rounded-lg border border-red-100">
                                    <x-lucide-info class="w-4 h-4 shrink-0 text-red-400" />
                                    <p>{{ $promoMessage }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col gap-2">
                            <div
                                class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-xl p-4 gap-3 shadow-inner">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="bg-linear-to-br from-emerald-400 to-emerald-500 rounded-full p-1.5 text-white shrink-0 shadow-sm border border-emerald-300/50">
                                        <x-lucide-check class="w-4 h-4 text-white" />
                                    </div>
                                    <div>
                                        <p class="font-bold text-emerald-900 leading-none mb-1 text-sm tracking-wide">
                                            {{ $promoCode }}</p>
                                        <p class="text-xs text-emerald-700 font-medium">{{ $promoMessage }}</p>
                                    </div>
                                </div>
                                <button wire:click="removePromoCode" type="button" wire:loading.attr="disabled"
                                    wire:target="removePromoCode"
                                    class="text-slate-400 hover:text-red-500 transition-colors p-2 hover:bg-red-50 rounded-lg shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <x-lucide-trash-2 wire:loading.remove wire:target="removePromoCode" class="w-5 h-5 " />
                                    <x-lucide-loader-2 wire:loading wire:target="removePromoCode" class="animate-spin w-5 h-5 text-red-500" />
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            {{-- 4. Transfer Method Section --}}
            <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-4 py-4 sm:px-8 sm:py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base sm:text-lg font-bold text-jti-primary flex items-center gap-2">
                        <x-lucide-credit-card class="w-5 h-5 text-jti-yellow" />
                        Pilih metode pembayaran
                    </h2>
                </div>

                <div class="p-4 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        @foreach ($transferMethods as $key => $method)
                            <div
                                class="group relative rounded-2xl transition-all duration-300 {{ $transferMethod === $key ? 'ring-2 ring-jti-yellow border-transparent bg-yellow-50/30 shadow-md' : 'border border-slate-200 bg-white hover:border-jti-yellow hover:bg-slate-50' }}">
                                <label class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 cursor-pointer w-full">
                                    <!-- Radio Button -->
                                    <div class="shrink-0 flex items-center">
                                        <input type="radio" name="transferMethod" wire:model.live="transferMethod"
                                            value="{{ $key }}"
                                            class="w-5 h-5 text-jti-yellow border-slate-300 focus:ring-jti-yellow cursor-pointer transition-colors">
                                    </div>

                                    <!-- Logo Container -->
                                    <div
                                        class="shrink-0 w-14 h-10 sm:w-16 sm:h-12 bg-white border border-slate-100 rounded-lg flex items-center justify-center p-1 sm:p-1.5 shadow-sm overflow-hidden">
                                        <img src="{{ asset($method['logo']) }}" alt="{{ $method['name'] }}"
                                            class="max-w-full max-h-full object-contain mix-blend-multiply"
                                            onerror="this.style.display='none'">
                                    </div>

                                    <!-- Text Details -->
                                    <div class="flex-1 flex flex-col justify-center">
                                        <p
                                            class="font-bold text-slate-900 text-xs sm:text-sm md:text-base leading-tight">
                                            {{ $method['name'] }}
                                        </p>
                                        <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-tight">
                                            {{ $method['description'] }}
                                        </p>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('transferMethod')
                        <div class="flex items-start gap-2 mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <x-lucide-alert-circle class="w-4 h-4 text-red-500 shrink-0" />
                            <p class="text-red-600 text-xs font-medium">Silakan pilih metode pembayaran terlebih dahulu.
                            </p>
                        </div>
                    @enderror
                    <div class="bg-amber-50 border border-amber-200 p-4 sm:p-6 rounded-2xl mt-6">
                        <p class="font-bold text-amber-900 text-sm sm:text-base">Informasi penting</p>
                        <p class="text-xs sm:text-sm text-amber-800/80 mt-1 leading-relaxed">
                            Pastikan transfer tepat hingga <strong>3 digit terakhir</strong>. Pembayaran yang tidak
                            sesuai nominal akan menghambat proses verifikasi otomatis oleh sistem.
                        </p>
                    </div>
                </div>
            </section>
        </div>

        {{-- Sidebar: Order Summary --}}
        <div class="lg:col-span-1 lg:sticky lg:top-26">
            <div class="bg-jti-primary rounded-3xl p-6 sm:p-8 text-white shadow-2xl overflow-hidden relative">
                {{-- Decorative circles --}}
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/5 rounded-full"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-jti-yellow/10 rounded-full"></div>

                <h3 class="text-lg sm:text-xl font-bold mb-6 sm:b-8 relative">Ringkasan harga</h3>

                <div class="space-y-4 sm:space-y-5 relative">
                    <div class="flex justify-between items-center text-slate-300">
                        <span class="text-xs sm:text-sm">Harga kursus</span>
                        <span
                            class="font-bold text-white text-sm sm:text-base">Rp{{ number_format($productPrice, 0, ',', '.') }}</span>
                    </div>

                    @if ($discountAmount > 0)
                        <div class="flex justify-between items-center bg-white/10 p-3 rounded-xl">
                            <span class="text-xs sm:text-sm text-emerald-300 flex items-center gap-2 font-medium">
                                <x-lucide-sparkles class="w-4 h-4" />
                                Potongan harga
                            </span>
                            <span
                                class="font-bold text-emerald-300 text-sm sm:text-base">-Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-white/10 mt-4 sm:pt-6 sm:mt-6">
                        <p class="text-slate-400 text-xs mb-1">Total yang harus dibayar:</p>
                        <p class="text-2xl sm:text-4xl font-extrabold text-jti-yellow tracking-tight">
                            Rp{{ number_format($totalAmount, 0, ',', '.') }}
                        </p>
                    </div>

                    <button wire:click="processPayment" wire:loading.attr="disabled"
                        class="w-full mt-6 sm:mt-8 py-3.5 sm:py-4 bg-jti-yellow text-jti-primary font-bold rounded-xl hover:bg-yellow-400 transition-all duration-300 shadow-lg shadow-jti-yellow/20 flex items-center justify-center gap-3 active:scale-[0.98] disabled:opacity-50 text-sm sm:text-base">
                        <span wire:loading.remove wire:target="processPayment">Bayar sekarang</span>
                        <span wire:loading wire:target="processPayment" class="flex items-center gap-2">
                            Memproses...
                        </span>
                        <x-lucide-loader-2 wire:loading wire:target="processPayment" class="animate-spin w-5 h-5 text-jti-primary" />
                    </button>
                </div>
            </div>
        </div>

        <x-checkout.payment-modal :showCustomPaymentModal="$showCustomPaymentModal" :paymentModalData="$paymentModalData" :totalAmount="$this->getTotalAmount()" :isPaid="$isPaid"
            :successCountdown="$successCountdown" />
    </div>
</div>

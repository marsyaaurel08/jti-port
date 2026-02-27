<div class="bg-[#FAFBFC] min-h-screen">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-48 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 lg:gap-16">
            <!-- Sidebar -->
            <div class="lg:col-span-1" wire:ignore>
                <x-user.sidebar :user="$this->user" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-12">
                <!-- Header Section -->
                <section class="relative">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-2 h-6 bg-jti-yellow rounded-full"></span>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">Keranjang Belanja</h1>
                    </div>
                    <p class="text-gray-600 text-lg">{{ $cartCount }} item di keranjang Anda</p>
                </section>

                @if (session('success'))
                    <div
                        class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-start gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 mt-0.5 text-emerald-600" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if (empty($cartItems))
                    <!-- Empty Cart -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-gray-100 mb-6">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Keranjang Kosong</h3>
                        <p class="text-gray-600 mb-6">Mulai tambahkan kursus untuk memulai perjalanan belajar Anda</p>
                        <a href="{{ route('courses.index') }}"
                            class="inline-block px-6 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-semibold rounded-xl transition-colors">
                            Jelajahi Kursus
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Cart Items -->
                        <div class="lg:col-span-2 space-y-4">
                            @foreach ($cartItems as $item)
                                <div wire:key="{{ $item['id'] }}"
                                    class="bg-white rounded-2xl border border-gray-200 p-6 flex items-center justify-between hover:border-gray-300 hover:shadow-sm transition-all">
                                    <div class="flex items-center gap-4 flex-1">
                                        @php
                                            $course = $item['course'] ?? $item['class'];
                                            $thumbnail = $course['thumbnail_url'] ?? null;
                                        @endphp
                                        @if ($thumbnail)
                                            <img src="{{ $thumbnail }}" alt="{{ $course['title'] }}"
                                                class="w-20 h-20 object-cover rounded-xl">
                                        @endif
                                        <div class="flex-1">
                                            <h3 class="text-base font-bold text-gray-900">
                                                {{ $course['title'] }}</h3>
                                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mt-1">
                                                @if ($item['course_id'])
                                                    Kursus
                                                @else
                                                    Kelas
                                                @endif
                                            </p>
                                            <p class="text-jti-yellow font-bold mt-3">Rp
                                                {{ number_format($course['price'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <button wire:click="removeItem({{ $item['id'] }})"
                                        wire:target="removeItem({{ $item['id'] }})" wire:loading.attr="disabled"
                                        class="ml-4 text-red-600 hover:text-red-700 font-semibold text-sm hover:bg-red-50 px-4 py-2 rounded-lg transition-colors"
                                        title="Hapus dari keranjang">
                                        <span wire:loading.remove
                                            wire:target="removeItem({{ $item['id'] }})">Hapus</span>
                                        <span wire:loading wire:target="removeItem({{ $item['id'] }})">
                                            <svg class="animate-spin h-4 w-4 inline" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Summary -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24">
                                <h3 class="text-lg font-bold text-gray-900 mb-6">Ringkasan Order</h3>

                                <div class="space-y-3 border-t border-gray-200 pt-6 mb-6">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 text-sm">Subtotal</span>
                                        <span class="font-semibold text-gray-900">Rp
                                            {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 text-sm">Biaya Admin</span>
                                        <span class="font-semibold text-emerald-600">Gratis</span>
                                    </div>
                                    <div
                                        class="flex justify-between text-lg font-bold border-t border-gray-200 pt-4 mt-4">
                                        <span class="text-gray-900">Total</span>
                                        <span class="text-jti-yellow">Rp
                                            {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('student.cart') }}"
                                    class="w-full px-4 py-3 bg-jti-yellow hover:bg-jti-maroon text-white font-semibold rounded-xl text-center transition-all hover:shadow-lg active:scale-95 block">
                                    Lanjut ke Pembayaran
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

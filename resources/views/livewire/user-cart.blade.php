<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1" wire:ignore>
                <x-user.sidebar :user="$this->user" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
                    <p class="text-gray-600 mt-2">{{ $cartCount }} item di keranjang Anda</p>
                </div>

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @if (empty($cartItems))
                    <!-- Empty Cart -->
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Keranjang Kosong</h3>
                        <p class="text-gray-600 mb-6">Mulai tambahkan kursus untuk memulai perjalanan belajar Anda
                        </p>
                        <a href="{{ route('courses.index') }}"
                            class="inline-block px-6 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-medium rounded-lg transition-colors">
                            Jelajahi Kursus
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Cart Items -->
                        <div class="lg:col-span-2 space-y-4">
                            @foreach ($cartItems as $item)
                                <div wire:key="{{ $item['id'] }}"
                                    class="bg-white rounded-lg shadow-md p-6 flex items-center justify-between hover:shadow-lg transition-shadow">
                                    <div class="flex items-center gap-4 flex-1">
                                        @php
                                            $course = $item['course'] ?? $item['class'];
                                            $thumbnail = $course['thumbnail_url'] ?? null;
                                        @endphp
                                        @if ($thumbnail)
                                            <img src="{{ $thumbnail }}" alt="{{ $course['title'] }}"
                                                class="w-24 h-24 object-cover rounded-lg">
                                        @endif
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $course['title'] }}</h3>
                                            <p class="text-sm text-gray-600">
                                                @if ($item['course_id'])
                                                    Kursus
                                                @else
                                                    Kelas
                                                @endif
                                            </p>
                                            <p class="text-jti-yellow font-bold mt-2">Rp
                                                {{ number_format($course['price'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <button wire:click="removeItem({{ $item['id'] }})" wire:loading.attr="disabled"
                                        class="ml-4 text-red-600 hover:text-red-700 font-medium text-sm hover:bg-red-50 px-3 py-2 rounded-lg transition-colors"
                                        title="Hapus dari keranjang">
                                        <span wire:loading.remove>Hapus</span>
                                        <span wire:loading wire:target="removeItem">
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
                            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Order</h3>

                                <div class="space-y-3 border-t pt-4 mb-6">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="font-medium">Rp
                                            {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Biaya Admin</span>
                                        <span class="font-medium">Gratis</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold border-t pt-3">
                                        <span>Total</span>
                                        <span class="text-jti-yellow">Rp
                                            {{ number_format($totalPrice, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('student.cart') }}"
                                    class="w-full px-4 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-medium rounded-lg text-center transition-colors block">
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

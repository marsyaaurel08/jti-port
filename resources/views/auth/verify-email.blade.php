<x-layouts.app>
    <div class="flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="mx-auto w-12 h-12 bg-jti-yellow rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Verifikasi Email</h1>
                    <p class="text-gray-600 mt-2">Terima kasih telah mendaftar!</p>
                </div>

                <!-- Message -->
                <div class="mb-6">
                    <p class="text-gray-700 text-center">
                        Kami telah mengirimkan link verifikasi ke email Anda.
                        Silakan periksa email Anda dan klik link untuk memverifikasi akun Anda.
                    </p>
                </div>

                <!-- Verification Status -->
                @if (session('resent'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-700 text-sm">
                            ✓ Link verifikasi baru telah dikirim ke email Anda!
                        </p>
                    </div>
                @endif

                <!-- Resend Link -->
                <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
                    @csrf
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-medium rounded-lg transition-colors">
                        Kirim Ulang Link Verifikasi
                    </button>
                </form>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit"
                        class="text-jti-yellow hover:text-jti-maroon font-medium text-sm transition-colors">
                        Keluar
                    </button>
                </form>
            </div>

            <!-- Help Text -->
            <div class="text-center mt-6 text-gray-600 text-sm">
                <p>Jika Anda tidak menerima email, silakan periksa folder spam Anda.</p>
            </div>
        </div>
    </div>
</x-layouts.app>

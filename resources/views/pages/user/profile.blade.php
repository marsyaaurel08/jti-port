<x-layouts.app>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar + Content Container -->
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <x-user.sidebar :user="$user" />
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-md p-8">
                        <!-- Header -->
                        <div class="mb-8 border-b pb-6">
                            <h1 class="text-3xl font-bold text-gray-900">Profil Saya</h1>
                            <p class="text-gray-600 mt-2">Kelola informasi profil dan keamanan akun Anda</p>
                        </div>

                        <!-- Success/Error Messages -->
                        @if (session('success'))
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                                ✓ {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Profile Form -->
                        <form method="POST" action="{{ route('student.profile.update') }}" class="mb-8">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Nama -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama
                                        Lengkap</label>
                                    <input type="text" id="name" name="name"
                                        value="{{ old('name', $user->name) }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                        required>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="{{ old('email', $user->email) }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                        required>
                                </div>

                                <!-- Submit Button -->
                                <div>
                                    <button type="submit"
                                        class="px-6 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-medium rounded-lg transition-colors">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Password Section -->
                        <div class="border-t pt-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Ubah Password</h2>

                            <form method="POST" action="{{ route('student.password.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="space-y-6">
                                    <!-- Current Password -->
                                    <div>
                                        <label for="current_password"
                                            class="block text-sm font-medium text-gray-700 mb-2">Password Saat
                                            Ini</label>
                                        <input type="password" id="current_password" name="current_password"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                            required>
                                    </div>

                                    <!-- New Password -->
                                    <div>
                                        <label for="password"
                                            class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                        <input type="password" id="password" name="password"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                            required>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div>
                                        <label for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi
                                            Password</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                            required>
                                    </div>

                                    <!-- Submit Button -->
                                    <div>
                                        <button type="submit"
                                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                            Ubah Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

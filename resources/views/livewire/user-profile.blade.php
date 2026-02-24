<div class="min-h-screen bg-gray-50">
    <!-- Sidebar + Content Container -->
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1" wire:ignore>
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
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 flex items-start gap-3"
                            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                            <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <ul class="list-disc list-inside text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tabs -->
                    <div class="mb-8 flex gap-4 border-b">
                        <button wire:click="$set('activeTab', 'profile')"
                            class="px-4 py-2 font-medium transition-colors {{ $activeTab === 'profile' ? 'text-jti-yellow border-b-2 border-jti-yellow' : 'text-gray-600 hover:text-gray-900' }}">
                            Informasi Profil
                        </button>
                        <button wire:click="$set('activeTab', 'password')"
                            class="px-4 py-2 font-medium transition-colors {{ $activeTab === 'password' ? 'text-jti-yellow border-b-2 border-jti-yellow' : 'text-gray-600 hover:text-gray-900' }}">
                            Keamanan
                        </button>
                    </div>

                    <!-- Profile Tab -->
                    @if ($activeTab === 'profile')
                        <form wire:submit="updateProfile" class="space-y-6">
                            <!-- Nama -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama
                                    Lengkap</label>
                                <input type="text" id="name" wire:model="name"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                    required>
                                @error('name')
                                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" wire:model="email"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                    required>
                                @error('email')
                                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button type="submit"
                                    class="px-6 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Simpan Perubahan</span>
                                    <span wire:loading>
                                        <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Password Tab -->
                    @if ($activeTab === 'password')
                        <form wire:submit="updatePassword" class="space-y-6">
                            <!-- Current Password -->
                            <div>
                                <label for="currentPassword"
                                    class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                                <input type="password" id="currentPassword" wire:model="currentPassword"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                    required>
                                @error('currentPassword')
                                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password
                                    Baru</label>
                                <input type="password" id="password" wire:model="password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                    required>
                                @error('password')
                                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="passwordConfirmation"
                                    class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                                <input type="password" id="passwordConfirmation" wire:model="passwordConfirmation"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent"
                                    required>
                                @error('passwordConfirmation')
                                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button type="submit"
                                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Ubah Password</span>
                                    <span wire:loading>
                                        <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Mengubah...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

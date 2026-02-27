<div class="bg-[#FAFBFC] min-h-screen">
    <!-- Sidebar + Content Container -->
    <div class="max-w-[1440px] mx-auto px-6 lg:px-48 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 lg:gap-16">
            <!-- Sidebar -->
            <div class="lg:col-span-1" wire:ignore>
                <x-user.sidebar :user="$user" />
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-12">
                <!-- Header Section -->
                <section class="relative">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-2 h-6 bg-jti-yellow rounded-full"></span>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">Profil Saya</h1>
                    </div>
                    <p class="text-gray-600 text-lg">Kelola informasi profil dan keamanan akun Anda</p>
                </section>

                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-start gap-3"
                        x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <svg class="h-5 w-5 flex-shrink-0 mt-0.5 text-emerald-600" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-700 text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Card Container -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8 lg:p-10">
                    <!-- Tabs -->
                    <div class="mb-8 flex gap-6 border-b border-gray-200 pb-0">
                        <button wire:click="$set('activeTab', 'profile')"
                            class="px-4 py-3 font-semibold transition-all {{ $activeTab === 'profile' ? 'text-jti-yellow border-b-2 border-jti-yellow' : 'text-gray-600 hover:text-gray-900' }}">
                            Informasi Profil
                        </button>
                        <button wire:click="$set('activeTab', 'password')"
                            class="px-4 py-3 font-semibold transition-all {{ $activeTab === 'password' ? 'text-jti-yellow border-b-2 border-jti-yellow' : 'text-gray-600 hover:text-gray-900' }}">
                            Keamanan
                        </button>
                    </div>

                    <!-- Profile Tab -->
                    @if ($activeTab === 'profile')
                        <form wire:submit="updateProfile" class="space-y-6">
                            <!-- Nama -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Nama
                                    Lengkap</label>
                                <input type="text" id="name" wire:model="name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent transition-all"
                                    required>
                                @error('name')
                                    <span class="text-red-600 text-sm mt-1.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email"
                                    class="block text-sm font-semibold text-gray-900 mb-2">Email</label>
                                <input type="email" id="email" wire:model="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent transition-all"
                                    required>
                                @error('email')
                                    <span class="text-red-600 text-sm mt-1.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button type="submit"
                                    class="px-6 py-3 bg-jti-yellow hover:bg-jti-maroon text-white font-semibold rounded-lg transition-all hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Simpan Perubahan</span>
                                    <span wire:loading class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
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
                                    class="block text-sm font-semibold text-gray-900 mb-2">Password Saat Ini</label>
                                <input type="password" id="currentPassword" wire:model="currentPassword"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent transition-all"
                                    required>
                                @error('currentPassword')
                                    <span class="text-red-600 text-sm mt-1.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">Password
                                    Baru</label>
                                <input type="password" id="password" wire:model="password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent transition-all"
                                    required>
                                @error('password')
                                    <span class="text-red-600 text-sm mt-1.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="passwordConfirmation"
                                    class="block text-sm font-semibold text-gray-900 mb-2">Konfirmasi Password</label>
                                <input type="password" id="passwordConfirmation" wire:model="passwordConfirmation"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jti-yellow focus:border-transparent transition-all"
                                    required>
                                @error('passwordConfirmation')
                                    <span class="text-red-600 text-sm mt-1.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button type="submit"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Ubah Password</span>
                                    <span wire:loading class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
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

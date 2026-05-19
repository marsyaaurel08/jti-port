<x-layouts.user :user="$this->user">
    {{-- Alert Berhasil: Lebih halus tanpa bold berlebih --}}
    @if (session('success'))
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-800 flex items-center gap-4"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="flex-shrink-0 w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-icon lucide-check">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-emerald-900">Pembaruan berhasil</p>
                <p class="text-xs opacity-90">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Form Informasi Dasar --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 mb-8">
        <div class="flex items-center gap-4 mb-10">
            <div class="p-3 bg-jti-yellow/10 rounded-xl text-jti-yellow">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-cog-icon lucide-user-cog">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                    <path d="M19.428 15.428a2 2 0 0 0-1.414-3.414l-1.415-1.415a2 2 0 0 0-2.828 -2.828l-1.415-1.415a2 2 0 0 0-3.414 -1.414l-3.777 .999a2,2,0,0,0,-3.777 .999L6,6a2,2,0,0,0,-3.777 .999L3,8a2,2,0,0,0,-3.777 .999L6,6a2,2,0,0,0,-3.777 .999L3,8a2,2,0,0,0,-3.777 .999L6,6a2,2,0,0,0,-3.777 .999L3,8a2,2,0,0,0,-3.777 .999L6,6a2,2,0,0,0,-3.777 .999L3,8a2,2,0,0,
                    <path d="M5.555555555555555e-8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 -8 L8 - eight"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-jti-primary tracking-tight">Informasi dasar</h2>
                <p class="text-sm text-slate-500 font-medium">Kelola informasi publik dan foto profil anda</p>
            </div>
        </div>

        <form wire:submit="updateProfile" class="space-y-10">
            {{-- Bagian Avatar --}}
            <div class="flex flex-col md:flex-row items-center gap-8 p-6 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                <div class="relative">
                    <div class="w-32 h-32 rounded-[2rem] overflow-hidden ring-4 ring-white shadow-xl bg-white">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($user->getFirstMediaUrl('avatar'))
                            <img src="{{ $user->getFirstMediaUrl('avatar') }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-jti-light flex items-center justify-center text-jti-primary text-3xl font-semibold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <label class="absolute -bottom-2 -right-2 p-2.5 bg-jti-yellow text-jti-primary rounded-xl shadow-lg cursor-pointer hover:scale-105 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-camera-icon lucide-camera">
                            <path d="M14.5 2H9.5C6.5 2 4 4.5 4 7.5V16.5C4 19.5 6.5 22 9.5 22H14.5C17.5 22 20 19.5 20 16.5V7.5C20 4.5 17.5 2 14.5 2Z"></path>
                            <circle cx="12" cy="13" r="3"></circle>
                            <path d="M18.364 6L17.364 7L18.364 8"></path>
                        </svg>
                        <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                    </label>
                </div>

                <div class="flex-1 text-center md:text-left">
                    <h4 class="font-semibold text-slate-800 mb-1">Foto profil</h4>
                    <p class="text-xs text-slate-500 max-w-xs leading-relaxed font-medium mb-3">Gunakan foto asli untuk memudahkan verifikasi sertifikat. PNG atau JPG, maksimal 2MB.</p>
                    @if ($user->getFirstMediaUrl('avatar'))
                        <button type="button" wire:click="deleteAvatar"
                            class="text-[11px] font-semibold text-rose-500 hover:text-rose-600 transition-colors">
                            Hapus foto saat ini
                        </button>
                    @endif
                </div>
            </div>

            {{-- Input Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-600 ml-1">Nama lengkap</label>
                    <div class="relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-jti-yellow transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user">
                            <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"></path>
                        </svg>
                        <input type="text" wire:model="name"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-jti-yellow/20 transition-all text-sm font-medium text-slate-800">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-600 ml-1">Alamat email</label>
                    <div class="relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-jti-yellow transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input type="email" wire:model="email"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-jti-yellow/20 transition-all text-sm font-medium text-slate-800">
                    </div>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-sm font-medium text-slate-600 ml-1">Biografi singkat</label>
                    <textarea wire:model="bio" rows="4"
                        class="w-full p-5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-jti-yellow/20 transition-all text-sm font-medium text-slate-800 resize-none"
                        placeholder="Tuliskan sedikit tentang diri anda..."></textarea>
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-50 pt-8">
                <button type="submit" wire:loading.attr="disabled"
                    class="px-8 py-3 bg-jti-primary text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95 flex items-center gap-2">
                    <span wire:loading.remove>Simpan perubahan</span>
                    <span wire:loading><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-loader-2">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                    </svg></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle" wire:loading.remove>
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="16 12 12 8 8 12"></polyline>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    {{-- Form Keamanan --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 relative overflow-hidden">
        <div class="flex items-center gap-4 mb-10">
            <div class="p-3 bg-rose-50 rounded-xl text-rose-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-lock">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-slate-800 tracking-tight">Kata sandi</h2>
                <p class="text-sm text-slate-500 font-medium">Pastikan akun anda menggunakan pengamanan yang kuat</p>
            </div>
        </div>

        <form wire:submit="updatePassword" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="md:col-span-2 space-y-2">
                <label class="text-sm font-medium text-slate-600 ml-1">Kata sandi saat ini</label>
                <input type="password" wire:model="currentPassword"
                    class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-rose-100 transition-all text-sm">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-600 ml-1">Kata sandi baru</label>
                <input type="password" wire:model="password"
                    class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-rose-100 transition-all text-sm">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-600 ml-1">Konfirmasi sandi baru</label>
                <input type="password" wire:model="passwordConfirmation"
                    class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-rose-100 transition-all text-sm">
            </div>

            <div class="md:col-span-2 mt-4 flex flex-col sm:flex-row items-center justify-between bg-slate-50/80 p-6 rounded-2xl gap-4">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <p class="text-[11px] text-slate-500 leading-relaxed font-medium max-w-[280px]">Gunakan minimal 8 karakter dengan kombinasi huruf besar, angka, dan simbol untuk keamanan maksimal.</p>
                </div>
                <button type="submit"
                    class="w-full sm:w-auto px-8 py-3 bg-rose-600 text-white text-sm font-semibold rounded-xl hover:bg-rose-700 transition-all shadow-md">
                    Perbarui keamanan
                </button>
            </div>
        </form>
    </div>
</x-layouts.user>

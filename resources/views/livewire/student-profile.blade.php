<x-layouts.user :user="$this->user">
    {{-- Toast Notifikasi Berhasil (Melayang fixed di pojok kanan bawah, otomatis hilang) --}}
    @if (session('success'))
        <div class="fixed top-24 right-6 bg-green-50 text-green-800 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[300px]"
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 blur-sm">
            <div class="flex-shrink-0 w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-sm">
                <x-lucide-check class="w-4 h-4" />
            </div>
            <div class="pr-2">
                <p class="text-sm font-semibold text-emerald-800">Tindakan berhasil</p>
                <p class="text-xs text-emerald-200/90">{{ session('success') }}</p>
            </div>
            <button type="button" @click="show = false" class="text-emerald-300 hover:text-white ml-auto transition-colors">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    {{-- Form Informasi Dasar --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 mb-8">
        <div class="flex items-center gap-4 mb-10">
            <div class="p-3 bg-jti-yellow/10 rounded-xl text-jti-yellow">
                <x-lucide-user-cog class="w-5 h-5" />
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
                        <x-lucide-camera class="w-5 h-5" />
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
                        <x-lucide-user class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-jti-yellow transition-colors" />
                        <input type="text" wire:model="name"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-jti-yellow/20 transition-all text-sm font-medium text-slate-800">
                    </div>
                    @error('name')
                        <span class="text-xs text-rose-500 ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-600 ml-1">Alamat email</label>
                    <div class="relative group">
                        <x-lucide-mail class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-jti-yellow transition-colors" />
                        <input type="email" wire:model="email"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-jti-yellow/20 transition-all text-sm font-medium text-slate-800">
                    </div>
                    @error('email')
                        <span class="text-xs text-rose-500 ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-sm font-medium text-slate-600 ml-1">Biografi singkat</label>
                    <textarea wire:model="bio" rows="4"
                        class="w-full p-5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-jti-yellow/20 transition-all text-sm font-medium text-slate-800 resize-none"
                        placeholder="Tuliskan sedikit tentang diri anda..."></textarea>
                    @error('bio')
                        <span class="text-xs text-rose-500 ml-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-50 pt-8">
                {{-- FIX LOADING TARGET: Mengunci loading hanya untuk fungsi updateProfile --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="updateProfile"
                    class="px-8 py-3 bg-jti-primary text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-jti-primary/20 transition-all active:scale-95 flex items-center gap-2">
                    <span wire:loading.remove wire:target="updateProfile">Simpan perubahan</span>
                    <span wire:loading wire:target="updateProfile">
                        <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- Form Keamanan (Password) --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 relative overflow-hidden">
        <div class="flex items-center gap-4 mb-10">
            <div class="p-3 bg-rose-50 rounded-xl text-rose-500">
                <x-lucide-shield class="w-5 h-5" />
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-slate-800 tracking-tight">Kata sandi</h2>
                <p class="text-sm text-slate-500 font-medium">Pastikan akun anda menggunakan pengamanan yang kuat</p>
            </div>
        </div>

        <form wire:submit="updatePassword" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Kata Sandi Saat Ini --}}
            <div class="md:col-span-2 space-y-2">
                <label class="text-sm font-medium text-slate-600 ml-1">Kata sandi saat ini</label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" wire:model="currentPassword"
                        class="w-full pl-5 pr-12 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-rose-100 transition-all text-sm">
                    <button type="button" @click="show = !show"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <x-lucide-eye x-show="!show" class="w-5 h-5" />
                        <x-lucide-eye-off x-show="show" class="w-5 h-5" x-cloak />
                    </button>
                </div>
                @error('currentPassword')
                    <span class="text-xs text-rose-500 ml-1 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Kata Sandi Baru --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-600 ml-1">Kata sandi baru</label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" wire:model="password"
                        class="w-full pl-5 pr-12 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-rose-100 transition-all text-sm">
                    <button type="button" @click="show = !show"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <x-lucide-eye x-show="!show" class="w-5 h-5" />
                        <x-lucide-eye-off x-show="show" class="w-5 h-5" x-cloak />
                    </button>
                </div>
                @error('password')
                    <span class="text-xs text-rose-500 ml-1 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Bagian Konfirmasi Sandi Baru di Blade --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-600 ml-1">Konfirmasi sandi baru</label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" wire:model="password_confirmation"
                        class="w-full pl-5 pr-12 py-3.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-rose-100 transition-all text-sm">
                    <button type="button" @click="show = !show"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <x-lucide-eye x-show="!show" class="w-5 h-5" />
                        <x-lucide-eye-off x-show="show" class="w-5 h-5" x-cloak />
                    </button>
                </div>
            </div>

            <div class="md:col-span-2 mt-4 flex flex-col sm:flex-row items-center justify-between bg-slate-50/80 p-6 rounded-2xl gap-4">
                <div class="flex items-start gap-3">
                    <x-lucide-info class="w-5 h-5 text-slate-400 shrink-0" />
                    <p class="text-[11px] text-slate-500 leading-relaxed font-medium max-w-[280px]">Gunakan minimal 8
                        karakter dengan kombinasi huruf besar, angka, dan simbol untuk keamanan maksimal.</p>
                </div>

                {{-- FIX LOADING TARGET: Mengunci loading dan efek spin khusus untuk fungsi updatePassword --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                    class="w-full sm:w-auto px-8 py-3 bg-rose-600 text-white text-sm font-semibold rounded-xl hover:bg-rose-700 transition-all shadow-md flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="updatePassword">Perbarui keamanan</span>
                    <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                        <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        <span>Memproses...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.user>

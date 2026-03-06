<div class="bg-[#F8FAFC] min-h-screen">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-24 py-12 md:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            <div class="lg:col-span-3" wire:ignore>
                <div class="sticky top-26">
                    <x-user.sidebar :user="$this->user" />
                </div>
            </div>

            <div class="lg:col-span-9 space-y-10">

                @if (session('success'))
                    <div class="p-5 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-800 flex items-center gap-4 shadow-sm shadow-emerald-100/50"
                        x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <div
                            class="flex-shrink-0 w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                            <i data-lucide="check" class="w-6 h-6" style="stroke-width: 3px;"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Berhasil Diperbarui</p>
                            <p class="text-xs opacity-80">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 p-8">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <i data-lucide="user-cog" class="w-6 h-6 text-slate-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Informasi Dasar</h2>
                    </div>

                    <form wire:submit="updateProfile" class="space-y-10">
                        <div
                            class="flex flex-col md:flex-row items-center gap-8 p-6 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                            <div class="relative group">
                                <div
                                    class="w-32 h-32 rounded-3xl overflow-hidden ring-4 ring-white shadow-2xl transition-transform duration-500 group-hover:scale-105">
                                    @if ($avatar)
                                        <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @elseif ($user->getFirstMediaUrl('avatar'))
                                        <img src="{{ $user->getFirstMediaUrl('avatar') }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div
                                            class="w-full h-full bg-gradient-to-br from-jti-primary to-jti-maroon flex items-center justify-center text-white text-3xl font-bold">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <label
                                    class="absolute -bottom-2 -right-2 p-2.5 bg-white rounded-xl shadow-lg border border-slate-100 cursor-pointer hover:bg-slate-50 transition-colors text-jti-primary">
                                    <i data-lucide="camera" class="w-5 h-5"></i>
                                    <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                                </label>
                            </div>

                            <div class="flex-1 text-center md:text-left space-y-2">
                                <h4 class="font-bold text-slate-800">Foto Profil</h4>
                                <p class="text-sm text-slate-500 max-w-xs leading-relaxed">Gunakan foto asli untuk
                                    memudahkan verifikasi sertifikat. PNG atau JPG, Maksimal 2MB.</p>
                                @if ($user->getFirstMediaUrl('avatar'))
                                    <button type="button" wire:click="deleteAvatar"
                                        class="text-xs font-bold text-rose-500 hover:text-rose-600 uppercase tracking-widest pt-2">Hapus
                                        Foto</button>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Nama Lengkap</label>
                                <div class="relative group">
                                    <i data-lucide="user"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-jti-primary transition-colors"></i>
                                    <input type="text" wire:model="name"
                                        class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-jti-primary/20 transition-all font-medium text-slate-800 placeholder:text-slate-400">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Alamat Email</label>
                                <div class="relative group">
                                    <i data-lucide="mail"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-jti-primary transition-colors"></i>
                                    <input type="email" wire:model="email"
                                        class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-jti-primary/20 transition-all font-medium text-slate-800">
                                </div>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-slate-700 ml-1">Biografi Singkat</label>
                                <textarea wire:model="bio" rows="4"
                                    class="w-full p-5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-jti-primary/20 transition-all font-medium text-slate-800 resize-none"
                                    placeholder="Tuliskan pengalaman atau minat belajar Anda..."></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6">
                            <button type="submit" wire:loading.attr="disabled"
                                class="group relative px-10 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-jti-primary transition-all duration-300 shadow-xl shadow-slate-900/10 hover:shadow-jti-primary/20 flex items-center gap-3 active:scale-95">
                                <span wire:loading.remove>Simpan Perubahan</span>
                                <span wire:loading><i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i></span>
                                <i data-lucide="arrow-right"
                                    class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                                    wire:loading.remove></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div
                    class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 lg:p-12 overflow-hidden relative">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-rose-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110">
                    </div>

                    <div class="flex items-center gap-4 mb-10 relative">
                        <div class="p-3 bg-rose-50 rounded-xl">
                            <i data-lucide="shield-check" class="w-6 h-6 text-rose-500"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kata Sandi & Keamanan</h2>
                    </div>

                    <form wire:submit="updatePassword" class="grid grid-cols-1 md:grid-cols-2 gap-8 relative">
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Password Saat Ini</label>
                            <input type="password" wire:model="currentPassword"
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-200 transition-all font-medium">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Password Baru</label>
                            <input type="password" wire:model="password"
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-200 transition-all font-medium">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Konfirmasi Password Baru</label>
                            <input type="password" wire:model="passwordConfirmation"
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-200 transition-all font-medium">
                        </div>

                        <div class="md:col-span-2 pt-4 flex items-center justify-between bg-slate-50 p-6 rounded-2xl">
                            <p class="text-xs text-slate-500 max-w-[240px]">Pastikan password mengandung kombinasi
                                huruf besar, angka, dan simbol.</p>
                            <button type="submit"
                                class="px-8 py-4 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 transition-all shadow-lg shadow-rose-200">
                                Perbarui Keamanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

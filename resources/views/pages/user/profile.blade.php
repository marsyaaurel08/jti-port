{{-- resources/views/pages/user/profile.blade.php --}}
<x-user.layout.index :user="$user">
    <div class="space-y-10">

        {{-- Section 1: Header & Photo Profile --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 md:p-12 bg-gradient-to-r from-jti-primary/5 to-transparent flex flex-col md:flex-row items-center gap-8">
                {{-- Avatar Upload with Preview --}}
                <form action="{{ route('user.profile.update.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm" class="relative group">
                    @csrf @method('PATCH')
                    <div class="w-32 h-32 rounded-[2.5rem] bg-jti-primary overflow-hidden shadow-2xl shadow-jti-primary/20 border-4 border-white relative">
                        <img id="avatarPreview"
                             src="{{ $user->getFilamentAvatarUrl() ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=14213d&color=fff' }}"
                             class="w-full h-full object-cover">

                        {{-- Overlay Upload --}}
                        <label for="avatarInput" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </label>
                    </div>
                    <input type="file" name="avatar" id="avatarInput" class="hidden" onchange="previewImage(event)">
                </form>

                <div class="text-center md:text-left space-y-2">
                    <h2 class="text-3xl font-black text-jti-primary tracking-tight">{{ $user->name }}</h2>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs italic">{{ $user->email }}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 pt-2">
                        @foreach($user->roles as $role)
                            <span class="px-3 py-1 bg-jti-orange/10 text-jti-orange text-[10px] font-black uppercase rounded-full ring-1 ring-jti-orange/20">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Form Informasi Dasar --}}
            <form action="{{ route('user.profile.update') }}" method="POST" class="p-8 md:p-12 space-y-8">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Input Nama --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-jti-primary uppercase tracking-[0.2em] ml-1">Nama Lengkap</label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full bg-gray-50 border-none ring-1 ring-gray-200 rounded-2xl px-5 py-4 text-sm font-bold text-jti-primary focus:ring-2 focus:ring-jti-orange transition-all outline-none">
                        </div>
                        @error('name') <p class="text-[10px] text-rose-500 font-bold ml-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Input Email --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-jti-primary uppercase tracking-[0.2em] ml-1">Alamat Email</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full bg-gray-100 border-none ring-1 ring-gray-200 rounded-2xl px-5 py-4 text-sm font-bold text-gray-400 cursor-not-allowed outline-none" readonly>
                        </div>
                        <p class="text-[9px] text-gray-400 italic ml-1">* Email tidak dapat diubah untuk keamanan akun.</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="group flex items-center gap-3 px-10 py-4 bg-jti-primary text-white font-black rounded-2xl hover:bg-jti-maroon transition-all shadow-xl shadow-jti-primary/20 active:scale-95 text-xs uppercase tracking-widest">
                        <span>Simpan Perubahan</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Section 2: Keamanan --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                <h3 class="text-xl font-black text-jti-primary tracking-tight">Keamanan Akun</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-1">Gunakan kombinasi password yang kuat</p>
            </div>

            <form action="{{ route('user.password.update') }}" method="POST" class="p-8 md:p-12 space-y-8">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-jti-primary uppercase tracking-[0.2em] ml-1">Password Saat Ini</label>
                        <input type="password" name="current_password" class="w-full bg-gray-50 border-none ring-1 ring-gray-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-rose-500 transition-all outline-none">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-jti-primary uppercase tracking-[0.2em] ml-1">Password Baru</label>
                        <input type="password" name="password" class="w-full bg-gray-50 border-none ring-1 ring-gray-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-jti-orange transition-all outline-none">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-jti-primary uppercase tracking-[0.2em] ml-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full bg-gray-50 border-none ring-1 ring-gray-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-jti-orange transition-all outline-none">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-4 border-2 border-jti-primary text-jti-primary font-black rounded-2xl hover:bg-jti-primary hover:text-white transition-all active:scale-95 text-xs uppercase tracking-widest">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script untuk Preview Image --}}
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('avatarPreview');
                output.src = reader.result;
                // Opsional: Langsung submit form saat file dipilih
                document.getElementById('avatarForm').submit();
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</x-user.layout.index>

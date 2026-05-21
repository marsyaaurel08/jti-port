<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]

class StudentProfile extends Component
{
    use WithFileUploads;

    public $user;

    public $name = '';

    public $email = '';

    public $bio = '';

    public $phone_number = '';

    public $avatar = null;

    public $currentPassword = '';

    public $password = '';

    public $password_confirmation = '';

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->bio = $this->user->bio ?? '';
        $this->phone_number = $this->user->phone_number ?? '';
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'bio' => ['nullable', 'string', 'max:500'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($this->avatar) {
            $this->user->clearMediaCollection('avatar');
            $this->user->addMedia($this->avatar->getRealPath())
                ->usingName('avatar')
                ->toMediaCollection('avatar');
        }

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'phone_number' => $this->phone_number,
        ]);

        $this->avatar = null;
        session()->flash('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(): void
    {
        // 1. Validasi awal untuk input wajib dan password saat ini
        $this->validate([
            'currentPassword' => ['required', 'current_password'],
            'password' => ['required', Password::defaults()],
        ], [
            'currentPassword.current_password' => 'Password saat ini tidak sesuai.',
        ]);

        // 2. Pengecekan Manual Konfirmasi Password (Aman & Akurat)
        // Kita bandingkan langsung properti $password dengan $password_confirmation
        if ($this->password !== $this->password_confirmation) {
            $this->addError('password', 'Konfirmasi password tidak cocok.');
            return; // Hentikan proses jika tidak sama
        }

        // 3. Jika lolos semua pengecekan, lakukan update ke database
        $this->user->update([
            'password' => bcrypt($this->password), // Tetap menggunakan bcrypt aman
        ]);

        // 4. Reset form dan beri notifikasi sukses
        $this->reset(['currentPassword', 'password', 'password_confirmation']);
        session()->flash('success', 'Password berhasil diubah!');
    }

    public function deleteAvatar(): void
    {
        $this->user->clearMediaCollection('avatar');
        session()->flash('success', 'Foto profil berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.student-profile');
    }
}

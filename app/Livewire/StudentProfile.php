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

    public $passwordConfirmation = '';

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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
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
        $this->validate([
            'currentPassword' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'currentPassword.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $this->user->update([
            'password' => bcrypt($this->password),
        ]);

        $this->reset(['currentPassword', 'password', 'passwordConfirmation']);
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

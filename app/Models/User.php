<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Impersonate, InteractsWithMedia, LogsActivity, Notifiable, TwoFactorAuthenticatable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'bio',
        'role',
        'phone_number',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        $isAdminRole = $this->hasAnyRole(['admin', 'super_admin', 'pengajar']);

        return match ($panel->getId()) {
            'admin' => $isAdminRole,

            'user' => ! $isAdminRole,

            default => false,
        };
    }

    /**
     * Determine if the user can impersonate another user.
     */
    public function canImpersonate(): bool
    {
        // Only super admins and support roles can impersonate
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Determine if the user can be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        // Super admins cannot be impersonated for security
        return ! $this->hasRole('super_admin');
    }

    /**
     * Get the user's avatar URL for Filament.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        // Try to get avatar from media library first
        $avatarMedia = $this->getFirstMediaUrl('avatar');
        if ($avatarMedia) {
            return $avatarMedia;
        }

        // Fallback to avatar_url field
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    /**
     * Register media collections for the user.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Register media conversions for the user.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(300)
            ->height(300);
    }

    /**
     * Check if user has linked social account.
     */
    public function hasSocialAccount(string $provider): bool
    {
        return $this->socialAccounts()->where('provider', $provider)->exists();
    }

    /**
     * Get the activity log options for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['password', 'remember_token'])
            ->setDescriptionForEvent(fn (string $eventName) => "User {$eventName}");
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

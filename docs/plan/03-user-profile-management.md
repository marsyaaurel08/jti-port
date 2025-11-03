# Plan 03: User Profile Management with Filament Breezy

## Overview
Implement comprehensive user profile management functionality using the `jeffgreco13/filament-breezy` package, allowing users to manage their own profiles, change passwords, enable two-factor authentication, and manage personal access tokens.

## Current State
- Filament 4 admin panel operational
- Basic authentication in place
- No user profile management features
- Users cannot update their own information

## Requirements
- User profile page for viewing/editing personal information
- Password change functionality
- Two-factor authentication (2FA) support
- Personal access tokens (API tokens) management
- Profile avatar upload
- Email change with verification
- User preferences management

## Implementation Steps

### Step 1: Install Filament Breezy Package
**Command**:
```bash
composer require jeffgreco13/filament-breezy
```

**Actions**:
1. Add package via composer
2. Verify compatibility with Filament 4.x and Laravel 12

### Step 2: Publish Configuration and Assets
**Commands**:
```bash
# Publish configuration file
php artisan vendor:publish --tag=filament-breezy-config

# Publish views (optional, for customization)
php artisan vendor:publish --tag=filament-breezy-views

# Publish migrations
php artisan vendor:publish --tag=filament-breezy-migrations
```

**File**: `config/filament-breezy.php`

**Actions**:
1. Review configuration options
2. Configure enabled features (profile, 2FA, password, tokens)
3. Set avatar provider and storage settings

### Step 3: Run Migrations
**Command**:
```bash
php artisan migrate
```

**Expected Tables**:
- Updates to `users` table for 2FA columns (if enabled)
- Personal access tokens table (if using Sanctum)

### Step 4: Configure User Model
**File**: `app/Models/User.php`

**Actions**:
1. Add `HasProfilePhoto` trait (if using avatar feature)
2. Add `TwoFactorAuthenticatable` trait (if using 2FA)
3. Add `HasApiTokens` trait (if using personal access tokens)
4. Configure fillable fields

**Code Changes**:
```php
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements HasAvatar
{
    use TwoFactorAuthenticatable;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url', // if using avatars
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ?? null;
    }
}
```

### Step 5: Configure Filament Breezy in Config File
**File**: `config/filament-breezy.php`

**Key Configuration Options**:
```php
return [
    'enable_profile_page' => true,
    'show_profile_page_in_navbar' => true,

    'enable_two_factor' => true,

    'enable_sanctum_tokens' => true,

    'myprofile_slug' => 'my-profile',

    'profile_page_group' => 'Settings',

    'avatars' => [
        'disk' => 'public',
        'directory' => 'avatars',
        'rules' => 'image|max:1024', // 1MB max
    ],

    'password_update' => true,

    'registration_redirect_url' => null,
];
```

### Step 6: Register Breezy Plugin in Admin Panel
**File**: `app/Providers/Filament/AdminPanelProvider.php`

**Actions**:
1. Register Filament Breezy plugin
2. Configure plugin options

**Code Changes**:
```php
use Jeffgreco13\FilamentBreezy\BreezyCore;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... existing configuration
        ->plugin(
            BreezyCore::make()
                ->myProfile(
                    shouldRegisterUserMenu: true,
                    shouldRegisterNavigation: false,
                    navigationGroup: 'Settings',
                    hasAvatars: true,
                    slug: 'my-profile'
                )
                ->enableTwoFactorAuthentication(
                    force: false, // don't force users to enable 2FA
                )
                ->enableSanctumTokens(
                    permissions: ['create', 'view', 'update', 'delete']
                )
        );
}
```

### Step 7: Configure Storage for Avatars
**File**: `config/filesystems.php`

**Actions**:
1. Ensure 'public' disk is configured
2. Create symbolic link for public storage

**Commands**:
```bash
# Create symbolic link
php artisan storage:link

# Verify storage/app/public is linked to public/storage
```

**Verify Configuration**:
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

### Step 8: Create Custom Profile Fields (Optional)
**Location**: Custom form fields for user profile

**Actions**:
1. If you want to add custom fields beyond name/email/password
2. Create a custom profile page class
3. Override the `getFormSchema()` method

**Command**:
```bash
php artisan make:filament-page MyCustomProfile --resource
```

**Example Custom Fields**:
```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

protected function getFormSchema(): array
{
    return [
        TextInput::make('name')
            ->required(),

        TextInput::make('email')
            ->email()
            ->required(),

        TextInput::make('phone')
            ->tel()
            ->nullable(),

        Textarea::make('bio')
            ->rows(3)
            ->nullable(),

        // Avatar upload handled by Breezy automatically
    ];
}
```

### Step 9: Configure Two-Factor Authentication
**Actions**:
1. Ensure 2FA is enabled in config
2. Configure 2FA recovery codes
3. Test 2FA flow

**Configuration**:
```php
// In config/filament-breezy.php
'enable_two_factor' => true,
'two_factor_issuer' => env('APP_NAME'),
```

**Required Package**:
```bash
# If not already installed
composer require pragmarx/google2fa
```

### Step 10: Configure Personal Access Tokens (Sanctum)
**File**: `config/sanctum.php`

**Actions**:
1. Ensure Sanctum is installed (comes with Laravel 12)
2. Configure token expiration and abilities
3. Run Sanctum migrations if not already run

**Commands**:
```bash
# Publish Sanctum config if needed
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Ensure migrations are run
php artisan migrate
```

**Configuration**:
```php
// In config/sanctum.php
'expiration' => null, // tokens don't expire by default

'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),
```

### Step 11: Customize Profile Page Appearance (Optional)
**Location**: Published views in `resources/views/vendor/filament-breezy/`

**Actions**:
1. Customize profile page layout
2. Add additional sections or widgets
3. Modify styling to match brand

**Files to Customize**:
- `livewire/profile/personal-info.blade.php`
- `livewire/profile/update-password.blade.php`
- `livewire/profile/two-factor-authentication.blade.php`
- `livewire/profile/sanctum-tokens.blade.php`

### Step 12: Add Profile Link to User Menu
**Actions**:
1. Breezy automatically adds profile link to user menu
2. Verify it appears when clicking on user avatar in top right
3. Customize menu label if needed

**Access Profile Page**:
- Click user avatar in top right corner
- Select "My Profile" from dropdown
- Or navigate directly to `/admin/my-profile`

### Step 13: Testing

**Test Cases**:
1. **Profile Information Update**:
   - Login as user
   - Navigate to My Profile page
   - Update name
   - Update email (verify email verification if enabled)
   - Upload avatar image
   - Verify changes are saved
   - Verify avatar displays in user menu

2. **Password Change**:
   - Navigate to password section
   - Enter current password
   - Enter new password
   - Confirm new password
   - Verify password is updated
   - Logout and login with new password

3. **Two-Factor Authentication**:
   - Enable 2FA from profile page
   - Scan QR code with authenticator app (e.g., Google Authenticator)
   - Enter 6-digit code to confirm
   - Save recovery codes
   - Logout and login again
   - Verify 2FA code is required
   - Test with incorrect code (should fail)
   - Test with correct code (should succeed)
   - Disable 2FA and verify it's turned off

4. **Personal Access Tokens**:
   - Navigate to API tokens section
   - Create new token with name
   - Copy token value (only shown once)
   - Test API request with token
   - List all tokens
   - Revoke token
   - Verify revoked token doesn't work
   - Test token permissions if configured

5. **Avatar Upload**:
   - Upload profile picture
   - Verify image appears on profile page
   - Verify image appears in user menu
   - Verify image is stored in storage/app/public/avatars
   - Test image size validation (should reject large files)
   - Test file type validation (should reject non-images)
   - Replace avatar with new image
   - Delete avatar

6. **Email Verification** (if enabled):
   - Change email address
   - Verify verification email is sent
   - Click verification link
   - Verify email is updated and verified

**Testing Commands**:
```bash
# Run tests
php artisan test

# Test in Tinker
php artisan tinker

# Test user data
$user = User::find(1);
$user->name;
$user->email;
$user->getFilamentAvatarUrl();
$user->two_factor_enabled;
$user->tokens; // API tokens
```

## Dependencies
- `jeffgreco13/filament-breezy` (main package)
- `pragmarx/google2fa` (for 2FA, may be installed as dependency)
- `laravel/sanctum` (for API tokens, included in Laravel 12)

## Configuration Files
- `config/filament-breezy.php` - Breezy configuration
- `config/sanctum.php` - API token configuration
- `config/filesystems.php` - Avatar storage configuration

## Database Tables
- `users` - Additional columns for 2FA
- `personal_access_tokens` - Sanctum tokens table

## Success Criteria
- [ ] Filament Breezy package installed successfully
- [ ] Configuration file published and configured
- [ ] Profile page accessible from user menu
- [ ] Users can update their profile information
- [ ] Users can change their password
- [ ] Avatar upload functionality works
- [ ] Avatar displays in user menu and profile page
- [ ] Two-factor authentication can be enabled/disabled
- [ ] 2FA works correctly during login
- [ ] Personal access tokens can be created and revoked
- [ ] API requests with tokens are authenticated
- [ ] Email change verification works (if enabled)
- [ ] All validation works correctly
- [ ] Profile page is responsive and user-friendly

## Rollback Plan
If issues occur:
1. Remove Breezy plugin from AdminPanelProvider
2. Rollback migrations if any: `php artisan migrate:rollback`
3. Remove package: `composer remove jeffgreco13/filament-breezy`
4. Remove traits from User model
5. Delete published config and views
6. Clear cache: `php artisan optimize:clear`

## Security Considerations
- Store avatars securely with proper permissions
- Validate file uploads (type, size, content)
- Implement rate limiting on profile updates
- Use secure password hashing (Laravel handles this)
- Protect API tokens (show only once, use HTTPS)
- Validate email changes with verification
- Enforce strong password requirements
- Implement 2FA recovery code protection
- Log profile changes for audit trail (optional)

## Performance Considerations
- Optimize avatar storage (use appropriate image sizes)
- Consider image optimization/compression for avatars
- Cache avatar URLs if needed
- Use eager loading when loading user with tokens
- Set appropriate token expiration

## Documentation References
- Filament Breezy: https://github.com/jeffgreco13/filament-breezy
- Filament Profiles: https://filamentphp.com/docs/4.x/panels/profile-pages
- Laravel Sanctum: https://laravel.com/docs/12.x/sanctum
- Google2FA: https://github.com/antonioribeiro/google2fa

## Estimated Effort
- Installation and configuration: 1-2 hours
- Avatar setup: 30 minutes
- 2FA configuration: 1 hour
- API token setup: 30 minutes
- Testing: 2-3 hours
- Customization (optional): 1-2 hours
- **Total**: 5-9 hours

# Plan 05: Social Login with Filament Socialite

## Overview
Implement social login functionality using the `dusan/filament-socialite` (or alternative) package, allowing users to authenticate using popular social media providers like Google, Facebook, GitHub, Twitter, etc.

## Current State
- Filament 4 admin panel operational
- Email/password authentication enabled
- Registration and login working
- No social authentication

## Requirements
- Support for multiple social login providers (Google, Facebook, GitHub, Twitter, LinkedIn)
- Seamless integration with existing authentication
- Link/unlink social accounts from profile
- Handle new user registration via social login
- Map social profile data to user model
- Display social login buttons on login/register pages

## Implementation Steps

### Step 1: Install Laravel Socialite
**Command**:
```bash
composer require laravel/socialite
```

**Actions**:
1. Install base Laravel Socialite package
2. This is the foundation for social authentication

### Step 2: Install Filament Socialite Plugin
**Command**:
```bash
composer require dusan/filament-socialite
# or alternative package for Filament 4
composer require duckiedev/filament-socialite
```

**Actions**:
1. Research available Filament 4 compatible socialite packages
2. Install the most suitable package
3. Verify compatibility with Filament 4.x

**Note**: Package name may vary. Check for Filament 4 compatible packages:
- `dusan/filament-socialite`
- `duckiedev/filament-socialite`
- Or implement custom solution with base Laravel Socialite

### Step 3: Publish Configuration Files
**Commands**:
```bash
# Publish Socialite config
php artisan vendor:publish --provider="Laravel\Socialite\SocialiteServiceProvider"

# Publish Filament Socialite config (if available)
php artisan vendor:publish --tag=filament-socialite-config

# Publish views (for customization)
php artisan vendor:publish --tag=filament-socialite-views
```

**File**: `config/services.php`

**Actions**:
1. Add social provider credentials
2. Configure callback URLs

### Step 4: Configure Social Providers
**File**: `config/services.php`

**Add Provider Configurations**:
```php
return [
    // ... existing services

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI', env('APP_URL') . '/auth/github/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', env('APP_URL') . '/auth/facebook/callback'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URI', env('APP_URL') . '/auth/twitter/callback'),
    ],

    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('LINKEDIN_REDIRECT_URI', env('APP_URL') . '/auth/linkedin/callback'),
    ],
];
```

### Step 5: Update Environment Variables
**File**: `.env` and `.env.example`

**Add Social Provider Credentials**:
```env
# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# GitHub OAuth
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URI="${APP_URL}/auth/github/callback"

# Facebook OAuth
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"

# Twitter OAuth
TWITTER_CLIENT_ID=
TWITTER_CLIENT_SECRET=
TWITTER_REDIRECT_URI="${APP_URL}/auth/twitter/callback"

# LinkedIn OAuth
LINKEDIN_CLIENT_ID=
LINKEDIN_CLIENT_SECRET=
LINKEDIN_REDIRECT_URI="${APP_URL}/auth/linkedin/callback"
```

### Step 6: Create Database Migration for Social Accounts
**Command**:
```bash
php artisan make:migration create_social_accounts_table
```

**File**: `database/migrations/YYYY_MM_DD_HHMMSS_create_social_accounts_table.php`

**Migration Content**:
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // google, facebook, github, etc.
            $table->string('provider_id'); // Social provider user ID
            $table->string('provider_token', 500)->nullable();
            $table->string('provider_refresh_token', 500)->nullable();
            $table->timestamp('provider_token_expires_at')->nullable();
            $table->text('avatar')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
            $table->index(['user_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
```

**Run Migration**:
```bash
php artisan migrate
```

### Step 7: Create Social Account Model
**Command**:
```bash
php artisan make:model SocialAccount
```

**File**: `app/Models/SocialAccount.php`

**Model Content**:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
        'provider_token_expires_at',
        'avatar',
    ];

    protected $casts = [
        'provider_token_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'provider_token',
        'provider_refresh_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### Step 8: Update User Model
**File**: `app/Models/User.php`

**Add Relationship**:
```php
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    // ... existing code

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get social account for specific provider
     */
    public function getSocialAccount(string $provider): ?SocialAccount
    {
        return $this->socialAccounts()->where('provider', $provider)->first();
    }

    /**
     * Check if user has linked social account
     */
    public function hasSocialAccount(string $provider): bool
    {
        return $this->socialAccounts()->where('provider', $provider)->exists();
    }
}
```

### Step 9: Create Social Authentication Controller
**Command**:
```bash
php artisan make:controller Auth/SocialiteController
```

**File**: `app/Http/Controllers/Auth/SocialiteController.php`

**Controller Content**:
```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Redirect to provider authentication page
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle provider callback
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Unable to authenticate with ' . ucfirst($provider));
        }

        // Find or create social account
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            // Update existing social account token
            $socialAccount->update([
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'avatar' => $socialUser->getAvatar(),
            ]);

            $user = $socialAccount->user;
        } else {
            // Check if user exists by email
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(32)), // Random password
                    'email_verified_at' => now(), // Auto-verify social login
                ]);
            }

            // Create social account
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        // Log in the user
        Auth::login($user, true);

        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }

    /**
     * Unlink social account
     */
    public function unlink(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        $user = Auth::user();

        $socialAccount = $user->socialAccounts()
            ->where('provider', $provider)
            ->first();

        if ($socialAccount) {
            $socialAccount->delete();

            return redirect()->back()
                ->with('success', ucfirst($provider) . ' account unlinked successfully');
        }

        return redirect()->back()
            ->with('error', 'No ' . ucfirst($provider) . ' account linked');
    }

    /**
     * Validate provider
     */
    protected function validateProvider(string $provider): void
    {
        $allowedProviders = ['google', 'github', 'facebook', 'twitter', 'linkedin'];

        if (!in_array($provider, $allowedProviders)) {
            abort(404);
        }
    }
}
```

### Step 10: Register Routes
**File**: `routes/web.php`

**Add Routes**:
```php
use App\Http\Controllers\Auth\SocialiteController;

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

Route::delete('/auth/{provider}/unlink', [SocialiteController::class, 'unlink'])
    ->middleware('auth')
    ->name('socialite.unlink');
```

### Step 11: Configure Filament Plugin (if using plugin)
**File**: `app/Providers/Filament/AdminPanelProvider.php`

**Register Plugin**:
```php
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... existing configuration
        ->plugin(
            FilamentSocialitePlugin::make()
                ->providers([
                    'google' => [
                        'label' => 'Google',
                        'icon' => 'fab-google',
                        'color' => 'danger',
                    ],
                    'github' => [
                        'label' => 'GitHub',
                        'icon' => 'fab-github',
                        'color' => 'gray',
                    ],
                    'facebook' => [
                        'label' => 'Facebook',
                        'icon' => 'fab-facebook',
                        'color' => 'primary',
                    ],
                ])
                ->showDivider(true)
        );
}
```

### Step 12: Customize Login Page (if not using plugin)
**Actions**:
1. Publish Filament login views
2. Add social login buttons

**Command**:
```bash
php artisan vendor:publish --tag=filament-panels-views
```

**File**: `resources/views/vendor/filament-panels/pages/auth/login.blade.php`

**Add Social Buttons**:
```blade
<div class="space-y-2 mt-4">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Or continue with</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('socialite.redirect', 'google') }}"
           class="btn btn-outline">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><!-- Google icon --></svg>
            Google
        </a>

        <a href="{{ route('socialite.redirect', 'github') }}"
           class="btn btn-outline">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><!-- GitHub icon --></svg>
            GitHub
        </a>
    </div>
</div>
```

### Step 13: Add Social Account Management to Profile
**File**: Custom Filament page or use Breezy integration

**Create Social Account Manager Component**:
```php
// In profile page or custom Livewire component
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;

Section::make('Connected Accounts')
    ->schema([
        Placeholder::make('social_accounts')
            ->content(function () {
                $user = auth()->user();
                $html = '<div class="space-y-2">';

                foreach (['google', 'github', 'facebook'] as $provider) {
                    $linked = $user->hasSocialAccount($provider);
                    $status = $linked ? 'Connected' : 'Not connected';
                    $action = $linked
                        ? '<form method="POST" action="'.route('socialite.unlink', $provider).'">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="text-red-600">Unlink</button>
                           </form>'
                        : '<a href="'.route('socialite.redirect', $provider).'" class="text-blue-600">Connect</a>';

                    $html .= "<div class='flex justify-between items-center'>
                        <span>".ucfirst($provider).": {$status}</span>
                        {$action}
                    </div>";
                }

                $html .= '</div>';
                return new \Illuminate\Support\HtmlString($html);
            }),
    ]),
```

### Step 14: Register OAuth Applications with Providers

**Google OAuth**:
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create new project
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `https://yourdomain.com/auth/google/callback`
6. Copy Client ID and Client Secret

**GitHub OAuth**:
1. Go to GitHub Settings > Developer settings > OAuth Apps
2. Create new OAuth App
3. Set callback URL: `https://yourdomain.com/auth/github/callback`
4. Copy Client ID and Client Secret

**Facebook OAuth**:
1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create new app
3. Add Facebook Login product
4. Configure OAuth redirect URIs
5. Copy App ID and App Secret

**Repeat for other providers** (Twitter, LinkedIn, etc.)

### Step 15: Testing

**Test Cases**:
1. **Google Login**:
   - Click "Login with Google" button
   - Authenticate with Google
   - Verify redirected to dashboard
   - Verify user created/logged in
   - Verify social_accounts record created
   - Check user data mapped correctly

2. **GitHub Login**:
   - Click "Login with GitHub" button
   - Authenticate with GitHub
   - Verify successful login
   - Verify GitHub data mapped to user

3. **Existing User Social Link**:
   - Create user with email `test@example.com`
   - Login via social provider with same email
   - Verify social account linked to existing user
   - Verify no duplicate user created

4. **Multiple Social Accounts**:
   - Link Google account
   - Link GitHub account
   - Verify both show as connected in profile
   - Login with either provider
   - Verify logs into same account

5. **Unlink Social Account**:
   - Link social account
   - Navigate to profile
   - Click "Unlink" for provider
   - Verify account unlinked
   - Verify cannot login with that provider anymore
   - Verify can still login with email/password

6. **New User Registration**:
   - Use social login with new email
   - Verify new user created
   - Verify email marked as verified
   - Verify random password assigned
   - Verify can still set password later

7. **Error Handling**:
   - Cancel authentication on provider page
   - Verify redirected with error message
   - Test with invalid credentials
   - Test with revoked app permissions

**Testing Commands**:
```bash
# Run tests
php artisan test

# Test in Tinker
php artisan tinker

# Check social accounts
$user = User::find(1);
$user->socialAccounts;
$user->hasSocialAccount('google');

# Check social account
$social = SocialAccount::where('provider', 'google')->first();
$social->user;
```

## Dependencies
- `laravel/socialite` (base package)
- `dusan/filament-socialite` or `duckiedev/filament-socialite` (Filament integration)
- Provider-specific packages (optional):
  - `socialiteproviders/microsoft`
  - `socialiteproviders/twitter-oauth-2`

## Configuration Files
- `config/services.php` - Provider credentials
- `config/filament-socialite.php` - Plugin configuration (if using plugin)

## Database Tables
- `social_accounts` - Stores social provider connections

## Routes
- `/auth/{provider}/redirect` - Initiate OAuth flow
- `/auth/{provider}/callback` - Handle OAuth callback
- `/auth/{provider}/unlink` - Unlink social account

## Success Criteria
- [ ] Laravel Socialite installed successfully
- [ ] Filament Socialite plugin installed and configured
- [ ] OAuth applications registered with all providers
- [ ] Environment variables configured
- [ ] Database migration run and table created
- [ ] Social login buttons appear on login page
- [ ] Users can login with Google
- [ ] Users can login with GitHub
- [ ] Users can login with Facebook (if configured)
- [ ] New users are created automatically
- [ ] Existing users can link social accounts
- [ ] Social accounts can be unlinked from profile
- [ ] Multiple social accounts can be linked to one user
- [ ] Profile shows connected social accounts
- [ ] Error handling works correctly
- [ ] Social avatars are saved and displayed

## Rollback Plan
If issues occur:
1. Remove plugin from AdminPanelProvider
2. Remove social login buttons from login page
3. Drop social_accounts table: `php artisan migrate:rollback`
4. Remove routes from web.php
5. Remove controller and model files
6. Remove package: `composer remove laravel/socialite dusan/filament-socialite`
7. Clear cache: `php artisan optimize:clear`

## Security Considerations
- Store OAuth tokens securely (encrypted in database)
- Use HTTPS for all OAuth callbacks
- Validate provider in all methods
- Verify email uniqueness when creating users
- Don't expose OAuth tokens in API responses
- Set appropriate token expiration
- Implement rate limiting on OAuth routes
- Validate OAuth state parameter
- Handle token refresh securely
- Auto-verify emails from trusted providers only
- Consider requiring password setup for social login users

## Privacy Considerations
- Request minimal permissions from providers
- Clearly communicate what data is collected
- Provide option to unlink social accounts
- Don't share user data with providers without consent
- Comply with provider terms of service
- Display provider privacy policies

## Documentation References
- Laravel Socialite: https://laravel.com/docs/12.x/socialite
- Filament Socialite: Check package repository for specific docs
- Google OAuth: https://developers.google.com/identity/protocols/oauth2
- GitHub OAuth: https://docs.github.com/en/developers/apps/building-oauth-apps
- Facebook Login: https://developers.facebook.com/docs/facebook-login

## Estimated Effort
- Installation and configuration: 1-2 hours
- OAuth app registration: 1-2 hours
- Controller and model setup: 1-2 hours
- UI customization: 1-2 hours
- Profile integration: 1 hour
- Testing: 2-3 hours
- **Total**: 7-12 hours

## Notes
- Package availability for Filament 4 may vary
- May need custom implementation if no compatible plugin exists
- Each provider has different requirements and data formats
- Some providers require app review before production use
- Consider implementing package: `socialiteproviders/manager` for additional providers

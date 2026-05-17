<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard as PagesDashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\{Authenticate, AuthenticateSession, DisableBladeIconComponents, DispatchServingFilamentEvent};
use Filament\Navigation\NavigationGroup;
use Filament\{Panel, PanelProvider};
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\{AddQueuedCookiesToResponse, EncryptCookies};
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('staff')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName('jti-port')
            ->brandLogo(asset('img/logo-jti.png'))
            ->favicon(asset('img/logo-jti.png'))
            ->brandLogoHeight('3rem')
            ->passwordReset()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                PagesDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),

                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        navigationGroup: 'Settings',
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->passwordUpdateRules(
                        rules: [Password::default()->min(8)->mixedCase()->numbers()->symbols()],
                        requiresCurrentPassword: true,
                    )
                    ->enableTwoFactorAuthentication(
                        force: false,
                    )
                    ->enableSanctumTokens(
                        permissions: ['create', 'view', 'update', 'delete']
                    ),
                // FilamentSocialitePlugin::make()
                //     ->providers([
                //         Provider::make('google')
                //             ->label('Google')
                //             ->icon('fab-google')
                //             ->color('danger'),
                //         // Provider::make('github')
                //         //     ->label('GitHub')
                //         //     ->icon('fab-github')
                //         //     ->color('gray'),
                //         // Provider::make('facebook')
                //         //     ->label('Facebook')
                //         //     ->icon('fab-facebook')
                //         //     ->color('primary'),
                //     ])
                //     ->showDivider(true),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->spa()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Content'),
                NavigationGroup::make()
                    ->label('User Management'),
                NavigationGroup::make()
                    ->label('Filament Shield'),
            ])
            ->globalSearch(false)
            ->databaseNotifications();
    }
}

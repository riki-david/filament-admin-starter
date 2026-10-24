<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Hammadzafar05\MobileBottomNav\MobileBottomNav;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use lockscreen\FilamentLockscreen\Lockscreen;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Swis\Filament\Backgrounds\ImageProviders\Triangles;
use Saade\FilamentFacehash\FacehashPlugin;
use Saade\FilamentFacehash\FacehashProvider;
use MatondoJK\FilamentAvatarPicker\Components\AvatarPicker;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->favicon(fn() => ($favicon = appSettings()->favicon_url) ? Storage::disk('public')->url($favicon) : asset('images/favicon.png'))
            ->brandName(fn() => ($appName = appSettings()->app_name) ? $appName : 'App Name')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('User Management')
                    ->navigationSort(12),
                Lockscreen::make()
                    ->enablePlugin()
                    ->enableIdleTimeout()
                    ->enableRateLimit(),
                MobileBottomNav::make(),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        hasAvatars: true,
                    )
                    ->enableTwoFactorAuthentication(
                        force: false,
                    )
                    ->enableSanctumTokens(
                        permissions: ['create', 'view', 'update', 'delete']
                    )
                    ->enableBrowserSessions(condition: true)
                    ->enablePasskeys(
                        relyingPartyName: config('app.name'),
                        relyingPartyId: config('app.url'),
                        relyingPartyIcon: asset('images/logo.webp'),
                        scopeToPanel: true,
                    )
                    ->avatarUploadComponent(fn() => AvatarPicker::make('avatar_url')->label('Avatar')),
                FacehashPlugin::make(),
                FilamentBackgroundsPlugin::make()
                    // ->showAttribution(false)
                    // ->imageProvider(MyImages::make()->directory('images/backgrounds'))
                    ->imageProvider(Triangles::make()),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->navigationGroup('Settings')
                    ->navigationSort(94)
                    ->authorize(fn(): bool => Auth::user()?->can('View:Backups') ?? false),
            ])
            ->defaultAvatarProvider(FacehashProvider::class)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}

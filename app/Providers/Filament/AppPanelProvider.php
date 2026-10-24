<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Tenancy\EditTenantProfile;
use App\Filament\App\Pages\Tenancy\RegisterTenant;
use App\Models\Tenant;
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
use Hammadzafar05\MobileBottomNav\MobileBottomNav;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use lockscreen\FilamentLockscreen\Lockscreen;
use MatondoJK\FilamentAvatarPicker\Components\AvatarPicker;
use Saade\FilamentFacehash\FacehashPlugin;
use Saade\FilamentFacehash\FacehashProvider;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\Triangles;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->tenant(Tenant::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterTenant::class)
            ->tenantProfile(EditTenantProfile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->favicon(fn() => ($favicon = appSettings()->favicon_url) ? Storage::disk('public')->url($favicon) : asset('images/favicon.png'))
            ->brandName(fn() => ($appName = appSettings()->app_name) ? $appName : 'App Name')
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
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
            ])
            ->defaultAvatarProvider(FacehashProvider::class)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}

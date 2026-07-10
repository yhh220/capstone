<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopProductsChart;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\LogoutAdminGuardOnly;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->login(Login::class)
            // Without this, Filament links carry no wire:navigate attribute at all —
            // every sidebar click was a full hard page reload (re-running the whole
            // Laravel + Filament bootstrap), which is what made navigation feel slow
            // and is why a wire:navigate-based loading overlay never fired.
            ->spa()
            // Profile page lets admins manage their account + set up 2FA.
            // Custom subclass adds a confirmation step before the post-password-change logout.
            ->profile(EditProfile::class)
            // Optional app (TOTP) two-factor auth with recovery codes. Admins
            // opt in from their profile; not forced, so no one gets locked out.
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
            ])

            // ── Branding ──────────────────────────────────────────────
            ->brandName('Win Win Car Audio')
            ->brandLogo(asset('images/logo/logo-dark.svg'))    // light mode: dark logo (dark text on white)
            ->darkModeBrandLogo(asset('images/logo/logo-light.svg')) // dark mode: light logo (light text on dark)
            ->brandLogoHeight('2rem')
            // Responsive admin panel CSS overrides (charts, tables, forms, modals).
            ->viteTheme('resources/css/admin.css')
            // Favicons/meta icons come from the shared `partials.favicons` injected
            // into the admin <head> via PanelsRenderHook::HEAD_END (below), so the
            // panel matches the public site exactly — one source of truth.

            // ── Theme ─────────────────────────────────────────────────
            ->darkMode(true)
            ->font('DM Sans')
            ->colors([
                // Brand red ramp anchored so shade 600 = #C8413D (the shade Filament
                // uses for filled primary buttons), matching the public site. Using an
                // explicit palette because Color::hex() only keeps the hue and forces
                // its own lightness ramp, which washes the brand red out to salmon.
                'primary' => [
                    50 => '#fdf3f2',
                    100 => '#fbe4e3',
                    200 => '#f7cdcb',
                    300 => '#efa9a5',
                    400 => '#e47b76',
                    500 => '#d6534d',
                    600 => '#c8413d',
                    700 => '#a4302d',
                    800 => '#882a28',
                    900 => '#722827',
                    950 => '#3e110f',
                ],
                'danger' => [
                    50 => '#fdf3f2',
                    100 => '#fbe4e3',
                    200 => '#f7cdcb',
                    300 => '#efa9a5',
                    400 => '#e47b76',
                    500 => '#d6534d',
                    600 => '#c8413d',
                    700 => '#a4302d',
                    800 => '#882a28',
                    900 => '#722827',
                    950 => '#3e110f',
                ],
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'gray' => Color::Zinc,
            ])

            // ── Notifications ─────────────────────────────────────────
            // Bell icon + history panel in the topbar, backed by the `notifications`
            // table. The product/order import & export actions run with a forced
            // 'sync' job connection (no queue worker needed), so Filament's own
            // internals would only show an on-screen toast for them — the
            // NotifiesImportExportCompletion trait additionally persists that same
            // result to the acting user's bell icon (see app/Filament/Concerns).
            ->databaseNotifications()

            // ── Render Hooks ──────────────────────────────────────────
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => view('filament.scroll-to-top'),
            )
            // Top progress bar + dimmed-content state while a page navigates,
            // so switching tabs shows a loading transition instead of a blank flash.
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => view('filament.nav-loading'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('partials.favicons'),
            )

            // ── Navigation ────────────────────────────────────────────
            ->sidebarFullyCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Sales'),
                NavigationGroup::make('Store Products'),
                NavigationGroup::make('Customer Interactions'),
                NavigationGroup::make('FAQs'),
                NavigationGroup::make('System Settings')
                    ->collapsed(),
            ])

            // ── Resources / Pages / Widgets ───────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                StatsOverview::class,
                RevenueChart::class,
                TopProductsChart::class,
                RecentActivityWidget::class,
            ])

            // ── Middleware ────────────────────────────────────────────
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
                LogoutAdminGuardOnly::class,
                AdminMiddleware::class,
            ]);
    }
}

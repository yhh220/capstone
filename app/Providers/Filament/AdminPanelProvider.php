<?php

namespace App\Providers\Filament;

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
            ->login(\App\Filament\Pages\Auth\Login::class)

            // ── Branding ──────────────────────────────────────────────
            ->brandName('Win Win Car Audio')
            ->brandLogo(asset('images/logo/logo-dark.svg'))    // light mode: dark logo (dark text on white)
            ->darkModeBrandLogo(asset('images/logo/logo-light.svg')) // dark mode: light logo (white text on dark)
            ->brandLogoHeight('2rem')
            // Favicons/meta icons come from the shared `partials.favicons` injected
            // into the admin <head> via PanelsRenderHook::HEAD_END (below), so the
            // panel matches the public site exactly — one source of truth.

            // ── Theme ─────────────────────────────────────────────────
            ->defaultThemeMode(\Filament\Enums\ThemeMode::System)
            ->darkMode(true)
            ->font('DM Sans')
            ->colors([
                // Brand red ramp anchored so shade 600 = #C8413D (the shade Filament
                // uses for filled primary buttons), matching the public site. Using an
                // explicit palette because Color::hex() only keeps the hue and forces
                // its own lightness ramp, which washes the brand red out to salmon.
                'primary' => [
                    50  => '#fdf3f2',
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
                'danger'  => [
                    50  => '#fdf3f2',
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
                'info'    => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'gray'    => Color::Zinc,
            ])

            // ── Render Hooks ──────────────────────────────────────────
            // (No custom theme toggle — Filament's native theme switcher in the
            // user menu is the single source of truth. A second copy in the topbar
            // kept its own state and desynced from it.)
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => view('filament.scroll-to-top'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('partials.favicons'),
            )

            // ── Navigation ────────────────────────────────────────────
            ->sidebarFullyCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Shop'),
                NavigationGroup::make('Store Products'),
                NavigationGroup::make('Services & Bookings'),
                NavigationGroup::make('Customer Interactions'),
                NavigationGroup::make('System Settings')
                    ->collapsed(),
                NavigationGroup::make('System')
                    ->collapsed(),
            ])

            // ── Resources / Pages / Widgets ───────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\RevenueChart::class,
                \App\Filament\Widgets\TopProductsChart::class,
                \App\Filament\Widgets\RecentActivityWidget::class,
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
                \App\Http\Middleware\LogoutAdminGuardOnly::class,
            ]);
    }
}

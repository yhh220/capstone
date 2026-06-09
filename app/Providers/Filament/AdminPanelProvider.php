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
            ->favicon(asset('winwin-favicon.svg') . '?v=20260609')

            // ── Theme ─────────────────────────────────────────────────
            ->defaultThemeMode(\Filament\Enums\ThemeMode::System)
            ->darkMode(true)
            ->font('DM Sans')
            ->colors([
                'primary' => Color::Rose,
                'danger'  => Color::Rose,
                'info'    => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'gray'    => Color::Zinc,
            ])

            // ── Render Hooks ──────────────────────────────────────────
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn () => view('filament.theme-toggle'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => view('filament.scroll-to-top'),
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

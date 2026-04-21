<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
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
            ->login(\App\Filament\Pages\Auth\Login::class)

            // ── Branding ──────────────────────────────────────────────
            ->brandName('Win Win Car Audio')
            ->brandLogo(asset('images/logo/logo-light.svg'))
            ->darkModeBrandLogo(asset('images/logo/logo-dark.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/logo/logo-light.svg'))

            // ── Theme ─────────────────────────────────────────────────
            ->defaultThemeMode(\Filament\Enums\ThemeMode::Dark)
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
                PanelsRenderHook::SIMPLE_LAYOUT_START,
                fn () => view('filament.theme-toggle'),
            )

            // ── Navigation ────────────────────────────────────────────
            ->sidebarFullyCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Shop')
                    ->icon('heroicon-o-shopping-bag'),
                NavigationGroup::make('Store Products')
                    ->icon('heroicon-o-rectangle-stack'),
                NavigationGroup::make('Services & Bookings')
                    ->icon('heroicon-o-wrench-screwdriver'),
                NavigationGroup::make('Customer Interactions')
                    ->icon('heroicon-o-chat-bubble-left-right'),
                NavigationGroup::make('System Settings')
                    ->icon('heroicon-o-users')
                    ->collapsed(),
                NavigationGroup::make('System')
                    ->icon('heroicon-o-cog-6-tooth')
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
            ]);
    }
}

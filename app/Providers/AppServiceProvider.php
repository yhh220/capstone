<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            fn (): string => \Illuminate\Support\Facades\Blade::render('
                <button type="button" x-data x-on:click="$store.theme.mode === \'dark\' ? $store.theme.mode = \'light\' : $store.theme.mode = \'dark\'" class="flex items-center justify-center w-9 h-9 ml-2 rounded-full hover:bg-gray-500/10 focus:outline-none focus:ring-1 focus:ring-primary-500 text-gray-500 dark:text-gray-400">
                    <svg x-show="$store.theme.mode === \'dark\'" style="display:none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg x-show="$store.theme.mode !== \'dark\'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>
            ')
        );
    }
}

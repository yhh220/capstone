@if (filament()->hasDarkMode() && ! filament()->hasDarkModeForced())
    <div class="me-2">
        <x-filament-panels::theme-switcher />
    </div>
@endif

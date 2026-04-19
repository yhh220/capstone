<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value from the settings table (cached for 1 hour).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return cache()->remember("setting_{$key}", 3600, fn() =>
            \App\Models\Setting::find($key)?->value ?? $default
        );
    }
}

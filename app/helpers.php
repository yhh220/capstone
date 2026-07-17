<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get a setting value from the settings table (cached for 1 hour).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }
}

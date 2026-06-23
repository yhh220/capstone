<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have a
    | conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
    ],

    'store' => [
        'name' => env('STORE_NAME', 'Win Win Car Audio Auto Accessories'),
        // Brand name used in <title> / OG tags — matches the admin panel and webmanifest
        'seo_name' => env('STORE_SEO_NAME', 'Win Win Car Audio'),
        'short_name' => env('STORE_SHORT_NAME', 'Win Win'),
        'tagline' => env('STORE_TAGLINE', 'Car Audio'),
        'phone_display' => env('STORE_PHONE_DISPLAY', '016-9150917'),
        'phone_raw' => env('STORE_PHONE_RAW', '60169150917'),
        'email' => env('STORE_EMAIL', 'winwincaraudio@gmail.com'),
        'facebook_url' => env('STORE_FACEBOOK_URL', 'https://www.facebook.com/winwincaraudio/'),
        'address' => env('STORE_ADDRESS', 'NO. 22, GROUND FLOOR, JALAN DINAR C U3/C, TAMAN SUBANG PERDANA, SEKSYEN U3., Shah Alam, Malaysia, 40150'),
        // Free-text address search on Google Maps re-geocodes per viewer and isn't
        // deterministic — different visitors can land on different pins for the same
        // query string. Map links use this verified lat/lng instead so everyone lands
        // on the exact same spot (matches the embedded store-map pin on the contact page).
        'lat' => (float) env('STORE_LAT', 3.1491),
        'lng' => (float) env('STORE_LNG', 101.5465),
        'hours' => env('STORE_HOURS'),
    ],

    // ── Social login (OAuth) ──────────────────────────────────────────────
    // A provider is "enabled" only when both its id and secret are set, so the
    // login buttons appear automatically once you add the keys. The redirect URI
    // is set per-request in SocialAuthController via route(), so it always matches
    // the current host (register that exact URL in the provider's console).
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
        // Refresh token for sending mail as winwincaraudio85@gmail.com via the
        // Gmail API — obtained once via /gmail-send/connect. Separate from the
        // login flow above; same OAuth client, different scope.
        'gmail_send_refresh_token' => env('GMAIL_SEND_REFRESH_TOKEN'),
    ],

    'microsoft' => [
        'client_id'     => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect'      => env('MICROSOFT_REDIRECT_URI'),
        'tenant'        => env('MICROSOFT_TENANT', 'common'),
    ],

];

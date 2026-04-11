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

    'store' => [
        'name' => env('STORE_NAME', 'WIN WIN CAR AUDIO AUTO ACCESSORIES'),
        'short_name' => env('STORE_SHORT_NAME', 'WIN WIN'),
        'tagline' => env('STORE_TAGLINE', 'CAR AUDIO'),
        'phone_display' => env('STORE_PHONE_DISPLAY', '016-9150917'),
        'phone_raw' => env('STORE_PHONE_RAW', '60169150917'),
        'email' => env('STORE_EMAIL', 'winwincaraudio@gmail.com'),
        'facebook_url' => env('STORE_FACEBOOK_URL', 'https://www.facebook.com/winwincaraudio/'),
        'address' => env('STORE_ADDRESS', 'NO. 22, GROUND FLOOR, JALAN DINAR C U3/C, TAMAN SUBANG PERDANA, SEKSYEN U3., Shah Alam, Malaysia, 40150'),
        'hours' => env('STORE_HOURS'),
    ],

];

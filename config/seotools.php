<?php

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),
    'meta' => [
        'defaults' => [
            'title'       => config('services.store.name', 'Win Win Car Studio'),
            'titleBefore' => false,
            'description' => 'Browse car audio, tint, and accessories online. Visit our showroom or chat on WhatsApp for expert advice.',
            'separator'   => ' | ',
            'keywords'    => ['car audio', 'car accessories', 'window tint', 'car studio', 'Kuala Lumpur'],
            'canonical'   => 'full',
            'robots'      => 'index, follow',
        ],
        'webmaster_tags' => [
            'google'    => env('GOOGLE_SITE_VERIFICATION'),
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],
        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        'defaults' => [
            'title'       => config('services.store.name', 'Win Win Car Studio'),
            'description' => 'Browse car audio, tint, and accessories online. Visit our showroom or chat on WhatsApp for expert advice.',
            'url'         => null,
            'type'        => 'website',
            'site_name'   => config('services.store.name', 'Win Win Car Studio'),
            'images'      => [],
        ],
    ],
    'twitter' => [
        'defaults' => [
            'card' => 'summary_large_image',
        ],
    ],
    'json-ld' => [
        'defaults' => [
            'title'       => config('services.store.name', 'Win Win Car Studio'),
            'description' => 'Browse car audio, tint, and accessories online. Visit our showroom or chat on WhatsApp for expert advice.',
            'url'         => null,
            'type'        => 'AutoPartsStore',
            'images'      => [],
        ],
    ],
];

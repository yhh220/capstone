<?php

namespace App\Support;

/**
 * Small helper around social-login providers. A provider is only "enabled" when
 * both its client id and secret are configured, so the UI buttons and the OAuth
 * routes light up automatically the moment the keys are added to .env.
 */
class SocialLogin
{
    /** Supported providers → display label. (Apple intentionally omitted for now.) */
    public const PROVIDERS = [
        'google'    => 'Google',
        'microsoft' => 'Microsoft',
    ];

    public static function isEnabled(string $provider): bool
    {
        if (! array_key_exists($provider, self::PROVIDERS)) {
            return false;
        }

        return filled(config("services.{$provider}.client_id"))
            && filled(config("services.{$provider}.client_secret"));
    }

    /** ['google' => 'Google', ...] for only the configured providers. */
    public static function enabled(): array
    {
        return array_filter(
            self::PROVIDERS,
            fn (string $key) => self::isEnabled($key),
            ARRAY_FILTER_USE_KEY,
        );
    }

    public static function anyEnabled(): bool
    {
        return self::enabled() !== [];
    }
}

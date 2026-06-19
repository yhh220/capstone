<?php

namespace App\Support;

/**
 * A request-scoped trail of notable events ("breadcrumbs"). Registered as a
 * singleton in AppServiceProvider, so it lives for one request. When an error is
 * logged, the ObservabilityProcessor attaches this trail to the log so you can see
 * what led up to the failure (Sentry-style).
 */
class Breadcrumbs
{
    /** @var array<int, array{ts:string, category:string, message:string, data:array}> */
    private array $crumbs = [];

    private const MAX = 50;

    public function add(string $category, string $message, array $data = []): void
    {
        $this->crumbs[] = [
            'ts'       => now()->format('H:i:s.v'),
            'category' => $category,
            'message'  => $message,
            'data'     => $data,
        ];

        // Ring buffer — keep only the most recent MAX entries.
        if (count($this->crumbs) > self::MAX) {
            $this->crumbs = array_slice($this->crumbs, -self::MAX);
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return $this->crumbs;
    }

    /** Convenience: add a breadcrumb without resolving the singleton by hand. */
    public static function push(string $category, string $message, array $data = []): void
    {
        app(self::class)->add($category, $message, $data);
    }
}

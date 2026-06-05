<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Enable XSS filter in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser feature access
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // HSTS — force HTTPS for a year. Sent only over secure connections so
        // local HTTP development is never locked into HTTPS.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content-Security-Policy for the public storefront. Skipped on /admin
        // so Filament's own asset/script/worker pipeline keeps working.
        // Permissive on script/style (the site uses inline scripts + the Tailwind
        // CDN), but the high-value directives — object-src 'none' (blocks SVG/
        // plugin XSS), frame-ancestors, base-uri, form-action — are enforced.
        if (! $request->is('admin', 'admin/*')) {
            // In local dev, Vite serves assets from its own dev server.
            // Read the hot file to get the exact origin, then allow it in CSP.
            $viteOrigins = '';
            if (app()->environment('local')) {
                $hotFile = public_path('hot');
                if (file_exists($hotFile)) {
                    $viteUrl = rtrim(file_get_contents($hotFile));
                    $wsUrl   = str_replace('http://', 'ws://', $viteUrl);
                    $viteOrigins = " {$viteUrl} {$wsUrl}";
                }
            }

            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://unpkg.com{$viteOrigins}",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com{$viteOrigins}",
                "font-src 'self' https://fonts.gstatic.com data:",
                "img-src 'self' data: blob: https:",
                "connect-src 'self' https://unpkg.com blob:{$viteOrigins}",
                "worker-src 'self' blob:",
                "child-src 'self' blob:",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "form-action 'self'",
                "object-src 'none'",
            ]);
            $response->headers->set('Content-Security-Policy', $csp);
        }

        // Remove server fingerprinting
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}

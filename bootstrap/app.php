<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AssignTraceId;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
        // Render (and most PaaS hosts) terminate TLS at the edge and forward
        // plain HTTP to the container, with the original scheme only in
        // X-Forwarded-Proto. Without trusting that header, Laravel generates
        // http:// asset/URL links on an https:// page, which browsers block
        // as mixed content (this is why CSS/JS silently failed to load).
        //
        // TRUSTED_PROXIES narrows this to specific proxy IPs/CIDRs on hosts
        // that publish them; '*' stays the default because Render doesn't.
        // X-Forwarded-Host is deliberately NOT trusted — the app's host comes
        // from the real Host header, so a spoofed forwarded host can't poison
        // generated URLs (password-reset links, signed URLs). Forwarded-For is
        // still needed: the per-IP rate limits and login lockouts key on it.
        $middleware->trustProxies(
            at: env('TRUSTED_PROXIES', '*'),
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_PORT,
        );
        // Runs first so every log line in the request carries a trace id.
        $middleware->web(prepend: [
            AssignTraceId::class,
        ]);
        $middleware->web(append: [
            SetLocale::class,
            SecurityHeaders::class,
        ]);
        // The theme cookie is written by client JS (plaintext) and read in the
        // layout to render the right <html> class — exclude it from Laravel's
        // cookie encryption so the server can read it (prevents a light-mode
        // flash when switching language via Livewire.navigate).
        $middleware->encryptCookies(except: ['app_theme']);
        // Stripe posts webhooks server-to-server with no session/CSRF token;
        // the endpoint authenticates via the Stripe-Signature header instead.
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            AuthorizationException $e,
            Request $request
        ) {
            if ($request->is('admin*') && auth()->check()) {
                return redirect()->route('unauthorized');
            }
        });
    })->create();

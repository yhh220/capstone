<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AssignTraceId;
use App\Http\Middleware\RedirectToNewHost;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // GLOBAL, first middleware of all: on the old redirect-stub deployment
        // (REDIRECT_TO set) this short-circuits EVERY request — storefront,
        // /admin, /stripe/webhook, anything — into a ~0.5KB redirect to the new
        // host before any group middleware, session, locale, or DB is touched,
        // so the printed-QR domain keeps working with negligible bandwidth. On
        // the real site (REDIRECT_TO unset) it is a no-op that returns instantly.
        $middleware->prepend(RedirectToNewHost::class);

        // Runs first within the web group so every log line carries a trace id.
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

        // A signed-in admin hitting a record that no longer exists (usually the
        // "View order" button of an alert email whose order was later deleted)
        // gets a friendly explanation instead of a bare 404. Guests still go
        // through the login redirect first, and the status stays 404 so
        // monitoring and tests keep seeing the truth.
        $exceptions->render(function (
            NotFoundHttpException $e,
            Request $request
        ) {
            if ($request->is('admin/*') && auth('admin')->check()) {
                return response()->view('errors.admin-404', [], 404);
            }
        });
    })->create();

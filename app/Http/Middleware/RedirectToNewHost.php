<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Turns a deployment into a lightweight redirect-only "stub" when REDIRECT_TO is
 * set. This is how the OLD Render service (winwincaraudio.onrender.com, whose URL
 * is printed on physical QR codes that can no longer be changed) keeps working
 * after the real site moved to a new account/URL: instead of booting the full
 * app and shipping megabytes of video/3D, it answers every request with a tiny
 * ~0.5KB redirect to the new host, preserving the path and query string.
 *
 * Set REDIRECT_TO on the old service only; leave it unset on the real site, where
 * this middleware is a no-op. Runs first in the stack (before locale, security
 * headers, sessions) so a redirected request touches nothing else — no database,
 * no rendering, negligible bandwidth.
 */
class RedirectToNewHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $target = config('app.redirect_to');

        if (blank($target)) {
            return $next($request);
        }

        $target = rtrim($target, '/');

        // Preserve the path and query so a link like /products?x=1 lands on the
        // same page on the new host, not just its homepage.
        $path = $request->getRequestUri(); // includes leading slash + query string

        // 302 (temporary), not 301: browsers cache 301s hard, so if the stub is
        // ever repurposed a permanent redirect would be sticky and hard to undo.
        return redirect()->away($target.$path, 302);
    }
}

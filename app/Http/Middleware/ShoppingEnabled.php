<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShoppingEnabled
{
    /**
     * Only allow access to shopping routes when online shopping is enabled.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (setting('ONLINE_SHOPPING_ENABLED') !== 'true') {
            return redirect()->route('home')->with('error', __('Online shopping is coming soon! Stay tuned.'));
        }

        return $next($request);
    }
}

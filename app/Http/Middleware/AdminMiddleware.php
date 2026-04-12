<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Only allow authenticated users with the 'admin' role to pass through.
     * Unauthorized users are redirected to the admin login page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            // Logout any non-admin user that somehow reached here
            if (Auth::check()) {
                Auth::logout();
            }

            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Unauthorized access. Admin credentials required.');
        }

        return $next($request);
    }
}

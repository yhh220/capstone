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
        // Within the Filament panel the active guard is 'admin', not the default 'web' guard.
        $user = Auth::guard('admin')->user() ?? Auth::user();

        if (! $user || (! $user->isAdmin() && ! $user->isStaff())) {
            if ($user) {
                Auth::guard('admin')->logout();
            }

            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Unauthorized access. Please sign in with an authorised account.');
        }

        return $next($request);
    }
}

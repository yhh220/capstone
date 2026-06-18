<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogoutAdminGuardOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->is('admin/logout')) {
            Auth::guard('admin')->logout();

            // Drop the per-guard auth hashes that AuthenticateSession stores and
            // give the session a fresh id. Without this the session keeps stale
            // auth state, so the FIRST login after logging out gets bounced back
            // to the login page (the "have to enter credentials twice" bug).
            // regenerate() keeps the rest of the session (e.g. a separate
            // public-site 'web' login), so this still logs out the admin guard only.
            $request->session()->forget('password_hash_admin');
            $request->session()->forget('password_hash_web');
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            return redirect()->to(Filament::getLoginUrl());
        }

        return $next($request);
    }
}

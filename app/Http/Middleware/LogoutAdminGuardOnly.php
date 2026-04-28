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
            $request->session()->regenerateToken();

            return redirect()->to(Filament::getLoginUrl());
        }

        return $next($request);
    }
}

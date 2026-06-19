<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
        // Runs first so every log line in the request carries a trace id.
        $middleware->web(prepend: [
            \App\Http\Middleware\AssignTraceId::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        // The theme cookie is written by client JS (plaintext) and read in the
        // layout to render the right <html> class — exclude it from Laravel's
        // cookie encryption so the server can read it (prevents a light-mode
        // flash when switching language via Livewire.navigate).
        $middleware->encryptCookies(except: ['app_theme']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            \Illuminate\Auth\Access\AuthorizationException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->is('admin*') && auth()->check()) {
                return redirect()->route('unauthorized');
            }
        });
    })->create();

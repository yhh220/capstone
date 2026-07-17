<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gives every request a trace id (reusing an upstream X-Request-Id if present) and
 * shares the request metadata via Laravel Context, so every log line emitted during
 * the request — file or DB — can be correlated. Also echoes the id back as a header.
 */
class AssignTraceId
{
    public function handle(Request $request, Closure $next): Response
    {
        $traceId = $request->headers->get('X-Request-Id') ?: Str::uuid()->toString();

        Context::add([
            'trace_id' => $traceId,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'path' => '/'.ltrim($request->path(), '/'),
        ]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $traceId);

        return $response;
    }
}

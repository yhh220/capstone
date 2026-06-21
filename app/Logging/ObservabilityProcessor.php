<?php

namespace App\Logging;

use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Context;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * Enriches every log record with request-trace metadata (from Laravel Context,
 * set by App\Http\Middleware\AssignTraceId), and — for error-level and above —
 * attaches the breadcrumb trail that preceded the failure.
 */
class ObservabilityProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $extra = $record->extra;

        // For CLI commands, generate a stable trace_id once per process and store
        // the artisan command name as the path so errors are identifiable in the log.
        if (app()->runningInConsole() && Context::get('trace_id') === null) {
            $argv    = $_SERVER['argv'] ?? [];
            $command = implode(' ', array_slice($argv, 1));
            Context::add([
                'trace_id' => 'cli:' . \Illuminate\Support\Str::uuid()->toString(),
                'path'     => $command ?: 'artisan',
            ]);
        }

        foreach (['trace_id', 'ip', 'method', 'path'] as $key) {
            $value = Context::get($key);
            if ($value !== null) {
                $extra[$key] = $value;
            }
        }

        // Resolved at log time — the user authenticates after the trace middleware.
        if (($userId = auth()->id()) !== null) {
            $extra['user_id'] = $userId;
        }

        if ($record->level->value >= Level::Error->value) {
            $crumbs = app(Breadcrumbs::class)->all();
            if ($crumbs !== []) {
                $extra['breadcrumbs'] = $crumbs;
            }
        }

        return $record->with(extra: $extra);
    }
}

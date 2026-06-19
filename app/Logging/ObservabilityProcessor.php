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

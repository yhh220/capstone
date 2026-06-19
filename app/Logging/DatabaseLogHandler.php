<?php

namespace App\Logging;

use App\Models\AppLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

/**
 * Writes structured logs into the `app_logs` table for the in-admin viewer.
 * Hardened so logging can never break the request: it never throws, never
 * recurses (a DB error logged here won't loop), and no-ops if the table is absent.
 */
class DatabaseLogHandler extends AbstractProcessingHandler
{
    private static bool $writing = false;

    protected function write(LogRecord $record): void
    {
        if (self::$writing) {
            return; // re-entry guard (e.g. a query error logged mid-write)
        }

        self::$writing = true;

        try {
            if (! Schema::hasTable('app_logs')) {
                return;
            }

            $extra = $record->extra;

            AppLog::create([
                'level'      => $record->level->value,
                'level_name' => $record->level->toPsrLogLevel(),
                'message'    => Str::limit($record->message, 2000),
                'channel'    => $record->channel,
                'trace_id'   => $extra['trace_id'] ?? null,
                'user_id'    => $extra['user_id'] ?? null,
                'ip'         => $extra['ip'] ?? null,
                'method'     => $extra['method'] ?? null,
                'path'       => $extra['path'] ?? null,
                'context'    => $this->payload($record->context, $extra),
                'logged_at'  => $record->datetime,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Logging must never throw. Drop silently.
        } finally {
            self::$writing = false;
        }
    }

    /** Build the JSON detail blob: context + breadcrumbs + leftover extra. */
    private function payload(array $context, array $extra): array
    {
        // Exceptions aren't JSON-serialisable — summarise them.
        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $e = $context['exception'];
            $context['exception'] = [
                'class'   => $e::class,
                'message' => $e->getMessage(),
                'at'      => $e->getFile() . ':' . $e->getLine(),
            ];
        }

        $breadcrumbs = $extra['breadcrumbs'] ?? null;
        unset($extra['trace_id'], $extra['user_id'], $extra['ip'], $extra['method'], $extra['path'], $extra['breadcrumbs']);

        return array_filter([
            'context'     => $context ?: null,
            'breadcrumbs' => $breadcrumbs,
            'extra'       => $extra ?: null,
        ], fn ($v) => $v !== null);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\AppLog;
use Illuminate\Console\Command;

class AutoResolveErrorLogs extends Command
{
    protected $signature   = 'logs:auto-resolve {--hours= : Hours of silence before auto-resolving (overrides LOG_AUTO_RESOLVE_HOURS)}';
    protected $description = 'Mark error logs as resolved when the same error has not recurred within the silence window.';

    public function handle(): int
    {
        // config(), not env() — env() reads nothing from .env once config is
        // cached (the entrypoint runs config:cache), silently reverting to 48.
        $hours  = max(1, (int) ($this->option('hours') ?: config('logging.db_log.auto_resolve_hours')));
        $cutoff = now()->subHours($hours);

        // Two set-based queries instead of a per-row exists() loop. (Not a single
        // correlated NOT EXISTS: MySQL/TiDB reject the update target reappearing
        // in a subquery FROM — error 1093 — so materialise the active set first.)
        // Step 1: fingerprints still seen inside the silence window (indexed, and
        // a handful of distinct values at most given the 7-day retention).
        $activeFingerprints = AppLog::query()
            ->whereIn('level_name', AppLog::ERROR_LEVELS)
            ->where('logged_at', '>', $cutoff)
            ->whereNotNull('fingerprint')
            ->distinct()
            ->pluck('fingerprint');

        // Step 2: everything stale, unresolved, and not in the active set is done.
        $count = AppLog::query()
            ->whereIn('level_name', AppLog::ERROR_LEVELS)
            ->whereNull('resolved_at')
            ->where('logged_at', '<=', $cutoff)
            ->whereNotIn('fingerprint', $activeFingerprints)
            ->update(['resolved_at' => now()]);

        $this->info("Auto-resolved {$count} error log(s) silent for {$hours}+ hours.");

        return self::SUCCESS;
    }
}

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
        $hours  = (int) ($this->option('hours') ?: env('LOG_AUTO_RESOLVE_HOURS', 48));
        $hours  = max(1, $hours);
        $cutoff = now()->subHours($hours);
        $now    = now();
        $count  = 0;

        AppLog::whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])
            ->whereNull('resolved_at')
            ->where('logged_at', '<=', $cutoff)
            ->chunkById(100, function ($logs) use ($cutoff, $now, &$count): void {
                foreach ($logs as $log) {
                    $fingerprint = substr($log->message, 0, 100);

                    $hasRecurred = AppLog::whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])
                        ->whereRaw('SUBSTR(message, 1, 100) = ?', [$fingerprint])
                        ->where('logged_at', '>', $cutoff)
                        ->exists();

                    if (! $hasRecurred) {
                        $log->update(['resolved_at' => $now]);
                        $count++;
                    }
                }
            });

        $this->info("Auto-resolved {$count} error log(s) silent for {$hours}+ hours.");

        return self::SUCCESS;
    }
}

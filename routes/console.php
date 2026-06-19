<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-regenerate sitemap daily at midnight
Schedule::command('sitemap:generate')->daily();

// Prune chat logs (90d) and structured app logs (LOG_DB_RETENTION_DAYS) so the
// tables can't grow unbounded.
Schedule::command('model:prune', ['--model' => [\App\Models\ChatLog::class, \App\Models\AppLog::class]])->daily();

// Heartbeat: the System Status page reads this to tell whether cron is alive.
Schedule::call(fn () => cache()->forever('scheduler:last_run', now()->toIso8601String()))
    ->everyMinute()
    ->name('scheduler-heartbeat');

// Cap the activity log so it can never grow unbounded and bloat the admin panel —
// keep the most recent 5,000 records, delete the rest. Adjust with --keep=N.
Schedule::command('activitylog:trim')->daily();

// Cancel unpaid orders past their 15-minute payment window and release stock.
Schedule::command('orders:expire-unpaid')->everyMinute();

// Email a day-before reminder for tomorrow's bookings (skips cancelled/completed).
Schedule::command('bookings:send-reminders')->dailyAt('09:00');

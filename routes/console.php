<?php

use App\Models\AppLog;
use App\Models\CartItem;
use App\Models\ChatLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-regenerate sitemap daily at midnight
Schedule::command('sitemap:generate')->daily();

// Prune chat logs (90d), structured app logs (LOG_DB_RETENTION_DAYS), and
// abandoned guest carts (30d) so none of these tables can grow unbounded.
Schedule::command('model:prune', ['--model' => [ChatLog::class, AppLog::class, CartItem::class]])->daily();

// The database session driver never deletes its own expired rows — prune
// anything past 2x the configured lifetime so the sessions table can't grow
// unbounded under real traffic.
Schedule::call(function () {
    DB::table('sessions')
        ->where('last_activity', '<', now()->subMinutes(config('session.lifetime') * 2)->getTimestamp())
        ->delete();
})->daily()->name('prune-expired-sessions');

// Built-in Laravel cleanup for the queue's failed_jobs table.
Schedule::command('queue:prune-failed', ['--hours' => 24 * 30])->daily();

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

// Auto-resolve error logs that have been silent for LOG_AUTO_RESOLVE_HOURS (default 48 h).
// Regression detection runs on write in DatabaseLogHandler — this handles the "went quiet" side.
Schedule::command('logs:auto-resolve')->dailyAt('03:00');

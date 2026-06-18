<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-regenerate sitemap daily at midnight
Schedule::command('sitemap:generate')->daily();

// Prune chat logs older than 90 days so spam / nonsense can't bloat the table.
Schedule::command('model:prune', ['--model' => [\App\Models\ChatLog::class]])->daily();

// Cap the activity log so it can never grow unbounded and bloat the admin panel —
// keep the most recent 5,000 records, delete the rest. Adjust with --keep=N.
Schedule::command('activitylog:trim')->daily();

// Cancel unpaid orders past their 15-minute payment window and release stock.
Schedule::command('orders:expire-unpaid')->everyMinute();

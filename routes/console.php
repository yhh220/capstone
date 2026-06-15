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

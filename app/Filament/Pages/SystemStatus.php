<?php

namespace App\Filament\Pages;

use App\Models\AppLog;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use UnitEnum;

class SystemStatus extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 92;
    protected static ?string $navigationLabel = 'System Status';
    protected static ?string $title = 'System Status';
    protected string $view = 'filament.pages.system-status';

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    private ?array $checksCache = null;

    /**
     * Plain-language health checks — written so a non-technical shop owner can tell
     * what is going on at a glance. Cached so the summary doesn't re-run them.
     *
     * @return array<int, array{name:string, status:string, value:string}>
     */
    public function getChecks(): array
    {
        return $this->checksCache ??= [
            $this->check('Your data', function () {
                DB::select('select 1');
                $size = '';
                $db = config('database.connections.' . config('database.default') . '.database');
                if (is_string($db) && is_file($db)) {
                    $size = ' (' . $this->humanSize((int) filesize($db)) . ' stored)';
                }

                return ['ok', 'Saved and reachable' . $size];
            }),
            $this->check('Website speed', function () {
                $key = 'status:ping:' . uniqid();
                Cache::put($key, '1', 5);
                $ok = Cache::get($key) === '1';
                Cache::forget($key);

                return $ok ? ['ok', 'Fast — caching is working'] : ['fail', 'Caching is not working'];
            }),
            $this->check('Background tasks', function () {
                $n = DB::table('jobs')->count();

                return [$n > 50 ? 'warn' : 'ok', $n === 0 ? 'Nothing waiting' : $n . ' waiting to run'];
            }),
            $this->check('Failed tasks', function () {
                $n = DB::table('failed_jobs')->count();

                return [$n > 0 ? 'warn' : 'ok', $n === 0 ? 'None failed' : $n . ' failed — worth a look'];
            }),
            $this->check('Automatic tasks', function () {
                $last = Cache::get('scheduler:last_run');
                if (! $last) {
                    return ['fail', 'Stopped — auto-emails and cleanup are paused'];
                }
                $stale = Carbon::parse($last)->diffInSeconds(now()) > 180;

                return [$stale ? 'fail' : 'ok', $stale ? 'Stopped — needs the cron set up' : 'Running normally'];
            }),
            $this->check('Email sending', fn () => filled(config('mail.mailers.smtp.username'))
                ? ['ok', 'Ready · ' . config('mail.from.address')]
                : ['warn', 'Not set up — customers will not get emails']),
            $this->check('Server space', function () {
                $writable = is_writable(storage_path('logs'));
                $freeGb = round((disk_free_space(base_path()) ?: 0) / 1_000_000_000, 1);
                if (! $writable) {
                    return ['fail', 'Cannot save files — needs attention'];
                }

                return [$freeGb < 2 ? 'warn' : 'ok', $freeGb . ' GB free'];
            }),
            $this->check('Recent problems', function () {
                $n = AppLog::whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])
                    ->where('logged_at', '>=', now()->subDay())->count();

                return [$n > 0 ? 'warn' : 'ok', $n === 0 ? 'None in the last 24 hours' : $n . ' in the last 24 hours'];
            }),
            $this->check('Developer mode', fn () => config('app.debug')
                ? ['warn', 'On — turn off before going live']
                : ['ok', 'Off (correct for a live site)']),
        ];
    }

    /** One-line overall verdict for the banner. @return array{0:string,1:string} */
    public function getSummary(): array
    {
        $problems = collect($this->getChecks())->where('status', 'fail')->count();
        $warnings = collect($this->getChecks())->where('status', 'warn')->count();

        if ($problems > 0) {
            return ['fail', $problems . ' thing' . ($problems === 1 ? '' : 's') . ' need fixing'];
        }
        if ($warnings > 0) {
            return ['warn', $warnings . ' thing' . ($warnings === 1 ? '' : 's') . ' to keep an eye on'];
        }

        return ['ok', 'Everything is running smoothly'];
    }

    /** @return array<string, string> */
    public function getAppInfo(): array
    {
        return [
            'Environment' => app()->environment(),
            'Version'     => env('APP_VERSION') ?: '—',
            'Laravel'     => app()->version(),
            'PHP'         => PHP_VERSION,
        ];
    }

    public function getRecentErrors()
    {
        return AppLog::whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])
            ->latest('id')->limit(8)->get();
    }

    private function humanSize(int $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024 || $unit === 'GB') {
                return round($bytes, $unit === 'B' ? 0 : 1) . ' ' . $unit;
            }
            $bytes /= 1024;
        }

        return $bytes . ' B';
    }

    public function clearCache(): void
    {
        try {
            // Flush all Laravel cache entries
            Cache::flush();

            // Clear configuration cache, route cache, view cache, and system cache using Artisan
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');

            \Filament\Notifications\Notification::make()
                ->title('System Cache Cleared')
                ->body('All application settings, chatbot FAQs, dashboard aggregated stats, and view caches have been cleared successfully.')
                ->success()
                ->send();

            // Clear private memory caches
            $this->checksCache = null;
        } catch (\Throwable $e) {
            logger()->error('Failed to clear cache: ' . $e->getMessage());

            \Filament\Notifications\Notification::make()
                ->title('Failed to clear cache')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function check(string $name, \Closure $probe): array
    {
        try {
            [$status, $value] = $probe();
        } catch (\Throwable $e) {
            $status = 'fail';
            $value = 'Error: ' . Str::limit($e->getMessage(), 60);
        }

        return ['name' => $name, 'status' => $status, 'value' => $value];
    }
}

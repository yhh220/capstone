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

    /** @return array<int, array{name:string, status:string, value:string}> */
    public function getChecks(): array
    {
        return [
            $this->check('Database', fn () => DB::select('select 1') ? ['ok', 'Connected'] : ['fail', 'No response']),
            $this->check('Cache', function () {
                $key = 'status:ping:' . uniqid();
                Cache::put($key, '1', 5);
                $ok = Cache::get($key) === '1';
                Cache::forget($key);

                return $ok ? ['ok', 'Read/write OK'] : ['fail', 'Read/write failed'];
            }),
            $this->check('Queue backlog', function () {
                $n = DB::table('jobs')->count();

                return [$n > 50 ? 'warn' : 'ok', $n . ' pending'];
            }),
            $this->check('Failed jobs', function () {
                $n = DB::table('failed_jobs')->count();

                return [$n > 0 ? 'warn' : 'ok', (string) $n];
            }),
            $this->check('Scheduler (cron)', function () {
                $last = Cache::get('scheduler:last_run');
                if (! $last) {
                    return ['fail', 'Never ran — is cron set up?'];
                }
                $when = Carbon::parse($last);

                return [$when->diffInSeconds(now()) > 180 ? 'fail' : 'ok', 'Last run ' . $when->diffForHumans()];
            }),
            $this->check('Mail', fn () => filled(config('mail.mailers.smtp.username'))
                ? ['ok', 'Configured · ' . config('mail.from.address')]
                : ['warn', 'Not configured']),
            $this->check('Storage', function () {
                $writable = is_writable(storage_path('logs'));
                $freeGb = round((disk_free_space(base_path()) ?: 0) / 1_000_000_000, 1);

                return [$writable ? 'ok' : 'fail', ($writable ? 'Writable' : 'NOT writable') . ' · ' . $freeGb . ' GB free'];
            }),
            $this->check('Errors (24h)', function () {
                $n = AppLog::whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])
                    ->where('logged_at', '>=', now()->subDay())->count();

                return [$n > 0 ? 'warn' : 'ok', $n . ' error' . ($n === 1 ? '' : 's')];
            }),
            $this->check('Debug mode', fn () => config('app.debug')
                ? ['warn', 'ON — turn OFF in production']
                : ['ok', 'Off']),
        ];
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

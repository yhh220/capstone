<?php

namespace App\Filament\Pages;

use App\Models\AppLog;
use App\Models\Setting;
use App\Services\Payments\StripeCheckoutService;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
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

    protected static string|UnitEnum|null $navigationGroup = 'System Settings';

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
            $this->check('Database', function () {
                DB::select('select 1');
                $size = '';
                $db = config('database.connections.'.config('database.default').'.database');
                if (is_string($db) && is_file($db)) {
                    $size = ' ('.$this->humanSize((int) filesize($db)).' stored)';
                }

                return ['ok', 'Orders, bookings and products are saved and reachable'.$size];
            }),
            // Named for what it actually probes (a cache write + read), not
            // "website speed" — that label made a cache failure read like a
            // network problem and a pass claim more than it proved.
            $this->check('Content cache', function () {
                $key = 'status:ping:'.uniqid();
                Cache::put($key, '1', 5);
                $ok = Cache::get($key) === '1';
                Cache::forget($key);

                return $ok ? ['ok', 'Working — pages reuse saved content'] : ['fail', 'Not working — pages rebuild everything on each visit'];
            }),
            $this->check('Payments', function () {
                if (setting('PAYMENT_MODE', 'demo') !== 'stripe') {
                    return ['ok', 'Demo mode — payments are simulated'];
                }
                if (! app(StripeCheckoutService::class)->enabled()) {
                    // enabled() already logged the specifics; the admin-facing
                    // story is that the switch says Stripe but demo is running.
                    return ['fail', 'Set to Stripe, but the test key is missing — taking demo payments instead'];
                }
                if (blank(config('services.stripe.webhook_secret'))) {
                    return ['warn', 'Stripe test mode is on, but the webhook secret is missing — payment confirmations rely on the customer returning to the site'];
                }

                return ['ok', 'Stripe Checkout (test mode) is active'];
            }),
            // One card for the whole queue: nothing in this app queues work
            // today (mail is sent inline, imports run sync), so two separate
            // always-zero cards ("Background tasks" / "Failed tasks") implied
            // machinery that wasn't there. Failures still surface if a queue
            // is ever introduced.
            $this->check('Task queue', function () {
                $waiting = DB::table('jobs')->count();
                $failed = DB::table('failed_jobs')->count();

                if ($failed > 0) {
                    return ['warn', $failed.' task(s) failed — worth a look'];
                }
                if ($waiting > 50) {
                    return ['warn', $waiting.' tasks waiting — the worker may be stuck'];
                }

                return ['ok', $waiting === 0 ? 'Empty — everything runs instantly' : $waiting.' waiting to run'];
            }),
            $this->check('Automatic tasks', function () {
                $last = Cache::get('scheduler:last_run');
                if (! $last) {
                    return ['fail', 'Stopped — auto-emails and cleanup are paused'];
                }
                // On a real server cron fires every minute, so 180s of silence
                // would mean it's genuinely stuck. In production the scheduler
                // is instead driven by an external pinger (cron-job.org) hitting
                // /cron/run-schedule every ~10 minutes — the threshold has to be
                // looser than that interval or this flaps "stopped" between pings.
                $stale = Carbon::parse($last)->diffInSeconds(now()) > 900;

                return [$stale ? 'fail' : 'ok', $stale ? 'Stopped — needs the cron set up' : 'Running normally'];
            }),
            // Judged against the mailer that is actually selected — production
            // sends through the Gmail API transport, so checking only the SMTP
            // username (as this used to) reported "not set up" on a perfectly
            // working install, and "ready" when the active mailer was the log.
            $this->check('Email sending', function () {
                $from = config('mail.from.address');

                return match ($mailer = config('mail.default')) {
                    'gmail_api' => filled(config('services.google.gmail_send_refresh_token'))
                        ? ['ok', 'Ready via Gmail · '.$from]
                        : ['fail', 'Gmail sending is not connected — visit /gmail-send/connect'],
                    'smtp' => filled(config('mail.mailers.smtp.username'))
                        ? ['ok', 'Ready via SMTP · '.$from]
                        : ['warn', 'SMTP is not set up — customers will not get emails'],
                    'log', 'array' => ['warn', 'Test mode — emails are written to the log, not sent'],
                    default => ['ok', 'Ready via '.$mailer.' · '.$from],
                };
            }),
            $this->check('Server space', function () {
                $writable = is_writable(storage_path('logs'));
                $freeGb = round((disk_free_space(base_path()) ?: 0) / 1_000_000_000, 1);
                if (! $writable) {
                    return ['fail', 'Cannot save files — needs attention'];
                }

                return [$freeGb < 2 ? 'warn' : 'ok', $freeGb.' GB free'];
            }),
            $this->check('Recent problems', function () {
                $n = AppLog::whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])
                    ->where('logged_at', '>=', now()->subDay())
                    ->whereNull('resolved_at')
                    ->count();

                return [$n > 0 ? 'warn' : 'ok', $n === 0 ? 'None in the last 24 hours' : $n.' in the last 24 hours'];
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
            return ['fail', $problems.' thing'.($problems === 1 ? '' : 's').' need fixing'];
        }
        if ($warnings > 0) {
            return ['warn', $warnings.' thing'.($warnings === 1 ? '' : 's').' to keep an eye on'];
        }

        return ['ok', 'Everything is running smoothly'];
    }

    /** @return array<string, string> */
    public function getAppInfo(): array
    {
        return [
            'Environment' => app()->environment(),
            'Version' => env('APP_VERSION') ?: '—',
            'Laravel' => app()->version(),
            'PHP' => PHP_VERSION,
        ];
    }

    public function getRecentErrors()
    {
        return AppLog::whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])
            ->whereNull('resolved_at')
            ->latest('id')->limit(8)->get();
    }

    private function humanSize(int $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024 || $unit === 'GB') {
                return round($bytes, $unit === 'B' ? 0 : 1).' '.$unit;
            }
            $bytes /= 1024;
        }

        return $bytes.' B';
    }

    public function clearCache(): void
    {
        try {
            // Content caches ONLY — deliberately not Cache::flush(). The database
            // cache store also holds every security counter: login-failure counts,
            // per-email lockouts, IP blocks, live OTP codes + their attempt caps,
            // every RateLimiter key (booking / contact / checkout / chatbot …), and
            // chatbot abuse blocks. Flushing all of it would unlock an in-progress
            // brute-forcer, invalidate codes for users mid-signup/reset, and reset
            // every rate limit. This button's only job is to drop the app's own
            // cached DB reads so an admin's content edit shows up immediately.
            //
            // Also no config:/route:/view:clear here: production boots with those
            // caches built (docker-entrypoint), and clearing-without-rebuilding
            // just slows every later request until the next deploy — and none of
            // them hold the DB content this button is about anyway.
            $keys = ['dashboard_stats', 'chatbot_faqs', 'chatbot_services', 'chatbot_brands', 'gmail_api_access_token'];

            foreach (Setting::pluck('key') as $settingKey) {
                $keys[] = 'setting_'.$settingKey;
            }

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            Notification::make()
                ->title('Content cache cleared')
                ->body('Settings, chatbot content, and dashboard statistics have been refreshed. Security counters and rate limits were left untouched.')
                ->success()
                ->send();

            // Clear private memory caches
            $this->checksCache = null;
        } catch (\Throwable $e) {
            logger()->error('Failed to clear cache: '.$e->getMessage());

            Notification::make()
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
            $value = 'Error: '.Str::limit($e->getMessage(), 60);
        }

        return ['name' => $name, 'status' => $status, 'value' => $value];
    }
}

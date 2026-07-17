<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Carbon;

/**
 * A single structured application log line, written by the `database` log channel
 * (see App\Logging\DatabaseLogHandler) and viewed in the admin Logs resource.
 */
class AppLog extends Model
{
    use Prunable;

    /** Levels treated as "problems" by the badge, auto-resolve, and recurrence checks. */
    public const ERROR_LEVELS = ['error', 'critical', 'alert', 'emergency'];

    /**
     * Manual "check for recurrence" refuses to resolve an error last seen inside
     * this window — too fresh to tell whether it's actually gone.
     */
    public const RECENT_ACTIVITY_MINUTES = 60;

    public $timestamps = false;

    protected $fillable = [
        'level', 'level_name', 'message', 'fingerprint', 'channel', 'trace_id',
        'user_id', 'ip', 'method', 'path', 'context', 'logged_at', 'created_at',
        'resolved_at',
    ];

    protected $casts = [
        'context' => 'array',
        'logged_at' => 'datetime',
        'created_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Grouping key for "the same error": first 100 CHARACTERS of the message.
     * mb_substr, not substr — a byte-based cut can split a multibyte character
     * and will never equal what SQL's character-based SUBSTR produced.
     */
    public static function fingerprintFor(string $message): string
    {
        return mb_substr($message, 0, 100);
    }

    /** All error-level entries sharing this entry's fingerprint (includes itself). */
    public function siblings(): Builder
    {
        return static::query()
            ->whereIn('level_name', self::ERROR_LEVELS)
            ->where('fingerprint', $this->fingerprint ?? self::fingerprintFor($this->message));
    }

    /**
     * Has this error happened again — and when was it last seen?
     *
     * - 'active'   → last occurrence is inside RECENT_ACTIVITY_MINUTES; too fresh to call fixed.
     * - 'recurred' → it happened again after this entry's burst (entry + 1 min).
     * - 'quiet'    → nothing newer than this entry's burst.
     *
     * @return array{state: 'active'|'recurred'|'quiet', last_seen: Carbon}
     */
    public function recurrenceState(): array
    {
        $lastSeen = Carbon::parse($this->siblings()->max('logged_at') ?? $this->logged_at);

        $state = match (true) {
            $lastSeen->gt(now()->subMinutes(self::RECENT_ACTIVITY_MINUTES)) => 'active',
            // +1 minute skips this entry's own burst (same error logged in quick
            // succession) — anchored on THIS entry, not the group's newest row,
            // so a later recurrence is actually detectable.
            $lastSeen->gt($this->logged_at->copy()->addMinute()) => 'recurred',
            default => 'quiet',
        };

        return ['state' => $state, 'last_seen' => $lastSeen];
    }

    /** Mark every unresolved entry of this error group fixed. Returns rows updated. */
    public function resolveSiblings(): int
    {
        return $this->siblings()
            ->whereNull('resolved_at')
            ->update(['resolved_at' => now()]);
    }

    /** Auto-prune old rows (model:prune) — keeps the table bounded. */
    public function prunable(): Builder
    {
        $days = max(1, (int) config('logging.db_log.retention_days'));

        return static::where('logged_at', '<=', now()->subDays($days));
    }

    /** Filament badge colour for a log level. */
    public function levelColor(): string
    {
        return match ($this->level_name) {
            'emergency', 'alert', 'critical', 'error' => 'danger',
            'warning' => 'warning',
            'notice', 'info' => 'info',
            default => 'gray', // debug
        };
    }
}

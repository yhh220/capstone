<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * A single structured application log line, written by the `database` log channel
 * (see App\Logging\DatabaseLogHandler) and viewed in the admin Logs resource.
 */
class AppLog extends Model
{
    use Prunable;

    public $timestamps = false;

    protected $fillable = [
        'level', 'level_name', 'message', 'channel', 'trace_id',
        'user_id', 'ip', 'method', 'path', 'context', 'logged_at', 'created_at',
        'resolved_at',
    ];

    protected $casts = [
        'context'     => 'array',
        'logged_at'   => 'datetime',
        'created_at'  => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /** Auto-prune old rows (model:prune) — keeps the table bounded. */
    public function prunable(): Builder
    {
        $days = (int) env('LOG_DB_RETENTION_DAYS', 7);

        return static::where('logged_at', '<=', now()->subDays(max(1, $days)));
    }

    /** Filament badge colour for a log level. */
    public function levelColor(): string
    {
        return match ($this->level_name) {
            'emergency', 'alert', 'critical', 'error' => 'danger',
            'warning'                                 => 'warning',
            'notice', 'info'                          => 'info',
            default                                   => 'gray', // debug
        };
    }
}

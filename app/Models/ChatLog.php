<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Schema;

class ChatLog extends Model
{
    use Prunable;

    /**
     * Auto-delete chat logs older than 90 days so spam / nonsense can't grow
     * the table without bound. Pruned nightly by the scheduler (model:prune).
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }

    protected $fillable = [
        'driver',
        'feature',
        'request_payload',
        'response_payload',
        'status',
        'error_message',
        'ip_address',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public static function record(array $attributes): void
    {
        if (! Schema::hasTable('chat_logs')) {
            return;
        }

        static::create($attributes);
    }
}

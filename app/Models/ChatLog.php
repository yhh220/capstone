<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ChatLog extends Model
{
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
        if (!Schema::hasTable('chat_logs')) {
            return;
        }

        static::create($attributes);
    }
}

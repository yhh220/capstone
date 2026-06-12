<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ChatbotFaq extends Model
{
    protected $fillable = [
        'topic',
        'keywords',
        'priority',
        'reply_en',
        'reply_ms',
        'reply_zh',
        'is_active',
    ];

    protected $casts = [
        'keywords' => 'array',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // The chatbot caches the FAQ rule set — bust it on any change.
        static::saved(fn () => Cache::forget('chatbot_faqs'));
        static::deleted(fn () => Cache::forget('chatbot_faqs'));
    }
}

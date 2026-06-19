<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Faq extends Model
{
    protected $fillable = [
        'category',
        'question_en', 'question_ms', 'question_zh',
        'answer_en', 'answer_ms', 'answer_zh',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    protected static function booted(): void
    {
        // The public FAQ page caches the active list — bust it on any change.
        static::saved(fn () => Cache::forget('public_faqs'));
        static::deleted(fn () => Cache::forget('public_faqs'));
    }

    /** Question in the current UI language, falling back to English. */
    public function question(): string
    {
        return $this->{'question_' . app()->getLocale()} ?: $this->question_en;
    }

    /** Answer in the current UI language, falling back to English. */
    public function answer(): string
    {
        return $this->{'answer_' . app()->getLocale()} ?: $this->answer_en;
    }

    /** Active FAQs in display order (cached, busted on save/delete). */
    public static function published()
    {
        return Cache::rememberForever('public_faqs', fn () => static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get());
    }
}

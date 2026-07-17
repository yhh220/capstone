<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'is_active' => 'boolean',
    ];

    /** Question in the current UI language, falling back to English. */
    public function question(): string
    {
        return $this->{'question_'.app()->getLocale()} ?: $this->question_en;
    }

    /** Answer in the current UI language, falling back to English. */
    public function answer(): string
    {
        return $this->{'answer_'.app()->getLocale()} ?: $this->answer_en;
    }

    /** Active FAQs in display order. (Tiny table — queried directly, not cached.) */
    public static function published()
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}

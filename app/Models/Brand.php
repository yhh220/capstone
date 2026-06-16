<?php

namespace App\Models;

use App\Models\Concerns\HasSortableOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Brand extends Model
{
    use HasSortableOrder;

    protected $fillable = [
        'name',
        'logo',
        'display_type',
        'website_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Keep the chatbot's brand list in sync the moment a brand changes. */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('chatbot_brands'));
        static::deleted(fn () => Cache::forget('chatbot_brands'));
    }
}

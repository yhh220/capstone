<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return cache()->remember("setting_{$key}", 3600, fn() =>
            static::find($key)?->value ?? $default
        );
    }

    /**
     * Set a setting value and bust cache.
     */
    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting_{$key}");
    }
}

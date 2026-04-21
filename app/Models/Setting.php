<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Setting extends Model
{
    use LogsActivity;

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * Get a setting value by key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (!Schema::hasTable('settings')) {
            return $default;
        }

        return cache()->remember("setting_{$key}", 3600, fn() =>
            static::find($key)?->value ?? $default
        );
    }

    /**
     * Set a setting value and bust cache.
     */
    public static function setValue(string $key, mixed $value): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting_{$key}");
    }
}

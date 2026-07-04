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

    protected static function booted(): void
    {
        // When online shopping is switched OFF, clear the board: cancel + restock
        // every unpaid order so no customer is left with a "pending payment" order
        // they can no longer pay for (the /pay route is gated by ShoppingEnabled).
        // Runs from any edit path — the table toggle or the edit form both save
        // through here. Fires only on a real true→false change (wasChanged guard),
        // so re-saving 'false' or the initial seed insert never triggers it.
        static::updated(function (Setting $setting): void {
            if ($setting->key === 'ONLINE_SHOPPING_ENABLED'
                && $setting->wasChanged('value')
                && $setting->value === 'false') {
                app(\App\Services\ShopModeService::class)->cancelUnpaidOrders();
            }
        });
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

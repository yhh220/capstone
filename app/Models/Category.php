<?php

namespace App\Models;

use App\Models\Concerns\HasSortableOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Category extends Model
{
    use HasSortableOrder, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = ['name', 'slug', 'description', 'image', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
        static::deleting(function (self $model) {
            if ($model->image) {
                Storage::disk('public')->delete($model->image);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * A fitting Lucide icon name picked from the category name keywords.
     * Falls back to a neutral "package" for anything unmatched, so new
     * admin-created categories always get a sensible icon.
     */
    public function getIconAttribute(): string
    {
        $name = mb_strtolower($this->name ?? '');

        return match (true) {
            str_contains($name, 'subwoofer'), str_contains($name, 'speaker'),
            str_contains($name, 'audio'), str_contains($name, 'sound') => 'speaker',
            str_contains($name, 'amp') => 'audio-waveform',
            str_contains($name, 'android'), str_contains($name, 'player'),
            str_contains($name, 'head unit'), str_contains($name, 'radio'),
            str_contains($name, 'multimedia'), str_contains($name, 'screen'),
            str_contains($name, 'monitor') => 'monitor-play',
            str_contains($name, 'dash'), str_contains($name, 'cam'),
            str_contains($name, 'camera'), str_contains($name, 'recorder') => 'video',
            str_contains($name, 'aircond'), str_contains($name, 'cond'),
            str_contains($name, 'cooling'), str_contains($name, 'gas') => 'snowflake',
            str_contains($name, 'fresh'), str_contains($name, 'perfume'),
            str_contains($name, 'scent'), str_contains($name, 'fragrance') => 'wind',
            str_contains($name, 'body'), str_contains($name, 'spoiler'),
            str_contains($name, 'bumper'), str_contains($name, 'kit') => 'car-front',
            str_contains($name, 'mat'), str_contains($name, 'carpet'),
            str_contains($name, 'floor') => 'footprints',
            str_contains($name, 'tint'), str_contains($name, 'film'),
            str_contains($name, 'window') => 'sun',
            str_contains($name, 'alarm'), str_contains($name, 'security'),
            str_contains($name, 'lock') => 'shield-check',
            str_contains($name, 'led'), str_contains($name, 'light'),
            str_contains($name, 'bulb'), str_contains($name, 'lamp') => 'lightbulb',
            str_contains($name, 'plate') => 'rectangle-horizontal',
            str_contains($name, 'sensor'), str_contains($name, 'reverse'),
            str_contains($name, 'parking'), str_contains($name, 'radar') => 'radar',
            str_contains($name, 'gps'), str_contains($name, 'nav') => 'navigation',
            str_contains($name, 'charger'), str_contains($name, 'usb'),
            str_contains($name, 'power') => 'plug-zap',
            str_contains($name, 'tyre'), str_contains($name, 'tire'),
            str_contains($name, 'wheel'), str_contains($name, 'rim') => 'disc-3',
            str_contains($name, 'seat'), str_contains($name, 'cover'),
            str_contains($name, 'cushion') => 'armchair',
            str_contains($name, 'horn') => 'megaphone',
            str_contains($name, 'wiper') => 'cloud-rain',
            default => 'package',
        };
    }
}

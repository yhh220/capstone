<?php

namespace App\Models;

use App\Models\Concerns\HasSortableOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Service extends Model implements HasMedia
{
    use HasSortableOrder, InteractsWithMedia, LogsActivity;

    /** Keep the chatbot's live price list in sync the moment a service changes. */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('chatbot_services'));
        static::deleted(fn () => Cache::forget('chatbot_services'));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(400)
            ->optimize()
            ->performOnCollections('images');
    }

    public function getImageUrl(string $conversion = ''): ?string
    {
        if ($this->hasMedia('images')) {
            return $this->getFirstMediaUrl('images', $conversion) ?: null;
        }
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'name', 'name_ms', 'name_zh', 'description', 'description_ms', 'description_zh',
        'price', 'duration', 'duration_minutes', 'buffer_after', 'image', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'buffer_after' => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Name/description in the visitor's language, falling back to English.
     * Services used to translate through lang JSON __() keys; DB columns
     * (same pattern as Product) keep translations intact when an admin
     * renames or creates a service from the panel.
     */
    public function getLocalizedNameAttribute(): string
    {
        return $this->translatedField('name') ?? $this->name;
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return $this->translatedField('description') ?? $this->description;
    }

    /** Locale variant of a *_ms / *_zh column pair, null when empty. */
    private function translatedField(string $field): ?string
    {
        return match (app()->getLocale()) {
            'ms' => $this->{$field.'_ms'} ?: null,
            'zh' => $this->{$field.'_zh'} ?: null,
            default => null,
        };
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->duration) {
            return $this->duration;
        }

        $minutes = max((int) $this->duration_minutes, 0);

        if ($minutes === 0) {
            return __('Flexible');
        }

        if ($minutes < 60) {
            return "{$minutes} min";
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        return $remaining > 0
            ? "{$hours} hr {$remaining} min"
            : "{$hours} hr";
    }
}

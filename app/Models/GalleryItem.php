<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GalleryItem extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(600)
            ->optimize()
            ->performOnCollections('images');

        $this->addMediaConversion('full')
            ->width(1200)
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
        'title', 'description', 'image', 'category', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public const CATEGORIES = ['audio', 'tint', 'accessories', 'modification', 'general'];
}

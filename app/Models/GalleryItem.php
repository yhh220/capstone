<?php

namespace App\Models;

use App\Models\Concerns\HasSortableOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GalleryItem extends Model implements HasMedia
{
    use HasSortableOrder, InteractsWithMedia, LogsActivity;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Generate conversions synchronously on upload. The queue connection is
        // `database` with no always-on worker, so a queued conversion would never
        // run and the thumbnail URL would 404 (a broken image in the gallery).
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(600)
            ->optimize()
            ->nonQueued()
            ->performOnCollections('images');

        $this->addMediaConversion('full')
            ->width(1200)
            ->optimize()
            ->nonQueued()
            ->performOnCollections('images');
    }

    public function getImageUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia('images');

        if ($media) {
            // Only point at the resized conversion when it has actually been
            // generated; otherwise fall back to the original upload so the image
            // always renders (never a broken thumbnail).
            if ($conversion !== '' && $media->hasGeneratedConversion($conversion)) {
                return $media->getUrl($conversion);
            }

            return $media->getUrl();
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

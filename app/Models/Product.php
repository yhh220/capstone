<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
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
            ->width(400)
            ->height(300)
            ->sharpen(5)
            ->optimize()
            ->performOnCollections('images');

        $this->addMediaConversion('card')
            ->width(800)
            ->height(600)
            ->optimize()
            ->performOnCollections('images');
    }

    /** Returns the best available image URL: medialibrary first, then legacy Storage path */
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
        'category_id', 'name', 'brand', 'slug', 'description', 'description_ms', 'description_zh', 'short_description',
        'price', 'sale_price', 'sku', 'stock', 'image', 'images', 'specs', 'compatible_vehicles',
        'model_url', 'has_3d', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'images' => 'array',
        'specs' => 'array',
        'compatible_vehicles' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'has_3d' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function carModels()
    {
        return $this->belongsToMany(CarModel::class, 'product_compatibilities');
    }

    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    public function getTranslatedDescriptionAttribute(): ?string
    {
        return match (app()->getLocale()) {
            'ms' => $this->description_ms ?: $this->description,
            'zh' => $this->description_zh ?: $this->description,
            default => $this->description,
        };
    }
}

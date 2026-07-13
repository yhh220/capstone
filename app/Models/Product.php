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
        // The first item remains the primary catalogue image. A multi-file
        // collection enables an ordered product gallery without disrupting
        // existing products that have only one image.
        $this->addMediaCollection('images')
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
        $media = $this->getFirstMedia('images');

        if ($media) {
            // getUrl('thumb') still returns a URL even when that conversion file
            // does not exist yet. Serving the original image is preferable to a
            // broken <img> while a newly uploaded image is being processed.
            if ($conversion !== '' && ! $media->hasGeneratedConversion($conversion)) {
                return $media->getUrl() ?: null;
            }

            return $media->getUrl($conversion) ?: null;
        }

        return $this->image ? Storage::url($this->image) : null;
    }

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true)->latest();
    }

    /** Reviews currently enabled for public display by a staff member. */
    public function visibleReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'category_id', 'name', 'name_ms', 'name_zh', 'brand', 'slug', 'description', 'description_ms', 'description_zh',
        'short_description', 'short_description_ms', 'short_description_zh',
        'price', 'sale_price', 'sku', 'stock', 'image', 'images', 'specs', 'compatible_vehicles',
        'model_url', 'has_3d', 'is_active', 'is_featured',
    ];

    protected $appends = ['current_price', 'is_on_sale'];

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
                $base = Str::slug($product->name);
                $slug = $base;
                $i    = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $product->slug = $slug;
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
        return $this->translatedField('description');
    }

    public function getTranslatedNameAttribute(): ?string
    {
        return $this->translatedField('name');
    }

    public function getTranslatedShortDescriptionAttribute(): ?string
    {
        return $this->translatedField('short_description');
    }

    /** Locale variant of a *_ms / *_zh column pair, falling back to the English base column. */
    private function translatedField(string $field): ?string
    {
        return match (app()->getLocale()) {
            'ms' => $this->{$field . '_ms'} ?: $this->{$field},
            'zh' => $this->{$field . '_zh'} ?: $this->{$field},
            default => $this->{$field},
        };
    }
}

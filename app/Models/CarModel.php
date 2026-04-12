<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CarModel extends Model
{
    protected $fillable = ['brand', 'model', 'year_from', 'year_to'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_compatibilities');
    }
}

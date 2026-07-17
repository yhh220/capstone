<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    public static function boot()
    {
        parent::boot();

        static::creating(function (Activity $activity) {
            if (request()) {
                $activity->ip_address = request()->ip();
            }
        });
    }
}

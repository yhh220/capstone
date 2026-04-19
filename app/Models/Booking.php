<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Booking extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'customer_name', 'customer_phone', 'customer_email',
        'vehicle_model', 'vehicle_plate', 'service_id', 'preferred_date', 'preferred_time',
        'start_at', 'end_at', 'notes', 'status', 'confirm_token',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'confirmed'  => 'success',
            'cancelled'  => 'danger',
            'completed'  => 'info',
            default      => 'warning',
        };
    }

    public function getManageUrlAttribute(): ?string
    {
        return $this->confirm_token ? route('booking.manage', $this->confirm_token) : null;
    }
}

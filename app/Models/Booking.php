<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Booking extends Model
{
    use LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'user_id', 'reference', 'customer_name', 'customer_phone', 'customer_email',
        'vehicle_model', 'vehicle_plate', 'service_id', 'preferred_date',
        'start_at', 'end_at', 'notes', 'status',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The account that placed this booking, when made while logged in.
     * Null for guest (showroom-mode) bookings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'warning',
        };
    }

    /**
     * Human-friendly, sequential booking reference like BK-2026-00042.
     * Mirrors Order::generateOrderNumber() — the customer quotes this together
     * with their email to look up or cancel a booking (no opaque tokens).
     * Uses withTrashed() + a row lock so soft-deleted rows can't cause the
     * unique reference to be reused, and concurrent creates stay collision-free.
     */
    public static function generateReference(): string
    {
        return DB::transaction(function () {
            $year = date('Y');
            $latest = static::withTrashed()
                ->where('reference', 'like', "BK-{$year}-%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $count = $latest
                ? ((int) substr($latest->reference, strrpos($latest->reference, '-') + 1)) + 1
                : 1;

            return 'BK-'.$year.'-'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
        });
    }
}

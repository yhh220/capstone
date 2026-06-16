<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Order extends Model
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
        'user_id', 'order_number',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'subtotal', 'total_amount',
        'status', 'payment_status', 'payment_method', 'notes',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'subtotal'         => 'decimal:2',
        'total_amount'     => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique order number like ORD-2026-00001
     * Fixes: Concurrency crashes and yearly counter un-reset (Bug 1, Bug 10)
     */
    public static function generateOrderNumber(): string
    {
        return DB::transaction(function () {
            $year = date('Y');
            $latestOrder = static::where('order_number', 'like', "ORD-{$year}-%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $count = $latestOrder
                ? ((int) substr($latestOrder->order_number, strrpos($latestOrder->order_number, '-') + 1)) + 1
                : 1;

            return 'ORD-' . $year . '-' . str_pad((string) $count, 5, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Get available statuses for the order lifecycle.
     */
    public static function statuses(): array
    {
        return ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    }

    /**
     * Get the next status in the progression.
     */
    public function getNextStatusAttribute(): ?string
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
        $currentIndex = array_search($this->status, $statuses);
        if ($currentIndex === false || $currentIndex >= count($statuses) - 1) {
            return null;
        }
        return $statuses[$currentIndex + 1];
    }
}

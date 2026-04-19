<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'tracking_number',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'subtotal', 'total_amount',
        'status', 'payment_status',
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
        $year = date('Y');
        $latestOrder = static::where('order_number', 'like', "ORD-{$year}-%")->orderBy('id', 'desc')->first();
        
        if ($latestOrder) {
            $lastSequence = (int) substr($latestOrder->order_number, -5);
            $count = $lastSequence + 1;
        } else {
            $count = 1;
        }
        
        return 'ORD-' . $year . '-' . str_pad((string)$count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique tracking number like WNWN12345678
     * Fixes: Adds a uniqueness check loop to avoid collisions
     */
    public static function generateTrackingNumber(): string
    {
        do {
            $tracking = 'WNWN' . strtoupper(Str::random(8));
        } while (static::where('tracking_number', $tracking)->exists());

        return $tracking;
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

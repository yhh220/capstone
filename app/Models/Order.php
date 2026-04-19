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
     */
    public static function generateOrderNumber(): string
    {
        $count = static::count() + 1;
        return 'ORD-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique tracking number like WNWN12345678
     */
    public static function generateTrackingNumber(): string
    {
        return 'WNWN' . strtoupper(Str::random(8));
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

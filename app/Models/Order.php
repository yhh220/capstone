<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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
        'user_id', 'order_number', 'tracking_number',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'subtotal', 'shipping_fee', 'delivery_method', 'pickup_at', 'total_amount',
        'status', 'payment_status', 'payment_method', 'notes', 'expires_at',
        'stripe_session_id', 'stripe_payment_intent_id',
        'paid_at', 'shipped_at', 'delivered_at', 'cancelled_at',
        'cancellation_reason', 'refund_amount', 'refund_percentage', 'cancelled_by', 'refunded_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'pickup_at' => 'datetime',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refund_amount' => 'decimal:2',
        'refund_percentage' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Auto-stamp lifecycle timestamps on any model update, so every code path
        // (admin actions, the advance action, expiry…) records when it happened
        // exactly once. (The pay() flow uses a raw query update, so it stamps paid_at
        // itself — that's the one path this event can't see.)
        static::updating(function (Order $order): void {
            if ($order->isDirty('payment_status') && $order->payment_status === 'paid' && $order->paid_at === null) {
                $order->paid_at = now();
            }
            if ($order->isDirty('status') && $order->status === 'shipped' && $order->shipped_at === null) {
                $order->shipped_at = now();
            }
            if ($order->isDirty('status') && $order->status === 'delivered' && $order->delivered_at === null) {
                $order->delivered_at = now();
            }
            if ($order->isDirty('status') && $order->status === 'cancelled' && $order->cancelled_at === null) {
                $order->cancelled_at = now();
            }
        });
    }

    /**
     * Return this order's reserved stock to inventory (idempotent at the row level
     * via the caller's lock). Centralised so expiry, the timer and admin cancel all
     * release stock the same way.
     */
    public function restockItems(): void
    {
        foreach ($this->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }
    }

    /** Self-collection at the showroom (no address, no shipping fee). */
    public function isPickup(): bool
    {
        return $this->delivery_method === 'pickup';
    }

    /** Order is awaiting online payment (drives the pay page, timer and "Pay now"). */
    public function isAwaitingPayment(): bool
    {
        return $this->payment_status === 'pending'
            && $this->status !== 'cancelled';
    }

    /** Awaiting payment but the payment window has elapsed (15 min for demo
     * orders; stretched to outlive the Stripe session when one is created). */
    public function isPaymentExpired(): bool
    {
        return $this->isAwaitingPayment()
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    /** Seconds left to pay (0 once expired); drives the countdown UI. */
    public function secondsUntilExpiry(): int
    {
        if (! $this->expires_at) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->expires_at, false));
    }

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

            // withTrashed(): a soft-deleted order still owns its order_number in the
            // unique index, so it must count toward the next number (otherwise the
            // new order collides with the deleted one).
            $latestOrder = static::withTrashed()
                ->where('order_number', 'like', "ORD-{$year}-%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $count = $latestOrder
                ? ((int) substr($latestOrder->order_number, strrpos($latestOrder->order_number, '-') + 1)) + 1
                : 1;

            // Belt-and-suspenders: skip any number already taken (gaps / rare races)
            // so we never hand back a colliding order_number.
            do {
                $number = 'ORD-'.$year.'-'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
                $count++;
            } while (static::withTrashed()->where('order_number', $number)->exists());

            return $number;
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'session_id', 'product_id', 'quantity'];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Attributes that identify the current cart owner (user if authed, else guest session).
     * Use for create() so rows always carry the correct owner key.
     */
    public static function currentOwnerAttributes(): array
    {
        return Auth::check()
            ? ['user_id' => Auth::id()]
            : ['session_id' => session()->getId(), 'user_id' => null];
    }

    /**
     * Query builder scoped to the current cart owner.
     * Auth users read by user_id; guests read by session_id (and only guest rows).
     */
    public static function forCurrentOwner(): Builder
    {
        if (Auth::check()) {
            return static::query()->where('user_id', Auth::id());
        }

        return static::query()
            ->where('session_id', session()->getId())
            ->whereNull('user_id');
    }

    /**
     * Transfer a guest session's cart to a just-logged-in user, merging quantities
     * when the same product already exists in the user's cart.
     * Call BEFORE session()->regenerate().
     */
    public static function claimGuestCart(string $guestSessionId, int $userId): void
    {
        DB::transaction(function () use ($guestSessionId, $userId) {
            $guestItems = static::where('session_id', $guestSessionId)
                ->whereNull('user_id')
                ->lockForUpdate()
                ->get();

            foreach ($guestItems as $guestItem) {
                $existing = static::where('user_id', $userId)
                    ->where('product_id', $guestItem->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    $existing->increment('quantity', $guestItem->quantity);
                    $guestItem->delete();
                } else {
                    $guestItem->update([
                        'user_id'    => $userId,
                        'session_id' => null,
                    ]);
                }
            }
        });
    }
}

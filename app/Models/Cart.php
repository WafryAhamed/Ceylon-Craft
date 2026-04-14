<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total price of the cart.
     */
    public function getTotalPrice(): float
    {
        return $this->items()->with('product')
            ->get()
            ->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
    }

    /**
     * Get the total number of items in the cart.
     */
    public function getTotalItems(): int
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Clear the cart.
     */
    public function clear(): void
    {
        $this->items()->delete();
    }
}

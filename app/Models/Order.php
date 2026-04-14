<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate order total from items.
     */
    public function calculateTotal(): float
    {
        return $this->items()->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Mark order as paid.
     */
    public function markAsPaid(): self
    {
        $this->update(['payment_status' => 'paid', 'status' => 'paid']);
        return $this;
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped(): self
    {
        $this->update(['status' => 'shipped']);
        return $this;
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered(): self
    {
        $this->update(['status' => 'delivered']);
        return $this;
    }

    /**
     * Mark order as cancelled.
     */
    public function markAsCancelled(): self
    {
        $this->update(['status' => 'cancelled']);
        return $this;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get recent orders.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}

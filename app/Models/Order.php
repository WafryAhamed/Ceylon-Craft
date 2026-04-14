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
     * Get the status history of the order.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the payment record for this order.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
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
     * Update status and log to history.
     * 
     * @param string $status
     * @param string|null $notes
     * @param string|null $trackingNumber
     * @return void
     */
    public function updateStatus(string $status, ?string $notes = null, ?string $trackingNumber = null): void
    {
        // Don't create history if status hasn't changed
        if ($this->status === $status) {
            return;
        }

        // Update order status
        $this->update([
            'status' => $status,
        ]);

        // Log to status history
        $this->statusHistory()->create([
            'status' => $status,
            'notes' => $notes,
            'tracking_number' => $trackingNumber,
            'updated_by_user_id' => auth('api')->user()?->id,
        ]);

        // Trigger events based on status change
        if ($status === 'shipped' && !$this->statusHistory()->where('status', 'packed')->exists()) {
            // Auto-mark as packed before shipping
            $this->statusHistory()->create([
                'status' => 'packed',
                'notes' => 'Automatically marked as packed',
                'updated_by_user_id' => auth('api')->user()?->id,
            ]);
        }

        // TODO: Send email notifications based on status
    }

    /**
     * Mark order as paid.
     */
    public function markAsPaid(): self
    {
        $this->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        $this->updateStatus('confirmed', 'Payment received');
        return $this;
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped(?string $trackingNumber = null): self
    {
        $this->updateStatus('shipped', 'Order shipped', $trackingNumber);
        return $this;
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered(): self
    {
        $this->updateStatus('delivered', 'Order delivered');
        return $this;
    }

    /**
     * Mark order as cancelled.
     */
    public function markAsCancelled(?string $reason = null): self
    {
        $this->updateStatus('cancelled', $reason ?? 'Order cancelled by user');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Order Status History Model
 * 
 * Tracks all status changes for an order with timestamps,
 * admin notes, and tracking information.
 */
class OrderStatusHistory extends Model
{
    protected $table = 'order_status_histories';

    protected $fillable = [
        'order_id',
        'status',
        'tracking_number',
        'notes',
        'updated_by_user_id',
    ];

    /**
     * Relationship: Status history belongs to Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: Status history was set by User (admin)
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}

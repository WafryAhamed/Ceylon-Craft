<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'carrier',
        'tracking_number',
        'status',
        'shipped_at',
        'delivered_at',
        'estimated_delivery_at',
        'last_location',
        'last_update_at',
        'notes',
        'reason_for_failure',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'last_update_at' => 'datetime',
    ];

    /**
     * Relationship: Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if shipment is delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if shipment has failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Pending Shipment',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'failed' => 'Delivery Failed',
            'returned' => 'Returned to Sender',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }
}

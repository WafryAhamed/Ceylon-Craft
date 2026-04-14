<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment Model
 * 
 * Tracks all payment transactions in the system.
 * Links payments to orders and stores Stripe transaction IDs.
 */
class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'user_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_payment_method_id',
        'amount',
        'currency',
        'status', // pending, processing, succeeded, failed, refunded
        'payment_method_type', // stripe, payhere, bank_transfer
        'metadata',
        'failed_at',
        'succeeded_at',
        'refunded_at',
        'error_message',
        'idempotency_key',
    ];

    protected $casts = [
        'amount' => 'float',
        'metadata' => 'json',
        'failed_at' => 'datetime',
        'succeeded_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Relationship: Payment belongs to Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: Payment belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark payment as processing.
     */
    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Mark payment as succeeded.
     */
    public function markAsSucceeded(string $chargeId): void
    {
        $this->update([
            'status' => 'succeeded',
            'stripe_charge_id' => $chargeId,
            'succeeded_at' => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Mark payment as refunded.
     */
    public function markAsRefunded(string $refundId): void
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
    }

    /**
     * Check if payment is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'succeeded';
    }

    /**
     * Check if payment has failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }
}

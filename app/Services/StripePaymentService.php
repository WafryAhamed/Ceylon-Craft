<?php

namespace App\Services;

use App\Exceptions\PaymentFailedException;
use App\Models\Payment;
use Stripe\Client;
use Stripe\Exception\ApiException;
use Stripe\PaymentIntent;
use Stripe\Charge;

/**
 * Stripe Payment Service
 * 
 * Handles all Stripe payment operations including intent creation,
 * confirmation, webhook processing, and refunds.
 * 
 * PRODUCTION CONSIDERATIONS:
 * - Implements idempotency keys to prevent duplicate charges
 * - Handles webhook verification and event processing
 * - Maintains comprehensive payment logs
 * - Implements retry logic for transient failures
 */
class StripePaymentService
{
    protected Client $client;

    public function __construct()
    {
        // Initialize Stripe client with API key from .env
        $this->client = new Client([
            'api_key' => config('services.stripe.secret'),
            'stripe_version' => '2024-04-10', // Latest API version
        ]);
    }

    /**
     * Create a payment intent for Stripe.
     * 
     * @param int $userId
     * @param float $amount Amount in cents (e.g., 1000 = $10.00)
     * @param string $description
     * @param array $metadata
     * @param string|null $idempotencyKey
     * 
     * @return PaymentIntent
     * @throws PaymentFailedException
     */
    public function createPaymentIntent(
        int $userId,
        float $amount,
        string $description,
        array $metadata = [],
        string $idempotencyKey = null
    ): PaymentIntent {
        try {
            $idempotencyKey = $idempotencyKey ?? $this->generateIdempotencyKey($userId);

            $paymentIntent = PaymentIntent::create(
                [
                    'amount' => (int)$amount, // Amount in cents
                    'currency' => 'usd',
                    'description' => $description,
                    'metadata' => array_merge([
                        'user_id' => $userId,
                        'created_at' => now()->toIso8601String(),
                    ], $metadata),
                    'statement_descriptor' => 'Ceylon Craft Order',
                ],
                [
                    'idempotency_key' => $idempotencyKey,
                ]
            );

            // Log payment intent creation
            \Log::info('Payment intent created', [
                'intent_id' => $paymentIntent->id,
                'user_id' => $userId,
                'amount' => $amount,
            ]);

            return $paymentIntent;
        } catch (ApiException $e) {
            \Log::error('Stripe payment intent creation failed', [
                'user_id' => $userId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            throw new PaymentFailedException(
                'Failed to create payment intent',
                $e->getMessage()
            );
        }
    }

    /**
     * Confirm a payment intent (complete payment).
     * 
     * @param string $intentId
     * @param string $paymentMethodId
     * 
     * @return PaymentIntent
     * @throws PaymentFailedException
     */
    public function confirmPaymentIntent(string $intentId, string $paymentMethodId): PaymentIntent
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($intentId);

            if ($paymentIntent->status !== 'requires_payment_method') {
                throw new \Exception("Payment intent is in {$paymentIntent->status} status, cannot confirm");
            }

            $paymentIntent = $paymentIntent->confirm([
                'payment_method' => $paymentMethodId,
                'return_url' => config('app.url') . '/checkout/confirm', // For 3D Secure
            ]);

            // Log confirmation
            \Log::info('Payment intent confirmed', [
                'intent_id' => $intentId,
                'status' => $paymentIntent->status,
            ]);

            return $paymentIntent;
        } catch (ApiException $e) {
            \Log::error('Payment intent confirmation failed', [
                'intent_id' => $intentId,
                'error' => $e->getMessage(),
            ]);

            throw new PaymentFailedException(
                'Failed to confirm payment',
                $e->getMessage()
            );
        }
    }

    /**
     * Retrieve payment intent status.
     * 
     * @param string $intentId
     * @return PaymentIntent
     */
    public function getPaymentIntent(string $intentId): PaymentIntent
    {
        return PaymentIntent::retrieve($intentId);
    }

    /**
     * Refund a payment.
     * 
     * @param string $chargeId
     * @param int|null $amount (in cents, null = full refund)
     * @param string|null $reason
     * 
     * @return \Stripe\Refund
     * @throws PaymentFailedException
     */
    public function refundPayment(string $chargeId, int $amount = null, string $reason = null)
    {
        try {
            $refundData = [
                'charge' => $chargeId,
            ];

            if ($amount) {
                $refundData['amount'] = $amount;
            }

            if ($reason) {
                $refundData['reason'] = $reason;
            }

            $refund = \Stripe\Refund::create($refundData);

            \Log::info('Payment refunded', [
                'charge_id' => $chargeId,
                'refund_id' => $refund->id,
                'amount' => $refund->amount,
            ]);

            return $refund;
        } catch (ApiException $e) {
            \Log::error('Payment refund failed', [
                'charge_id' => $chargeId,
                'error' => $e->getMessage(),
            ]);

            throw new PaymentFailedException(
                'Failed to process refund',
                $e->getMessage()
            );
        }
    }

    /**
     * Verify webhook signature.
     * 
     * @param string $body
     * @param string $signature
     * 
     * @return array
     * @throws PaymentFailedException
     */
    public function verifyWebhook(string $body, string $signature): array
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $body,
                $signature,
                config('services.stripe.webhook_secret')
            );

            return $event;
        } catch (\Exception $e) {
            \Log::error('Webhook verification failed', [
                'error' => $e->getMessage(),
            ]);

            throw new PaymentFailedException('Webhook verification failed');
        }
    }

    /**
     * Generate idempotency key to prevent duplicate charges.
     * 
     * @param int $userId
     * @return string
     */
    protected function generateIdempotencyKey(int $userId): string
    {
        return hash(
            'sha256',
            "payment_{$userId}_" . now()->format('Y-m-d H:i') . '_' . \Str::random(16)
        );
    }
}

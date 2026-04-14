<?php

namespace App\Services;

/**
 * Mock Payment Service
 * 
 * Provides mock payment processing for testing and development.
 * Simulates payment intents and confirmations without external dependencies.
 */
class MockPaymentService
{
    /**
     * Create a mock payment intent.
     * 
     * @param int $userId
     * @param float $amount Amount in cents (e.g., 1000 = $10.00)
     * @param string $description
     * @param array $metadata
     * @param string|null $idempotencyKey
     * 
     * @return array
     */
    public function createPaymentIntent(
        int $userId,
        float $amount,
        string $description,
        array $metadata = [],
        string $idempotencyKey = null
    ): array {
        $idempotencyKey = $idempotencyKey ?? $this->generateIdempotencyKey($userId);
        $intentId = 'pi_' . fake()->numerify('####################');
        $clientSecret = $intentId . '_secret_' . fake()->numerify('####################');

        \Log::info('Mock payment intent created', [
            'intent_id' => $intentId,
            'user_id' => $userId,
            'amount' => $amount,
        ]);

        return [
            'id' => $intentId,
            'client_secret' => $clientSecret,
            'amount' => $amount,
            'currency' => 'usd',
            'status' => 'requires_payment_method',
            'metadata' => $metadata,
        ];
    }

    /**
     * Confirm a mock payment intent.
     * 
     * @param string $intentId
     * @return array
     */
    public function confirmPaymentIntent(string $intentId): array
    {
        $isSuccess = !str_contains($intentId, 'failed');

        $status = $isSuccess ? 'succeeded' : 'requires_action';
        $chargeId = $isSuccess ? 'ch_' . fake()->numerify('####################') : null;

        \Log::info('Mock payment intent confirmed', [
            'intent_id' => $intentId,
            'status' => $status,
        ]);

        return [
            'id' => $intentId,
            'status' => $status,
            'charges' => [
                'data' => $chargeId ? [['id' => $chargeId]] : [],
            ],
            'last_payment_error' => !$isSuccess ? [
                'message' => 'Mock payment failed for testing purposes',
            ] : null,
        ];
    }

    /**
     * Generate an idempotency key to prevent duplicate charges.
     * 
     * @param int $userId
     * @return string
     */
    protected function generateIdempotencyKey(int $userId): string
    {
        return md5($userId . time() . random_int(1, 99999));
    }

    /**
     * Verify a mock webhook (always returns true).
     * 
     * @param string $payload
     * @param string $signature
     * @return array
     */
    public function verifyWebhook(string $payload, string $signature): array
    {
        return [];
    }
}

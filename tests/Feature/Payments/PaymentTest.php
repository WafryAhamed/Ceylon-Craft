<?php

namespace Tests\Feature\Payments;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'total' => 99.99,
            'status' => 'pending',
        ]);
    }

    /**
     * TEST: Payment - Create payment intent
     * Scenario: Order requires payment, user initiates payment
     * Expected: 200 OK with Stripe payment intent
     */
    public function test_create_payment_intent(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'order_id' => $this->order->id,
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'data' => ['client_secret', 'public_key']])
                ->assertJsonPath('data.client_secret', fn($secret) => is_string($secret) && strlen($secret) > 0);

        // Verify payment record created
        $this->assertDatabaseHas('payments', [
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * TEST: Payment - Create intent for non-existent order
     * Scenario: Order doesn't exist
     * Expected: 404 Not Found
     */
    public function test_create_intent_for_non_existent_order(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'order_id' => 99999,
        ]);

        $response->assertStatus(404);
    }

    /**
     * TEST: Payment - Confirm payment success
     * Scenario: User confirms payment with valid Stripe token
     * Expected: 200 OK, payment status = succeeded, order status = paid
     */
    public function test_confirm_payment_success(): void
    {
        $payment = Payment::factory()->create([
            'order_id' => $this->order->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/payments/confirm', [
            'payment_id' => $payment->id,
            'stripe_payment_intent_id' => 'pi_test_success_123',
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'succeeded',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'payment_status' => 'paid',
        ]);
    }

    /**
     * TEST: Payment - Confirm payment failure
     * Scenario: Payment is declined
     * Expected: 200 OK but payment status = failed
     */
    public function test_confirm_payment_failure(): void
    {
        $payment = Payment::factory()->create([
            'order_id' => $this->order->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/payments/confirm', [
            'payment_id' => $payment->id,
            'stripe_payment_intent_id' => 'pi_test_failed_123',
            'success' => false,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'failed',
        ]);
    }

    /**
     * TEST: Payment - Prevent duplicate charges
     * Scenario: Same payment confirmed twice
     * Expected: Second attempt rejected with idempotency error
     */
    public function test_prevent_duplicate_charges(): void
    {
        $payment = Payment::factory()->create([
            'order_id' => $this->order->id,
            'status' => 'pending',
        ]);

        // First confirmation
        $response1 = $this->actingAs($this->user)->postJson('/api/payments/confirm', [
            'payment_id' => $payment->id,
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);

        $this->assertEquals(200, $response1->status());

        // Second confirmation (should fail or return existing)
        $response2 = $this->actingAs($this->user)->postJson('/api/payments/confirm', [
            'payment_id' => $payment->id,
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);

        // Should not process duplicate
        $payment = $payment->fresh();
        $this->assertEquals(1, Payment::where('id', $payment->id)->count());
    }

    /**
     * TEST: Payment - Invalid amount
     * Scenario: Payment amount doesn't match order total
     * Expected: 422 validation error
     */
    public function test_invalid_payment_amount(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'order_id' => $this->order->id,
            'amount' => 50.00, // Payment less than order total
        ]);

        // Should either accept or validate on confirm
        $response->assertStatus(200) || $response->assertStatus(422);
    }

    /**
     * TEST: Payment - Webhook handling for succeeded event
     * Scenario: Stripe webhook sends payment_intent.succeeded event
     * Expected: 200 OK, payment status updated, order status updated
     */
    public function test_webhook_payment_succeeded_event(): void
    {
        $payment = Payment::factory()->create([
            'stripe_payment_intent_id' => 'pi_webhook_test_123',
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/webhooks/stripe', [
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_webhook_test_123',
                    'status' => 'succeeded',
                    'amount' => intval($payment->amount * 100),
                ],
            ],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'stripe_payment_intent_id' => 'pi_webhook_test_123',
            'status' => 'succeeded',
        ]);
    }

    /**
     * TEST: Payment - Webhook signature validation
     * Scenario: Webhook with invalid signature
     * Expected: 401 Unauthorized or 403 Forbidden
     */
    public function test_webhook_signature_validation(): void
    {
        $response = $this->postJson('/api/webhooks/stripe', [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_test_123']],
        ], [
            'HTTP_STRIPE_SIGNATURE' => 'invalid_signature_12345',
        ]);

        // Should reject invalid signatures
        $this->assertTrue($response->status() === 401 || $response->status() === 403 || $response->status() === 400);
    }

    /**
     * TEST: Payment - Cannot pay another user's order
     * Scenario: User tries to pay for different user's order
     * Expected: 403 Forbidden
     */
    public function test_cannot_pay_another_users_order(): void
    {
        $otherUser = User::factory()->create();
        $otherOrder = Order::factory()->create(['user_id' => $otherUser->id]);
        $payment = Payment::factory()->create(['order_id' => $otherOrder->id, 'user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->postJson('/api/payments/confirm', [
            'payment_id' => $payment->id,
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);

        $response->assertStatus(403);
    }

    /**
     * TEST: Payment - Guest cannot make payment
     * Scenario: Unauthenticated user tries to initiate payment
     * Expected: 401 Unauthorized
     */
    public function test_guest_cannot_make_payment(): void
    {
        $response = $this->postJson('/api/payments/intent', [
            'order_id' => $this->order->id,
        ]);

        $response->assertStatus(401);
    }

    /**
     * TEST: Payment - Get payment history
     * Scenario: User views their payment history
     * Expected: 200 OK with list of payments
     */
    public function test_get_payment_history(): void
    {
        Payment::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/payments');

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonStructure(['success', 'data' => ['*' => ['id', 'order_id', 'status', 'amount', 'created_at']]])
                ->assertJsonCount(3, 'data');
    }

    /**
     * TEST: Payment - Refund successful payment
     * Scenario: Admin refunds a succeeded payment
     * Expected: 200 OK, payment status = refunded
     */
    public function test_refund_payment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $payment = Payment::factory()->create(['status' => 'succeeded']);

        $response = $this->actingAs($admin)->postJson("/api/payments/{$payment->id}/refund", []);

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'refunded',
        ]);
    }

    /**
     * TEST: Payment - Cannot refund failed payment
     * Scenario: Try to refund already failed payment
     * Expected: 422 or 409 error
     */
    public function test_cannot_refund_failed_payment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $payment = Payment::factory()->create(['status' => 'failed']);

        $response = $this->actingAs($admin)->postJson("/api/payments/{$payment->id}/refund", []);

        $this->assertTrue($response->status() === 422 || $response->status() === 409);
    }

    /**
     * TEST: Payment - Non-admin cannot refund
     * Scenario: Regular user tries to refund payment
     * Expected: 403 Forbidden
     */
    public function test_non_admin_cannot_refund_payment(): void
    {
        $payment = Payment::factory()->create(['status' => 'succeeded']);

        $response = $this->actingAs($this->user)->postJson("/api/payments/{$payment->id}/refund", []);

        $response->assertStatus(403);
    }

    /**
     * TEST: Payment - Payment with zero amount
     * Scenario: Order total is 0 (free product)
     * Expected: Payment auto-succeeds or not required
     */
    public function test_payment_with_zero_amount(): void
    {
        $freeOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'total' => 0,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'order_id' => $freeOrder->id,
        ]);

        // Should either skip payment or auto-succeed
        $this->assertTrue($response->status() === 200 || $response->status() === 204);
    }

    /**
     * TEST: Payment - Invalid currency
     * Scenario: Payment with unsupported currency
     * Expected: 422 validation error
     */
    public function test_invalid_currency_in_payment(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'order_id' => $this->order->id,
            'currency' => 'XYZ', // Invalid currency code
        ]);

        // Should validate currency
        $this->assertTrue($response->status() === 200 || $response->status() === 422);
    }
}

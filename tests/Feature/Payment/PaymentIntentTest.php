<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentIntentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test Case QA-053: Create payment intent with valid amount
     */
    public function test_create_payment_intent_with_valid_amount(): void
    {
        Http::fake([
            '*stripe.com*' => Http::response([
                'id' => 'pi_test_success_123',
                'client_secret' => 'pi_test_success_123_secret',
                'status' => 'requires_payment_method',
                'amount' => 9999,
                'currency' => 'usd',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'amount' => 9999, // $99.99
            'currency' => 'usd',
            'order_id' => null,
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure(['success', 'data' => ['client_secret', 'payment_intent_id']])
                ->assertJsonPath('data.payment_intent_id', 'pi_test_success_123');
    }

    /**
     * Test Case QA-054: Create payment intent with amount < $0.50
     */
    public function test_create_payment_intent_with_amount_too_small(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'amount' => 49, // $0.49 (below $0.50 minimum)
            'currency' => 'usd',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-056: Create payment intent with unsupported currency
     */
    public function test_create_payment_intent_with_unsupported_currency(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'amount' => 9999,
            'currency' => 'xxx', // Invalid currency
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-064: Rate limit payment endpoint (11th request in 60s)
     */
    public function test_payment_endpoint_rate_limit(): void
    {
        Http::fake(['*stripe.com*' => Http::response(['id' => 'pi_test'], 200)]);

        // Make 10 requests (within limit)
        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
                'amount' => 9999,
                'currency' => 'usd',
            ]);
            
            if ($i < 9) {
                $this->assertNotEquals(429, $response->status());
            }
        }

        // 11th request should be throttled (429)
        $response = $this->actingAs($this->user)->postJson('/api/payments/intent', [
            'amount' => 9999,
            'currency' => 'usd',
        ]);

        $this->assertEquals(429, $response->status());
    }
}

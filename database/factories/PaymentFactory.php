<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'stripe_payment_intent_id' => 'pi_' . fake()->numerify('####################'),
            'stripe_charge_id' => 'ch_' . fake()->numerify('####################'),
            'stripe_payment_method_id' => 'pm_' . fake()->numerify('####################'),
            'amount' => fake()->randomFloat(2, 10, 10000),
            'currency' => 'usd',
            'status' => 'pending',
            'payment_method_type' => 'stripe',
            'metadata' => [],
        ];
    }

    /**
     * Payment is pending
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Payment succeeded
     */
    public function succeeded()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'succeeded',
                'succeeded_at' => now(),
            ];
        });
    }

    /**
     * Payment failed
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
                'failed_at' => now(),
            ];
        });
    }
}

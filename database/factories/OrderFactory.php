<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => 'pending',
            'total' => $this->faker->numberBetween(1000, 100000) / 100,
            'shipping_address' => $this->faker->address(),
            'shipping_city' => $this->faker->city(),
            'shipping_postal_code' => $this->faker->postcode(),
            'payment_method' => 'stripe',
        ];
    }
}

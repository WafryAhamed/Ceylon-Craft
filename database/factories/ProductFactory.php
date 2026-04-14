<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(1000, 100000) / 100,
            'image' => $this->faker->imageUrl(),
            'stock' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
            'category_id' => null,
        ];
    }
}

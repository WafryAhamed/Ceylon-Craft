<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Handmade Ceramic Vase',
                'price' => 45.99,
                'image' => 'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Wooden Craft Box',
                'price' => 35.50,
                'image' => 'https://images.unsplash.com/photo-1595521624277-d9ef250e3f1c?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Woven Wall Tapestry',
                'price' => 62.00,
                'image' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Hand-Painted Canvas',
                'price' => 55.75,
                'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Leather Craft Journal',
                'price' => 28.99,
                'image' => 'https://images.unsplash.com/photo-1507842217343-583f20270319?w=400&h=400&fit=crop',
            ],
            [
                'name' => 'Bohemian Dream Catcher',
                'price' => 32.50,
                'image' => 'https://images.unsplash.com/photo-1525909002651-b8f576fd611d?w=400&h=400&fit=crop',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

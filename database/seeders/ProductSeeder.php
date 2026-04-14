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
                'slug' => 'handmade-ceramic-vase',
                'description' => 'Beautiful handcrafted ceramic vase with intricate patterns',
                'price' => 45.99,
                'stock' => 15,
                'image' => 'products/ceramic-vase.jpg',
            ],
            [
                'name' => 'Wooden Craft Box',
                'slug' => 'wooden-craft-box',
                'description' => 'Elegant wooden storage box perfect for any room',
                'price' => 35.50,
                'stock' => 20,
                'image' => 'products/wooden-box.jpg',
            ],
            [
                'name' => 'Woven Wall Tapestry',
                'slug' => 'woven-wall-tapestry',
                'description' => 'Traditional hand-woven tapestry with authentic patterns',
                'price' => 62.00,
                'stock' => 10,
                'image' => 'products/wall-tapestry.jpg',
            ],
            [
                'name' => 'Hand-Painted Canvas',
                'slug' => 'hand-painted-canvas',
                'description' => 'Unique hand-painted canvas art piece',
                'price' => 55.75,
                'stock' => 8,
                'image' => 'products/canvas-art.jpg',
            ],
            [
                'name' => 'Leather Craft Journal',
                'slug' => 'leather-craft-journal',
                'description' => 'Premium leather journal for writing and sketching',
                'price' => 28.99,
                'stock' => 25,
                'image' => 'products/leather-journal.jpg',
            ],
            [
                'name' => 'Bohemian Dream Catcher',
                'slug' => 'bohemian-dream-catcher',
                'description' => 'Handmade dream catcher with traditional design',
                'price' => 32.50,
                'stock' => 12,
                'image' => 'products/dream-catcher.jpg',
            ],
            [
                'name' => 'Beaded Necklace',
                'slug' => 'beaded-necklace',
                'description' => 'Colorful handmade beaded necklace',
                'price' => 24.99,
                'stock' => 30,
                'image' => 'products/beaded-necklace.jpg',
            ],
            [
                'name' => 'Wooden Serving Platter',
                'slug' => 'wooden-serving-platter',
                'description' => 'Hand-carved wooden serving platter in traditional design',
                'price' => 48.00,
                'stock' => 14,
                'image' => 'products/wooden-platter.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

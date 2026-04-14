<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Home Decor',
                'slug' => 'home-decor',
                'description' => 'Beautiful handmade decorative items for your home',
            ],
            [
                'name' => 'Art & Crafts',
                'slug' => 'art-crafts',
                'description' => 'Unique artistic pieces and handcrafted art',
            ],
            [
                'name' => 'Jewelry',
                'slug' => 'jewelry',
                'description' => 'Handmade jewelry and accessories',
            ],
            [
                'name' => 'Textiles',
                'slug' => 'textiles',
                'description' => 'Woven and embroidered textile products',
            ],
            [
                'name' => 'Ceramics',
                'slug' => 'ceramics',
                'description' => 'Handcrafted ceramic and pottery items',
            ],
            [
                'name' => 'Woodwork',
                'slug' => 'woodwork',
                'description' => 'Fine wooden handcrafted products',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

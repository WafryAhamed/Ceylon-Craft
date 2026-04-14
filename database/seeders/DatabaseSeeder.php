<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@ceyloncraft.lk',
            'role' => 'admin',
        ]);
        Cart::create(['user_id' => $admin->id]);

        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@ceyloncraft.lk',
            'role' => 'user',
        ]);
        Cart::create(['user_id' => $user->id]);

        // Create more test users
        User::factory(5)->create()->each(function ($user) {
            Cart::create(['user_id' => $user->id]);
        });

        $this->call(ProductSeeder::class);
        $this->call(CategorySeeder::class);
    }
}


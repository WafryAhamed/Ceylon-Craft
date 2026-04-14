<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case QA-004: Unauthorized access to another user's order
     */
    public function test_user_cannot_access_another_users_order(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $order = Order::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->getJson('/api/orders/' . $order->id);

        $response->assertStatus(403);
    }

    /**
     * Test Case QA-073: Create product as regular user (should fail)
     */
    public function test_regular_user_cannot_create_product(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->postJson('/api/products', [
            'name' => 'New Product',
            'price' => 99.99,
            'description' => 'Test product',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test Case QA-080: Admin operations as regular user returns 403
     */
    public function test_regular_user_cannot_access_admin_endpoints(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->getJson('/api/admin/orders');

        $response->assertStatus(403);
    }
}

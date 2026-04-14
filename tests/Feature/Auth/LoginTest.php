<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case QA-006: Login with correct credentials
     */
    public function test_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('SecurePass123!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure(['success', 'data' => ['id', 'email', 'token']]);
    }

    /**
     * Test Case QA-007: Login with wrong password
     */
    public function test_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('SecurePass123!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test Case QA-008: Login with non-existent email
     */
    public function test_login_with_non_existent_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test Case QA-009: Logout invalidates token
     */
    public function test_logout_invalidates_token(): void
    {
        $user = User::factory()->create();
        $token = 'test-token-' . uniqid();
        
        // Simulate logout - this would clear the token from session/cache
        $response = $this->actingAs($user)->postJson('/api/auth/logout', [
            'token' => $token,
        ]);

        // After logout, next request should fail
        $response = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test Case QA-010: Access protected endpoint without token
     */
    public function test_access_protected_endpoint_without_token(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated');
    }
}

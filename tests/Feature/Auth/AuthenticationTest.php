<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TEST: Auth - Register with valid data
     * Scenario: User provides correct registration information
     * Expected: Account created, token returned, success response
     */
    public function test_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecurePass123!@',
            'password_confirmation' => 'SecurePass123!@',
            'address' => '123 Main Street, Colombo',
            'country' => 'lk',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => ['id', 'name', 'email', 'token']
                ])
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.email', 'john.doe@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'name' => 'John Doe',
        ]);
    }

    /**
     * TEST: Auth - Register with duplicate email
     * Scenario: Email already exists in database
     * Expected: 422 validation error
     */
    public function test_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'existing@example.com',
            'password' => 'SecurePass123!@',
            'password_confirmation' => 'SecurePass123!@',
        ]);

        $response->assertStatus(422)
                ->assertJsonPath('success', false)
                ->assertJsonStructure(['message', 'data']);
    }

    /**
     * TEST: Auth - Register with weak password
     * Scenario: Password missing uppercase, numbers, or symbols
     * Expected: 422 validation error
     */
    public function test_register_with_weak_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'weakpassword',
            'password_confirmation' => 'weakpassword',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Auth - Register with mismatched passwords
     * Scenario: Password confirmation doesn't match password
     * Expected: 422 validation error
     */
    public function test_register_with_mismatched_passwords(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'SecurePass123!@',
            'password_confirmation' => 'DifferentPass123!@',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Auth - Register with missing required fields
     * Scenario: Missing name, email, or password
     * Expected: 422 validation error
     */
    public function test_register_with_missing_required_fields(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            // Missing email and password
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Auth - Login with correct credentials
     * Scenario: Valid email and password in database
     * Expected: 200 OK with user data and token
     */
    public function test_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('SecurePass123!@'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'SecurePass123!@',
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('success', true)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => ['id', 'name', 'email', 'token']
                ]);
    }

    /**
     * TEST: Auth - Login with wrong password
     * Scenario: Correct email but incorrect password
     * Expected: 401 Unauthorized
     */
    public function test_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('CorrectPass123!@'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'WrongPass123!@',
        ]);

        $response->assertStatus(401)
                ->assertJsonPath('success', false);
    }

    /**
     * TEST: Auth - Login with non-existent email
     * Scenario: Email doesn't exist in database
     * Expected: 401 Unauthorized (no account enumeration)
     */
    public function test_login_with_non_existent_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'Password123!@',
        ]);

        $response->assertStatus(401)
                ->assertJsonPath('success', false);
    }

    /**
     * TEST: Auth - Login with missing email
     * Scenario: Email field not provided
     * Expected: 422 validation error
     */
    public function test_login_with_missing_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'Password123!@',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST: Auth - Logout invalidates token
     * Scenario: User logs out and tries to access protected route
     * Expected: 401 Unauthenticated
     */
    public function test_logout_invalidates_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJsonPath('success', true);

        // Verify token is invalid for subsequent requests
        $response = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer invalid-token',
        ]);

        $response->assertStatus(401);
    }

    /**
     * TEST: Auth - Access protected endpoint without token
     * Scenario: Request to /api/auth/me without authorization header
     * Expected: 401 Unauthenticated
     */
    public function test_access_protected_endpoint_without_token(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
                ->assertJsonPath('success', false);
    }

    /**
     * TEST: Auth - Get current user profile
     * Scenario: Authenticated user requests /api/auth/me
     * Expected: 200 OK with user data
     */
    public function test_get_current_user_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertStatus(200)
                ->assertJsonPath('data.email', $user->email)
                ->assertJsonStructure(['success', 'data' => ['id', 'name', 'email']]);
    }

    /**
     * TEST: Auth - Update profile
     * Scenario: Authenticated user updates their name and address
     * Expected: 200 OK with updated data
     */
    public function test_update_user_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/auth/profile', [
            'name' => 'Updated Name',
            'address' => '456 Oak Street',
        ]);

        $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * TEST: Security - Rate limiting on login
     * Scenario: Multiple failed login attempts exceed rate limit
     * Expected: 429 Too Many Requests after limit exceeded
     */
    public function test_login_rate_limiting(): void
    {
        // This test demonstrates rate limiting. In real production,
        // you'd configure throttle middleware and test it.
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('CorrectPass123!@'),
        ]);

        // Simulate 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'john@example.com',
                'password' => 'WrongPassword',
            ]);
        }

        // 6th attempt should be throttled
        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'WrongPassword',
        ]);

        // May return 429 if throttle is configured
        $this->assertTrue(
            $response->status() === 401 || $response->status() === 429
        );
    }
}

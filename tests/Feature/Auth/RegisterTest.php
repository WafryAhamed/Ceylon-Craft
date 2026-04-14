<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case QA-001: Register with valid data
     */
    public function test_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'address' => '123 Main Street, City',
            'country' => 'lk',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure(['success', 'data' => ['id', 'email', 'token']])
                ->assertJsonPath('data.email', 'john@example.com');

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /**
     * Test Case QA-002: Register with existing email
     */
    public function test_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'existing@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertStatus(422)
                ->assertJsonPath('message', 'The email has already been taken');
    }

    /**
     * Test Case QA-003: Register with weak password (no symbols)
     */
    public function test_register_with_weak_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123', // All lowercase, missing symbols
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-004: Register with mismatched password confirmation
     */
    public function test_register_with_mismatched_password_confirmation(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'DifferentPass456!',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test Case QA-005: Register missing required fields
     */
    public function test_register_missing_required_fields(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            // Missing email, password
        ]);

        $response->assertStatus(422);
    }
}

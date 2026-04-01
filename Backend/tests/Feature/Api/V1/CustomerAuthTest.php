<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a customer can register with basic fields.
     */
    public function test_customer_can_register(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'customer' => ['id', 'name', 'email'],
            ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test a customer can register with optional fields.
     */
    public function test_customer_can_register_with_optional_fields(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'date_of_birth' => '1995-01-01',
            'gender' => 'female',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Jane Doe',
                'phone' => '1234567890',
                'gender' => 'female',
            ]);
    }

    /**
     * Test a customer can login with valid credentials.
     */
    public function test_customer_can_login(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'customer']);
    }

    /**
     * Test login fails with invalid credentials.
     */
    public function test_customer_login_fails_with_invalid_credentials(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test a customer can get their profile information.
     */
    public function test_customer_can_get_profile(): void
    {
        $customer = Customer::create([
            'name' => 'Auth Customer',
            'email' => 'auth@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $token = $customer->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/customer');

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => 'auth@example.com']);
    }

    /**
     * Test profile access is blocked without a token.
     */
    public function test_profile_access_is_blocked_without_token(): void
    {
        $response = $this->getJson('/api/v1/customer');

        $response->assertStatus(401);
    }

    /**
     * Test a customer can logout.
     */
    public function test_customer_can_logout(): void
    {
        $customer = Customer::create([
            'name' => 'Logout Customer',
            'email' => 'logout@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $token = $customer->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out.']);

        $this->assertCount(0, $customer->tokens);
    }
}

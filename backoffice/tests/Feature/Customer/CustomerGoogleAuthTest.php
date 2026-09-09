<?php

namespace Tests\Feature\Customer;

use App\DTOs\GoogleIdentity;
use App\Models\Customer;
use App\Models\User;
use App\Services\Auth\GoogleIdentityVerifierInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class CustomerGoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_new_customer_can_register_and_login_with_google(): void
    {
        $mockVerifier = Mockery::mock(GoogleIdentityVerifierInterface::class);
        $mockVerifier->shouldReceive('verify')
            ->once()
            ->with('valid-google-id-token')
            ->andReturn(new GoogleIdentity(
                subject: 'google-sub-998877',
                email: 'arya.satria@example.com',
                emailVerified: true,
                name: 'Arya Satria',
                avatarUrl: 'https://lh3.googleusercontent.com/a/mock-avatar'
            ));

        $this->app->instance(GoogleIdentityVerifierInterface::class, $mockVerifier);

        $response = $this->postJson('/api/v1/customers/google', [
            'credential' => 'valid-google-id-token',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.customer.email', 'arya.satria@example.com')
            ->assertJsonPath('data.customer.name', 'Arya Satria')
            ->assertJsonPath('data.customer.membership_tier', 'Silver')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'customer' => ['id', 'name', 'email', 'avatar', 'membership_tier'],
                ],
            ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'arya.satria@example.com',
            'google_id' => 'google-sub-998877',
            'membership_tier' => 'Silver',
            'marketing_opt_in' => true,
            'is_active' => true,
        ]);

        $customer = Customer::where('email', 'arya.satria@example.com')->first();
        $this->assertNotNull($customer->email_verified_at);
        $this->assertNotNull($customer->remember_token);
        $this->assertTrue(Auth::guard('customer')->check());
        $this->assertEquals($customer->id, Auth::guard('customer')->id());
    }

    public function test_existing_customer_with_same_email_is_securely_linked(): void
    {
        // Existing customer registered earlier with email/password
        $existing = Customer::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'phone' => '08123456789',
            'password' => 'password123',
            'membership_tier' => 'Gold',
            'total_spend_amount' => 550000,
            'is_active' => true,
        ]);

        $this->assertNull($existing->google_id);

        $mockVerifier = Mockery::mock(GoogleIdentityVerifierInterface::class);
        $mockVerifier->shouldReceive('verify')
            ->once()
            ->with('google-link-token')
            ->andReturn(new GoogleIdentity(
                subject: 'google-sub-linked-112233',
                email: 'budi.santoso@example.com',
                emailVerified: true,
                name: 'Budi Santoso',
                avatarUrl: 'https://lh3.googleusercontent.com/a/budi-avatar'
            ));

        $this->app->instance(GoogleIdentityVerifierInterface::class, $mockVerifier);

        $response = $this->postJson('/api/v1/customers/google', [
            'credential' => 'google-link-token',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.customer.id', $existing->id);

        // Ensure no duplicate account was created
        $this->assertEquals(1, Customer::where('email', 'budi.santoso@example.com')->count());

        $refreshed = $existing->fresh();
        $this->assertEquals('google-sub-linked-112233', $refreshed->google_id);
        $this->assertNotNull($refreshed->email_verified_at);
        $this->assertEquals('https://lh3.googleusercontent.com/a/budi-avatar', $refreshed->avatar);
    }

    public function test_returning_google_customer_can_login(): void
    {
        $customer = Customer::create([
            'name' => 'Clara Renata',
            'email' => 'clara@example.com',
            'google_id' => 'google-sub-clara-555',
            'membership_tier' => 'Silver',
            'is_active' => true,
        ]);

        $mockVerifier = Mockery::mock(GoogleIdentityVerifierInterface::class);
        $mockVerifier->shouldReceive('verify')
            ->once()
            ->with('clara-returning-token')
            ->andReturn(new GoogleIdentity(
                subject: 'google-sub-clara-555',
                email: 'clara@example.com',
                emailVerified: true,
                name: 'Clara Renata'
            ));

        $this->app->instance(GoogleIdentityVerifierInterface::class, $mockVerifier);

        $response = $this->postJson('/api/v1/customers/google', [
            'credential' => 'clara-returning-token',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.customer.id', $customer->id);

        $this->assertEquals(1, Customer::where('email', 'clara@example.com')->count());
        $this->assertTrue(Auth::guard('customer')->check());
        $this->assertEquals($customer->id, Auth::guard('customer')->id());
    }

    public function test_inactive_or_suspended_customer_is_rejected(): void
    {
        Customer::create([
            'name' => 'Suspended User',
            'email' => 'suspended@example.com',
            'google_id' => 'google-sub-suspended-999',
            'is_active' => false,
        ]);

        $mockVerifier = Mockery::mock(GoogleIdentityVerifierInterface::class);
        $mockVerifier->shouldReceive('verify')
            ->once()
            ->andReturn(new GoogleIdentity(
                subject: 'google-sub-suspended-999',
                email: 'suspended@example.com',
                emailVerified: true,
                name: 'Suspended User'
            ));

        $this->app->instance(GoogleIdentityVerifierInterface::class, $mockVerifier);

        $response = $this->postJson('/api/v1/customers/google', [
            'credential' => 'suspended-token',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);

        $this->assertFalse(Auth::guard('customer')->check());
    }

    public function test_invalid_or_expired_google_token_is_rejected(): void
    {
        $mockVerifier = Mockery::mock(GoogleIdentityVerifierInterface::class);
        $mockVerifier->shouldReceive('verify')
            ->once()
            ->with('expired-jwt-token')
            ->andThrow(new AuthenticationException('Token kredensial Google telah kadaluarsa. Silakan coba masuk kembali.'));

        $this->app->instance(GoogleIdentityVerifierInterface::class, $mockVerifier);

        $response = $this->postJson('/api/v1/customers/google', [
            'credential' => 'expired-jwt-token',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Token kredensial Google telah kadaluarsa. Silakan coba masuk kembali.');

        $this->assertFalse(Auth::guard('customer')->check());
    }

    public function test_malformed_request_fails_validation(): void
    {
        $response = $this->postJson('/api/v1/customers/google', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['credential']);
    }

    public function test_logout_invalidates_session_and_clears_remember_token(): void
    {
        $customer = Customer::create([
            'name' => 'Logout Tester',
            'email' => 'logout@example.com',
            'google_id' => 'google-sub-logout-1',
            'remember_token' => 'mlg_cust_token_to_clear',
            'is_active' => true,
        ]);

        Auth::guard('customer')->login($customer);
        $this->assertTrue(Auth::guard('customer')->check());

        $response = $this->withHeader('Authorization', 'Bearer mlg_cust_token_to_clear')
            ->postJson('/api/v1/customers/logout');

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNull($customer->fresh()->remember_token);
        $this->assertFalse(Auth::guard('customer')->check());
    }

    public function test_google_customer_cannot_access_admin_backoffice(): void
    {
        $customer = Customer::create([
            'name' => 'Regular Buyer',
            'email' => 'buyer@example.com',
            'google_id' => 'google-sub-regular-buyer',
            'is_active' => true,
        ]);

        Auth::guard('customer')->login($customer);

        // The default web guard (for backoffice admin) should remain unauthenticated
        $this->assertFalse(Auth::guard('web')->check());
        $this->assertNull(Auth::guard('web')->user());

        // Ensure customer record is not in users table
        $this->assertDatabaseMissing('users', [
            'email' => 'buyer@example.com',
        ]);
    }
}

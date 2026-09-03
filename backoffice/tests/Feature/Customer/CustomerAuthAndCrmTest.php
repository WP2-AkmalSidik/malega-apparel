<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Enums\UserRole;
use App\Livewire\Customers\CustomerIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerAuthAndCrmTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);
    }

    public function test_customer_can_register_via_storefront_api(): void
    {
        $response = $this->postJson('/api/v1/customers/register', [
            'name' => 'Dimas Bagaskara',
            'email' => 'dimas@example.com',
            'phone' => '081234567890',
            'password' => 'secret123',
            'marketing_opt_in' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'customer' => ['id', 'name', 'email', 'phone', 'membership_tier'],
                ],
            ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'dimas@example.com',
            'membership_tier' => 'Silver',
            'marketing_opt_in' => true,
        ]);
    }

    public function test_customer_can_login_and_fetch_profile(): void
    {
        $customer = Customer::create([
            'name' => 'Sarah Wijaya',
            'email' => 'sarah@example.com',
            'phone' => '081987654321',
            'password' => 'mypassword123',
            'membership_tier' => 'Gold',
            'total_spend_amount' => 750000,
        ]);

        $loginResponse = $this->postJson('/api/v1/customers/login', [
            'email_or_phone' => 'sarah@example.com',
            'password' => 'mypassword123',
        ]);

        $loginResponse->assertOk()
            ->assertJsonPath('success', true);

        $token = $loginResponse->json('data.token');

        $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/customers/me');

        $meResponse->assertOk()
            ->assertJsonPath('data.name', 'Sarah Wijaya')
            ->assertJsonPath('data.membership_tier', 'Gold');
    }

    public function test_customer_can_sync_wishlist(): void
    {
        $customer = Customer::create([
            'name' => 'Rian Pratama',
            'email' => 'rian@example.com',
            'phone' => '081122334455',
            'password' => 'password123',
            'remember_token' => 'test_token_123',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer test_token_123')
            ->postJson('/api/v1/customers/wishlist', [
                'wishlist' => ['mlg-001', 'mlg-006'],
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.wishlist', ['mlg-001', 'mlg-006']);

        $this->assertEquals(['mlg-001', 'mlg-006'], $customer->fresh()->wishlist);
    }

    public function test_admin_can_view_and_export_customers_in_backoffice(): void
    {
        Customer::create([
            'name' => 'Budi VIP',
            'email' => 'budi@vip.com',
            'phone' => '081299998888',
            'membership_tier' => 'VIP Platinum',
            'marketing_opt_in' => true,
            'total_spend_amount' => 2000000,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerIndex::class)
            ->assertSee('Budi VIP')
            ->assertSee('VIP Platinum')
            ->call('exportMarketingCsv')
            ->assertStatus(200);
    }
}

<?php

namespace Tests\Feature\Customers;

use App\Actions\Customers\CreateCustomerAction;
use App\Enums\UserRole;
use App\Livewire\Customers\CustomerIndex;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->customer = app(CreateCustomerAction::class)->execute([
            'name' => 'Bambang Sudarmono',
            'email' => 'bambang@malega.id',
            'phone' => '08123456789',
        ]);
    }

    public function test_guest_is_redirected_from_customers_to_login(): void
    {
        $response = $this->get(route('customers.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_staff_can_view_customers_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('customers.index'));

        $response->assertOk()
            ->assertSee('Pelanggan & Kontak')
            ->assertSee('Bambang Sudarmono')
            ->assertSee('bambang@malega.id');
    }

    public function test_can_create_new_customer_via_modal(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CustomerIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Chandra Wijaya')
            ->set('email', 'chandra@wijaya.com')
            ->set('phone', '081999888777')
            ->call('saveCustomer')
            ->assertDispatched('toast')
            ->assertDispatched('close-modal-customer-modal');

        $this->assertDatabaseHas('customers', [
            'name' => 'Chandra Wijaya',
            'email' => 'chandra@wijaya.com',
            'phone' => '081999888777',
        ]);
    }

    public function test_cannot_create_duplicate_customer_email(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CustomerIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Duplicate User')
            ->set('email', 'bambang@malega.id') // already exists!
            ->set('phone', '081111111')
            ->call('saveCustomer')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('customers', [
            'name' => 'Duplicate User',
        ]);
    }

    public function test_can_update_existing_customer(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CustomerIndex::class)
            ->call('openEditModal', $this->customer->id)
            ->set('name', 'Bambang Sudarmono (Updated)')
            ->set('phone', '081999000111')
            ->call('saveCustomer')
            ->assertDispatched('toast')
            ->assertDispatched('close-modal-customer-modal');

        $this->assertDatabaseHas('customers', [
            'id' => $this->customer->id,
            'name' => 'Bambang Sudarmono (Updated)',
            'phone' => '081999000111',
        ]);
    }

    public function test_customer_pagination_renders_max_15_per_page(): void
    {
        $this->actingAs($this->admin);

        for ($i = 1; $i <= 18; $i++) {
            Customer::create([
                'name' => "Customer {$i}",
                'email' => "cust{$i}@malega.id",
                'phone' => "081234000{$i}",
            ]);
        }

        Livewire::test(CustomerIndex::class)
            ->assertViewHas('customers', function ($customers) {
                return $customers->count() === 15;
            });
    }
}

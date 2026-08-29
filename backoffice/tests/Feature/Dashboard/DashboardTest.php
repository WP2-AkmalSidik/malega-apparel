<?php

namespace Tests\Feature\Dashboard;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\CreateProductAction;
use App\Actions\Inventory\AddStockInboundAction;
use App\Actions\Orders\CreateOrderAction;
use App\Actions\Orders\UpdateOrderStatusAction;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Livewire\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_staff_can_view_dashboard(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard Overview')
            ->assertSee('Total Pendapatan (Omzet)')
            ->assertSee('Total Pesanan Masuk');
    }

    public function test_dashboard_computes_live_metrics_from_orders_and_inventory(): void
    {
        $this->actingAs($this->admin);

        $category = app(CreateCategoryAction::class)->execute([
            'name' => 'Kemeja',
            'slug' => 'kemeja',
        ]);

        $product = app(CreateProductAction::class)->execute([
            'category_id' => $category->id,
            'name' => 'Oxford Navy Top',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'DSH-OXF-01', 'title' => 'M', 'price' => 500000],
            ],
        ]);

        $variant = $product->variants->first();

        // Inbound 10 units
        app(AddStockInboundAction::class)->execute($variant->inventoryItem, [
            'quantity' => 10,
        ]);

        // Create paid order of 2 units (Rp 1.000.000)
        $order = app(CreateOrderAction::class)->execute([
            'customer' => ['name' => 'Kolektor Batik', 'email' => 'batik@malega.id', 'phone' => '08123456789'],
            'items' => [['variant_id' => $variant->id, 'quantity' => 2]],
            'address' => [
                'recipient_name' => 'Kolektor Batik',
                'phone' => '08123456789',
                'address_line1' => 'Jl. Sudirman',
                'city' => 'Jakarta',
                'province' => 'DKI',
                'postal_code' => '12190',
            ],
        ]);

        app(UpdateOrderStatusAction::class)->execute($order, [
            'payment_status' => PaymentStatus::Paid,
        ]);

        Livewire::test(Dashboard::class)
            ->assertSee('Rp 1.000.000') // Live revenue
            ->assertSee('Kolektor Batik')
            ->assertSee('Oxford Navy Top');
    }
}

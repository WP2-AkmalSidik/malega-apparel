<?php

namespace Tests\Feature\Inventory;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\CreateProductAction;
use App\Actions\Inventory\AddStockInboundAction;
use App\Actions\Inventory\AdjustStockAction;
use App\Actions\Inventory\FulfillStockAction;
use App\Actions\Inventory\ReleaseStockAction;
use App\Actions\Inventory\ReserveStockAction;
use App\Enums\ProductStatus;
use App\Enums\StockMovementType;
use App\Enums\UserRole;
use App\Livewire\Inventory\InventoryIndex;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Category $category;

    protected Product $product;

    protected InventoryItem $inventoryItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->category = app(CreateCategoryAction::class)->execute([
            'name' => 'Kemeja Formal',
            'slug' => 'kemeja-formal',
        ]);

        $this->product = app(CreateProductAction::class)->execute([
            'category_id' => $this->category->id,
            'name' => 'Signature Oxford Navy',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'MLG-OXF-TEST-M', 'title' => 'M', 'price' => 349000, 'weight_grams' => 250],
            ],
        ]);

        $this->inventoryItem = $this->product->variants->first()->inventoryItem;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('inventory.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_staff_can_view_inventory_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('inventory.index'));

        $response->assertOk()
            ->assertSee('Inventori & Buku Besar Mutasi')
            ->assertSee('MLG-OXF-TEST-M');
    }

    public function test_can_add_inbound_stock_and_record_movement(): void
    {
        $this->actingAs($this->admin);

        app(AddStockInboundAction::class)->execute($this->inventoryItem, [
            'quantity' => 50,
            'reference_note' => 'Kiriman vendor Batch 01',
            'user_id' => $this->admin->id,
        ]);

        $this->assertEquals(50, $this->inventoryItem->fresh()->on_hand);
        $this->assertEquals(50, $this->inventoryItem->fresh()->available);

        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $this->inventoryItem->id,
            'type' => StockMovementType::Inbound->value,
            'quantity_change' => 50,
            'on_hand_after' => 50,
        ]);
    }

    public function test_can_adjust_stock_via_livewire_modal_and_record_movement(): void
    {
        $this->actingAs($this->admin);

        // Set initial stock
        $this->inventoryItem->update(['on_hand' => 20]);

        Livewire::test(InventoryIndex::class)
            ->call('openAdjustmentModal', $this->inventoryItem->id)
            ->set('newOnHand', 35)
            ->set('lowStockThreshold', 8)
            ->set('referenceNote', 'Hasil hitung ulang stok opname fisik')
            ->call('saveAdjustment')
            ->assertDispatched('toast')
            ->assertDispatched('close-modal-stock-adjustment-modal');

        $this->assertEquals(35, $this->inventoryItem->fresh()->on_hand);
        $this->assertEquals(8, $this->inventoryItem->fresh()->low_stock_threshold);

        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $this->inventoryItem->id,
            'type' => StockMovementType::Adjustment->value,
            'quantity_change' => 15,
            'on_hand_before' => 20,
            'on_hand_after' => 35,
        ]);
    }

    public function test_strict_no_negative_stock_rejection(): void
    {
        $this->actingAs($this->admin);

        $this->expectException(ValidationException::class);

        app(AdjustStockAction::class)->execute($this->inventoryItem, [
            'new_on_hand' => -5,
        ]);
    }

    public function test_cannot_adjust_stock_below_active_reserved_quantity(): void
    {
        $this->actingAs($this->admin);

        $this->inventoryItem->update(['on_hand' => 20, 'reserved' => 10]);

        $this->expectException(ValidationException::class);

        app(AdjustStockAction::class)->execute($this->inventoryItem, [
            'new_on_hand' => 5, // Below 10 reserved!
        ]);
    }

    public function test_can_reserve_and_release_stock(): void
    {
        $this->actingAs($this->admin);

        $this->inventoryItem->update(['on_hand' => 30, 'reserved' => 0]);

        // 1. Reserve 5 units
        app(ReserveStockAction::class)->execute($this->inventoryItem, [
            'quantity' => 5,
            'order_id' => 101,
            'reference_note' => 'Pesanan #MLG-101',
        ]);

        $this->assertEquals(30, $this->inventoryItem->fresh()->on_hand);
        $this->assertEquals(5, $this->inventoryItem->fresh()->reserved);
        $this->assertEquals(25, $this->inventoryItem->fresh()->available);

        // 2. Release 5 units
        app(ReleaseStockAction::class)->execute($this->inventoryItem, [
            'quantity' => 5,
            'order_id' => 101,
            'reference_note' => 'Pembatalan pesanan #MLG-101',
        ]);

        $this->assertEquals(0, $this->inventoryItem->fresh()->reserved);
        $this->assertEquals(30, $this->inventoryItem->fresh()->available);
    }

    public function test_cannot_reserve_more_than_available_stock(): void
    {
        $this->actingAs($this->admin);

        $this->inventoryItem->update(['on_hand' => 10, 'reserved' => 8]); // Available is 2

        $this->expectException(ValidationException::class);

        app(ReserveStockAction::class)->execute($this->inventoryItem, [
            'quantity' => 5, // Exceeds 2 available
        ]);
    }

    public function test_can_fulfill_order_and_deduct_stock(): void
    {
        $this->actingAs($this->admin);

        $this->inventoryItem->update(['on_hand' => 20, 'reserved' => 5]);

        app(FulfillStockAction::class)->execute($this->inventoryItem, [
            'quantity' => 5,
            'order_id' => 202,
            'reference_note' => 'Resi JNE #01293847291',
        ]);

        $this->assertEquals(15, $this->inventoryItem->fresh()->on_hand);
        $this->assertEquals(0, $this->inventoryItem->fresh()->reserved);
        $this->assertEquals(15, $this->inventoryItem->fresh()->available);

        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $this->inventoryItem->id,
            'type' => StockMovementType::Fulfilled->value,
            'quantity_change' => -5,
            'on_hand_after' => 15,
        ]);
    }

    public function test_inventory_pagination_renders_max_15_per_page(): void
    {
        $this->actingAs($this->admin);

        for ($i = 1; $i <= 18; $i++) {
            app(CreateProductAction::class)->execute([
                'category_id' => $this->category->id,
                'name' => "Product Extra {$i}",
                'status' => ProductStatus::Active,
                'variants' => [
                    ['sku' => "SKU-EXTRA-{$i}", 'title' => 'Std', 'price' => 100000],
                ],
            ]);
        }

        Livewire::test(InventoryIndex::class)
            ->assertViewHas('inventoryItems', function ($items) {
                return $items->count() === 15;
            });
    }
}

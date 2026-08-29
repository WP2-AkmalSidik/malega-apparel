<?php

namespace Tests\Feature\Catalog;

use App\Actions\Catalog\CreateCategoryAction;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Livewire\Catalog\ProductIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Category $category;

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
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_from_product_management_to_login(): void
    {
        $response = $this->get(route('catalog.products'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_staff_can_view_product_index_page(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Oxford Signature Navy',
            'slug' => 'oxford-signature-navy',
            'status' => ProductStatus::Active,
        ]);

        $product->variants()->create([
            'sku' => 'MLG-OXF-NVY-M',
            'title' => 'Ukuran M',
            'price' => 349000,
            'weight_grams' => 250,
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.products'));

        $response->assertOk()
            ->assertSee('Katalog Produk & SKU')
            ->assertSee('Oxford Signature Navy')
            ->assertSee('MLG-OXF-NVY-M');
    }

    public function test_can_create_product_with_multiple_variants_via_livewire_modal(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ProductIndex::class)
            ->call('openCreateModal')
            ->set('category_id', $this->category->id)
            ->set('name', 'Classic Linen Shirt')
            ->set('status', 'active')
            ->set('variants', [
                [
                    'id' => null,
                    'sku' => 'MLG-LNN-WHT-S',
                    'title' => 'White / S',
                    'price' => 299000,
                    'compare_at_price' => 349000,
                    'cost_price' => 140000,
                    'weight_grams' => 250,
                    'is_active' => true,
                ],
                [
                    'id' => null,
                    'sku' => 'MLG-LNN-WHT-M',
                    'title' => 'White / M',
                    'price' => 299000,
                    'compare_at_price' => 349000,
                    'cost_price' => 140000,
                    'weight_grams' => 250,
                    'is_active' => true,
                ],
            ])
            ->call('saveProduct')
            ->assertDispatched('toast')
            ->assertDispatched('close-modal-product-modal');

        $this->assertDatabaseHas('products', [
            'name' => 'Classic Linen Shirt',
            'slug' => 'classic-linen-shirt',
            'category_id' => $this->category->id,
            'status' => ProductStatus::Active->value,
        ]);

        $this->assertDatabaseHas('product_variants', [
            'sku' => 'MLG-LNN-WHT-S',
            'price' => 299000,
        ]);

        $this->assertDatabaseHas('product_variants', [
            'sku' => 'MLG-LNN-WHT-M',
            'price' => 299000,
        ]);
    }

    public function test_cannot_create_product_with_duplicate_skus_in_same_payload(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ProductIndex::class)
            ->call('openCreateModal')
            ->set('category_id', $this->category->id)
            ->set('name', 'Duplicate SKU Test')
            ->set('variants', [
                ['id' => null, 'sku' => 'DUP-SKU-01', 'title' => 'S', 'price' => 200000, 'weight_grams' => 200, 'is_active' => true],
                ['id' => null, 'sku' => 'DUP-SKU-01', 'title' => 'M', 'price' => 200000, 'weight_grams' => 200, 'is_active' => true],
            ])
            ->call('saveProduct')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('products', ['name' => 'Duplicate SKU Test']);
    }

    public function test_can_update_product_and_modify_variants(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Original Name',
            'slug' => 'original-name',
            'status' => ProductStatus::Active,
        ]);

        $v1 = $product->variants()->create([
            'sku' => 'ORIG-SKU-01',
            'title' => 'Size S',
            'price' => 150000,
            'weight_grams' => 200,
            'is_active' => true,
        ]);

        Livewire::test(ProductIndex::class)
            ->call('openEditModal', $product->id)
            ->set('name', 'Updated Name')
            ->set('variants', [
                [
                    'id' => $v1->id,
                    'sku' => 'ORIG-SKU-01',
                    'title' => 'Size S (Updated)',
                    'price' => 175000,
                    'compare_at_price' => null,
                    'cost_price' => null,
                    'weight_grams' => 220,
                    'is_active' => true,
                ],
                [
                    'id' => null,
                    'sku' => 'NEW-SKU-02',
                    'title' => 'Size M (New)',
                    'price' => 175000,
                    'compare_at_price' => null,
                    'cost_price' => null,
                    'weight_grams' => 220,
                    'is_active' => true,
                ],
            ])
            ->call('saveProduct')
            ->assertDispatched('toast')
            ->assertDispatched('close-modal-product-modal');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $v1->id,
            'title' => 'Size S (Updated)',
            'price' => 175000,
        ]);

        $this->assertDatabaseHas('product_variants', [
            'sku' => 'NEW-SKU-02',
            'title' => 'Size M (New)',
        ]);
    }

    public function test_can_delete_product_and_cascade_variants(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'To Delete Product',
            'slug' => 'to-delete',
            'status' => ProductStatus::Active,
        ]);

        $variant = $product->variants()->create([
            'sku' => 'DEL-SKU-99',
            'title' => 'One Size',
            'price' => 100000,
            'weight_grams' => 150,
            'is_active' => true,
        ]);

        Livewire::test(ProductIndex::class)
            ->set('deletingProductId', $product->id)
            ->call('deleteProduct')
            ->assertDispatched('toast')
            ->assertDispatched('close-confirmation-delete-product-modal');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_variants', ['id' => $variant->id]);
    }

    public function test_product_pagination_renders_max_15_per_page(): void
    {
        $this->actingAs($this->admin);

        for ($i = 1; $i <= 18; $i++) {
            $prod = Product::create([
                'category_id' => $this->category->id,
                'name' => "Product {$i}",
                'slug' => "product-{$i}",
                'status' => ProductStatus::Active,
            ]);

            $prod->variants()->create([
                'sku' => "SKU-PAGE-{$i}",
                'title' => 'Std',
                'price' => 100000 * $i,
                'weight_grams' => 200,
            ]);
        }

        Livewire::test(ProductIndex::class)
            ->assertViewHas('products', function ($products) {
                return $products->count() === 15 && $products->total() === 18;
            });
    }
}

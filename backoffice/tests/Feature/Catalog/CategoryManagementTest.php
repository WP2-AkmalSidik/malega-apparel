<?php

namespace Tests\Feature\Catalog;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\CreateProductAction;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Livewire\Catalog\CategoryIndex;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
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

    public function test_guest_is_redirected_from_category_management_to_login(): void
    {
        $response = $this->get(route('catalog.categories'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_staff_can_view_category_index_page(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Kemeja Formal',
            'slug' => 'kemeja-formal',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.categories'));

        $response->assertOk()
            ->assertSee('Manajemen Kategori')
            ->assertSee('Kemeja Formal');
    }

    public function test_can_create_new_category_via_livewire_modal(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CategoryIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Celana Chino')
            ->set('description', 'Celana katun stretch.')
            ->set('sort_order', 2)
            ->set('is_active', true)
            ->call('saveCategory')
            ->assertDispatched('toast')
            ->assertDispatched('close-modal-category-modal');

        $this->assertDatabaseHas('categories', [
            'name' => 'Celana Chino',
            'slug' => 'celana-chino',
            'sort_order' => 2,
            'is_active' => true,
        ]);
    }

    public function test_can_update_existing_category(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Tops',
            'slug' => 'tops',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->call('openEditModal', $category->id)
            ->set('name', 'Tops & Shirts')
            ->set('sort_order', 5)
            ->call('saveCategory')
            ->assertDispatched('toast')
            ->assertDispatched('close-modal-category-modal');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Tops & Shirts',
            'sort_order' => 5,
        ]);
    }

    public function test_can_toggle_category_active_status(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->call('toggleStatus', $category->id)
            ->assertDispatched('toast');

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_cannot_delete_category_with_existing_products(): void
    {
        $this->actingAs($this->admin);

        $category = app(CreateCategoryAction::class)->execute([
            'name' => 'Outerwear',
            'slug' => 'outerwear',
        ]);

        app(CreateProductAction::class)->execute([
            'category_id' => $category->id,
            'name' => 'Wool Bomber',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'WOL-BMB-M', 'title' => 'M', 'price' => 500000],
            ],
        ]);

        Livewire::test(CategoryIndex::class)
            ->set('deletingCategoryId', $category->id)
            ->call('deleteCategory')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_can_delete_unlinked_category(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create([
            'name' => 'Temporary Category',
            'slug' => 'temp-cat',
            'sort_order' => 99,
        ]);

        Livewire::test(CategoryIndex::class)
            ->set('deletingCategoryId', $category->id)
            ->call('deleteCategory')
            ->assertDispatched('toast')
            ->assertDispatched('close-confirmation-delete-category-modal');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_pagination_renders_max_15_per_page(): void
    {
        $this->actingAs($this->admin);

        for ($i = 1; $i <= 20; $i++) {
            Category::create([
                'name' => "Category {$i}",
                'slug' => "category-{$i}",
                'sort_order' => $i,
            ]);
        }

        Livewire::test(CategoryIndex::class)
            ->assertViewHas('categories', function ($categories) {
                return $categories->count() === 15 && $categories->total() === 20;
            });
    }
}

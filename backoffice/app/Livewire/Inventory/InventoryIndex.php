<?php

namespace App\Livewire\Inventory;

use App\Actions\Inventory\AdjustStockAction;
use App\Models\Category;
use App\Models\InventoryItem;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Stok & Buku Besar Mutasi | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class InventoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $tabFilter = 'all'; // all, low, out

    public string $categoryFilter = 'all';

    // Stock Adjustment Modal Form State
    public ?int $adjustingItemId = null;

    public string $adjustingItemSku = '';

    public string $adjustingItemTitle = '';

    public string $adjustingProductName = '';

    public int $currentOnHand = 0;

    public int $currentReserved = 0;

    public int $newOnHand = 0;

    public int $lowStockThreshold = 5;

    public string $referenceNote = '';

    // Stock Ledger Movement Modal State
    public ?int $viewingItemId = null;

    public string $viewingItemSku = '';

    public string $viewingProductName = '';

    /**
     * Define the custom pagination view.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.custom';
    }

    /**
     * Reset pagination when search or filters change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTabFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Set quick filter tab.
     */
    public function setTab(string $tab): void
    {
        $this->tabFilter = $tab;
        $this->resetPage();
    }

    /**
     * Open Stock Adjustment / Opname modal.
     */
    public function openAdjustmentModal(int $id): void
    {
        $this->resetValidation();
        $item = InventoryItem::with(['variant.product'])->findOrFail($id);

        $this->adjustingItemId = $item->id;
        $this->adjustingItemSku = $item->variant->sku;
        $this->adjustingItemTitle = $item->variant->title;
        $this->adjustingProductName = $item->variant->product->name;
        $this->currentOnHand = $item->on_hand;
        $this->currentReserved = $item->reserved;
        $this->newOnHand = $item->on_hand;
        $this->lowStockThreshold = $item->low_stock_threshold;
        $this->referenceNote = 'Penyesuaian stok opname fisik gudang';

        $this->dispatch('open-modal-stock-adjustment-modal');
    }

    /**
     * Save stock adjustment with pessimistic locking & immutable audit logging.
     */
    public function saveAdjustment(AdjustStockAction $adjustAction): void
    {
        $this->validate([
            'newOnHand' => ['required', 'integer', 'min:0'],
            'lowStockThreshold' => ['required', 'integer', 'min:1'],
            'referenceNote' => ['required', 'string', 'max:255'],
        ]);

        if (! $this->adjustingItemId) {
            return;
        }

        $item = InventoryItem::findOrFail($this->adjustingItemId);

        try {
            $adjustAction->execute($item, [
                'new_on_hand' => $this->newOnHand,
                'low_stock_threshold' => $this->lowStockThreshold,
                'reference_note' => $this->referenceNote,
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Stok Disesuaikan',
                'message' => "Stok SKU '{$this->adjustingItemSku}' berhasil diperbarui menjadi {$this->newOnHand} unit.",
            ]);

            $this->dispatch('close-modal-stock-adjustment-modal');
            $this->reset(['adjustingItemId', 'adjustingItemSku', 'adjustingItemTitle', 'adjustingProductName', 'currentOnHand', 'currentReserved', 'newOnHand', 'referenceNote']);
        } catch (ValidationException $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Gagal Menyesuaikan Stok',
                'message' => $e->validator->errors()->first('new_on_hand') ?? 'Penyesuaian stok ditolak sistem.',
            ]);
        }
    }

    /**
     * Open stock movement ledger history modal.
     */
    public function openLedgerModal(int $id): void
    {
        $item = InventoryItem::with(['variant.product'])->findOrFail($id);

        $this->viewingItemId = $item->id;
        $this->viewingItemSku = $item->variant->sku;
        $this->viewingProductName = $item->variant->product->name;

        $this->dispatch('open-modal-stock-ledger-modal');
    }

    public function render()
    {
        $categories = Category::active()->orderBy('name')->get();

        $query = InventoryItem::with(['variant.product.category'])
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->whereHas('variant', function ($v) use ($term) {
                    $v->where('sku', 'like', $term)
                        ->orWhere('title', 'like', $term)
                        ->orWhereHas('product', fn ($p) => $p->where('name', 'like', $term));
                });
            })
            ->when($this->categoryFilter !== 'all', function ($q) {
                $q->whereHas('variant.product', fn ($p) => $p->where('category_id', $this->categoryFilter));
            })
            ->when($this->tabFilter === 'low', fn ($q) => $q->lowStock())
            ->when($this->tabFilter === 'out', fn ($q) => $q->outOfStock())
            ->latest('updated_at');

        $inventoryItems = $query->paginate(15);

        // Overall Counts for Quick Tab Badges
        $totalItemsCount = InventoryItem::count();
        $lowStockCount = InventoryItem::lowStock()->count();
        $outOfStockCount = InventoryItem::outOfStock()->count();

        // Selected item movements if ledger modal is active
        $movements = $this->viewingItemId
            ? InventoryItem::find($this->viewingItemId)?->movements()->with('user')->take(30)->get() ?? collect()
            : collect();

        return view('livewire.inventory.inventory-index', [
            'inventoryItems' => $inventoryItems,
            'categories' => $categories,
            'totalItemsCount' => $totalItemsCount,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'movements' => $movements,
        ]);
    }
}

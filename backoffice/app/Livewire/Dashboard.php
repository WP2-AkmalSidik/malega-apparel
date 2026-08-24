<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Backoffice Dashboard | Malega Apparel')]
class Dashboard extends Component
{
    public string $activeTab = 'overview';
    public string $searchQuery = '';
    public string $selectedCategory = 'all';
    public string $timeRange = 'month';

    // Form state for adding new product
    public bool $showAddModal = false;
    public string $newProductName = '';
    public string $newProductCategory = 'T-Shirt';
    public string $newProductSku = '';
    public int $newProductPrice = 0;
    public int $newProductStock = 0;

    // Flash notification message
    public ?string $notificationMessage = null;

    // Mock dataset for Backoffice demonstration
    public array $products = [
        [
            'id' => 1,
            'sku' => 'MLG-TS-001',
            'name' => 'Obsidian Heavyweight Tee 300GSM',
            'category' => 'T-Shirt',
            'price' => 289000,
            'stock' => 45,
            'status' => 'In Stock',
            'sales' => 128
        ],
        [
            'id' => 2,
            'sku' => 'MLG-CG-002',
            'name' => 'Tactical Cargo Jogger Pants',
            'category' => 'Bottoms',
            'price' => 429000,
            'stock' => 18,
            'status' => 'In Stock',
            'sales' => 94
        ],
        [
            'id' => 3,
            'sku' => 'MLG-HD-003',
            'name' => 'Minimalist Boxy Fleece Hoodie',
            'category' => 'Outerwear',
            'price' => 489000,
            'stock' => 7,
            'status' => 'Low Stock',
            'sales' => 76
        ],
        [
            'id' => 4,
            'sku' => 'MLG-DN-004',
            'name' => 'Raw Denim Workwear Overshirt',
            'category' => 'Outerwear',
            'price' => 529000,
            'stock' => 12,
            'status' => 'In Stock',
            'sales' => 52
        ],
        [
            'id' => 5,
            'sku' => 'MLG-CP-005',
            'name' => 'Structured Minimal 6-Panel Cap',
            'category' => 'Accessories',
            'price' => 179000,
            'stock' => 34,
            'status' => 'In Stock',
            'sales' => 160
        ],
        [
            'id' => 6,
            'sku' => 'MLG-BG-006',
            'name' => 'Urban Modular Crossbody Bag',
            'category' => 'Accessories',
            'price' => 249000,
            'stock' => 4,
            'status' => 'Low Stock',
            'sales' => 88
        ]
    ];

    public array $recentOrders = [
        [
            'order_id' => 'ORD-2026-9021',
            'customer' => 'Dimas Pratama',
            'items' => 'Obsidian Heavyweight Tee (XL)',
            'total' => 289000,
            'status' => 'Paid',
            'created_at' => '10 menit lalu'
        ],
        [
            'order_id' => 'ORD-2026-9020',
            'customer' => 'Rian Aditya',
            'items' => 'Tactical Cargo Jogger (L) + Cap',
            'total' => 608000,
            'status' => 'Packing',
            'created_at' => '35 menit lalu'
        ],
        [
            'order_id' => 'ORD-2026-9019',
            'customer' => 'Arif Wicaksono',
            'items' => 'Minimalist Boxy Hoodie (L)',
            'total' => 489000,
            'status' => 'Shipped',
            'created_at' => '2 jam lalu'
        ],
        [
            'order_id' => 'ORD-2026-9018',
            'customer' => 'Fajar Nugraha',
            'items' => 'Raw Denim Overshirt (M)',
            'total' => 529000,
            'status' => 'Delivered',
            'created_at' => '4 jam lalu'
        ]
    ];

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openAddModal(): void
    {
        $this->newProductName = '';
        $this->newProductCategory = 'T-Shirt';
        $this->newProductSku = 'MLG-NEW-' . rand(100, 999);
        $this->newProductPrice = 299000;
        $this->newProductStock = 25;
        $this->showAddModal = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
    }

    public function saveProduct(): void
    {
        if (trim($this->newProductName) === '') {
            $this->notificationMessage = 'Nama produk wajib diisi.';
            return;
        }

        $newId = count($this->products) + 1;
        $this->products[] = [
            'id' => $newId,
            'sku' => $this->newProductSku ?: 'MLG-SKU-' . $newId,
            'name' => $this->newProductName,
            'category' => $this->newProductCategory,
            'price' => (int)$this->newProductPrice,
            'stock' => (int)$this->newProductStock,
            'status' => (int)$this->newProductStock > 10 ? 'In Stock' : 'Low Stock',
            'sales' => 0
        ];

        $this->showAddModal = false;
        $this->notificationMessage = 'Produk "' . $this->newProductName . '" berhasil ditambahkan ke inventaris!';
    }

    public function adjustStock(int $id, int $amount): void
    {
        foreach ($this->products as &$prod) {
            if ($prod['id'] === $id) {
                $prod['stock'] = max(0, $prod['stock'] + $amount);
                $prod['status'] = $prod['stock'] > 10 ? 'In Stock' : ($prod['stock'] > 0 ? 'Low Stock' : 'Out of Stock');
                $this->notificationMessage = 'Stok ' . $prod['name'] . ' diperbarui: ' . $prod['stock'] . ' pcs';
                break;
            }
        }
    }

    public function dismissNotification(): void
    {
        $this->notificationMessage = null;
    }

    public function getFilteredProductsProperty(): array
    {
        return array_filter($this->products, function ($prod) {
            $matchesSearch = empty($this->searchQuery) ||
                stripos($prod['name'], $this->searchQuery) !== false ||
                stripos($prod['sku'], $this->searchQuery) !== false;

            $matchesCategory = $this->selectedCategory === 'all' ||
                $prod['category'] === $this->selectedCategory;

            return $matchesSearch && $matchesCategory;
        });
    }

    public function getTotalRevenueProperty(): int
    {
        $total = 0;
        foreach ($this->products as $prod) {
            $total += ($prod['price'] * $prod['sales']);
        }
        return $total;
    }

    public function getTotalStockProperty(): int
    {
        $total = 0;
        foreach ($this->products as $prod) {
            $total += $prod['stock'];
        }
        return $total;
    }

    public function getLowStockCountProperty(): int
    {
        $count = 0;
        foreach ($this->products as $prod) {
            if ($prod['stock'] <= 10) {
                $count++;
            }
        }
        return $count;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'filteredProducts' => $this->filteredProducts,
            'totalRevenue' => $this->totalRevenue,
            'totalStock' => $this->totalStock,
            'lowStockCount' => $this->lowStockCount,
        ]);
    }
}

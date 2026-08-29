<?php

namespace App\Livewire;

use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class Dashboard extends Component
{
    public string $timeRange = '7d';

    public string $searchQuery = '';

    public function setTimeRange(string $range): void
    {
        $this->timeRange = $range;
    }

    public function exportReport(): void
    {
        $this->dispatch('toast', [
            'type' => 'info',
            'title' => 'Ekspor Laporan',
            'message' => 'Laporan rekapitulasi data penjualan & stok berhasil disiapkan.',
        ]);
    }

    /**
     * Live metrics calculations from database.
     */
    #[Computed]
    public function stats(): array
    {
        $totalRevenue = Order::where('payment_status', PaymentStatus::Paid)->sum('grand_total');
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();
        $totalStockAvailable = InventoryItem::all()->sum(fn ($i) => $i->available);

        return [
            'revenue' => [
                'label' => 'Total Pendapatan (Omzet)',
                'value' => 'Rp '.number_format($totalRevenue, 0, ',', '.'),
                'badge' => 'Live',
                'badgeType' => 'emerald',
                'comparison' => 'Omzet pesanan lunas terverifikasi',
            ],
            'orders' => [
                'label' => 'Total Pesanan Masuk',
                'value' => number_format($totalOrders),
                'badge' => Order::pending()->count().' Pending',
                'badgeType' => 'emerald',
                'comparison' => 'Seluruh pesanan tercatat',
            ],
            'customers' => [
                'label' => 'Pelanggan Terdaftar',
                'value' => number_format($totalCustomers),
                'badge' => 'Aktif',
                'badgeType' => 'emerald',
                'comparison' => 'Buku kontak pembeli',
            ],
            'stock' => [
                'label' => 'Stok Fisik Tersedia',
                'value' => number_format($totalStockAvailable).' unit',
                'badge' => InventoryItem::lowStock()->count().' Menipis',
                'badgeType' => InventoryItem::lowStock()->count() > 0 ? 'red' : 'emerald',
                'comparison' => 'Total unit siap dikirim',
            ],
        ];
    }

    /**
     * Top selling products computed from order_items snapshots.
     */
    #[Computed]
    public function topProducts()
    {
        $bestSellers = OrderItem::select('product_name', 'sku', DB::raw('SUM(quantity) as total_sold'), DB::raw('MAX(unit_price) as price'))
            ->groupBy('product_name', 'sku')
            ->orderByDesc('total_sold')
            ->take(4)
            ->get();

        if ($bestSellers->isEmpty()) {
            return Product::with('variants')->take(4)->get()->map(fn ($p) => (object) [
                'product_name' => $p->name,
                'sku' => $p->variants->first()?->sku ?? '-',
                'total_sold' => 0,
                'price' => $p->variants->first()?->price ?? 0,
            ]);
        }

        return $bestSellers;
    }

    /**
     * Recent orders feed with customer and status.
     */
    #[Computed]
    public function recentOrders()
    {
        return Order::with(['customer', 'items', 'address'])
            ->when($this->searchQuery, function ($q) {
                $term = '%'.$this->searchQuery.'%';
                $q->where('order_number', 'like', $term)
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $term));
            })
            ->latest('created_at')
            ->take(5)
            ->get();
    }

    /**
     * Low stock items needing immediate warehouse attention.
     */
    #[Computed]
    public function lowStockItems()
    {
        return InventoryItem::with(['variant.product'])
            ->whereRaw('(on_hand - reserved) <= low_stock_threshold')
            ->take(4)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}

<?php

namespace App\Livewire;

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

    /**
     * Set the active time range filter for metrics.
     */
    public function setTimeRange(string $range): void
    {
        $this->timeRange = $range;
    }

    /**
     * Export current dashboard metrics snapshot.
     */
    public function exportReport(): void
    {
        $this->dispatch('toast', [
            'type' => 'info',
            'title' => 'Ekspor Data',
            'message' => 'Laporan metrik penjualan sedang dipersiapkan untuk diekspor.',
        ]);
    }

    /**
     * Get computed stat cards based on timeRange.
     *
     * @return array<string, array{label: string, value: string, badge: string, badgeType: string, comparison: string}>
     */
    #[Computed]
    public function stats(): array
    {
        return match ($this->timeRange) {
            '30d' => [
                'revenue' => ['label' => 'Total Pendapatan', 'value' => 'Rp 1,12M', 'badge' => '+18,2%', 'badgeType' => 'emerald', 'comparison' => 'vs bulan lalu'],
                'orders' => ['label' => 'Pesanan Baru', 'value' => '4.890', 'badge' => '+8,7%', 'badgeType' => 'emerald', 'comparison' => 'vs bulan lalu'],
                'customers' => ['label' => 'Pelanggan Baru', 'value' => '1.420', 'badge' => '+4,1%', 'badgeType' => 'emerald', 'comparison' => 'vs bulan lalu'],
                'conversion' => ['label' => 'Tingkat Konversi', 'value' => '3,95%', 'badge' => '+0,4%', 'badgeType' => 'emerald', 'comparison' => 'vs bulan lalu'],
            ],
            '1y' => [
                'revenue' => ['label' => 'Total Pendapatan', 'value' => 'Rp 14,8M', 'badge' => '+34,5%', 'badgeType' => 'emerald', 'comparison' => 'vs tahun lalu'],
                'orders' => ['label' => 'Pesanan Baru', 'value' => '58.420', 'badge' => '+22,1%', 'badgeType' => 'emerald', 'comparison' => 'vs tahun lalu'],
                'customers' => ['label' => 'Pelanggan Baru', 'value' => '18.900', 'badge' => '+15,6%', 'badgeType' => 'emerald', 'comparison' => 'vs tahun lalu'],
                'conversion' => ['label' => 'Tingkat Konversi', 'value' => '4,10%', 'badge' => '+0,8%', 'badgeType' => 'emerald', 'comparison' => 'vs tahun lalu'],
            ],
            default => [
                'revenue' => ['label' => 'Total Pendapatan', 'value' => 'Rp 284,5jt', 'badge' => '+12,4%', 'badgeType' => 'emerald', 'comparison' => 'vs minggu lalu'],
                'orders' => ['label' => 'Pesanan Baru', 'value' => '1.248', 'badge' => '+5,1%', 'badgeType' => 'emerald', 'comparison' => 'vs minggu lalu'],
                'customers' => ['label' => 'Pelanggan Baru', 'value' => '356', 'badge' => '-2,3%', 'badgeType' => 'red', 'comparison' => 'vs minggu lalu'],
                'conversion' => ['label' => 'Tingkat Konversi', 'value' => '3,82%', 'badge' => '+0,6%', 'badgeType' => 'emerald', 'comparison' => 'vs minggu lalu'],
            ],
        };
    }

    /**
     * Top selling products feed.
     *
     * @return array<int, array{code: string, name: string, sold: string, price: string}>
     */
    #[Computed]
    public function topProducts(): array
    {
        return [
            ['code' => 'OS', 'name' => 'Oxford Shirt — Navy', 'sold' => '142 terjual', 'price' => 'Rp 349rb'],
            ['code' => 'TC', 'name' => 'Tailored Chino', 'sold' => '98 terjual', 'price' => 'Rp 429rb'],
            ['code' => 'LB', 'name' => 'Leather Belt Classic', 'sold' => '76 terjual', 'price' => 'Rp 219rb'],
            ['code' => 'WC', 'name' => 'Wool Overcoat', 'sold' => '54 terjual', 'price' => 'Rp 1,2jt'],
        ];
    }

    /**
     * Recent orders feed with status and customer information.
     *
     * @return array<int, array{id: string, initials: string, customer: string, products: string, date: string, total: string, status: string, statusType: string}>
     */
    #[Computed]
    public function recentOrders(): array
    {
        $allOrders = [
            [
                'id' => '#MLG-10245',
                'initials' => 'RD',
                'customer' => 'Rina Dewi',
                'products' => 'Oxford Shirt, Tailored Chino',
                'date' => '27 Ags 2026',
                'total' => 'Rp 778rb',
                'status' => 'Dikirim',
                'statusType' => 'gold',
            ],
            [
                'id' => '#MLG-10244',
                'initials' => 'BS',
                'customer' => 'Budi Santoso',
                'products' => 'Wool Overcoat',
                'date' => '27 Ags 2026',
                'total' => 'Rp 1,2jt',
                'status' => 'Diproses',
                'statusType' => 'amber',
            ],
            [
                'id' => '#MLG-10243',
                'initials' => 'SP',
                'customer' => 'Sari Puspita',
                'products' => 'Leather Belt Classic',
                'date' => '26 Ags 2026',
                'total' => 'Rp 219rb',
                'status' => 'Selesai',
                'statusType' => 'emerald',
            ],
            [
                'id' => '#MLG-10242',
                'initials' => 'HN',
                'customer' => 'Hendra Nugraha',
                'products' => 'Oxford Shirt — Navy',
                'date' => '26 Ags 2026',
                'total' => 'Rp 349rb',
                'status' => 'Dibatalkan',
                'statusType' => 'red',
            ],
            [
                'id' => '#MLG-10241',
                'initials' => 'DP',
                'customer' => 'Dian Permata',
                'products' => 'Tailored Chino, Leather Belt',
                'date' => '25 Ags 2026',
                'total' => 'Rp 648rb',
                'status' => 'Dikirim',
                'statusType' => 'gold',
            ],
        ];

        if (empty($this->searchQuery)) {
            return $allOrders;
        }

        $query = strtolower($this->searchQuery);

        return array_filter($allOrders, function ($order) use ($query) {
            return str_contains(strtolower($order['id']), $query)
                || str_contains(strtolower($order['customer']), $query)
                || str_contains(strtolower($order['products']), $query);
        });
    }

    /**
     * Render dashboard view.
     */
    public function render()
    {
        return view('livewire.dashboard');
    }
}

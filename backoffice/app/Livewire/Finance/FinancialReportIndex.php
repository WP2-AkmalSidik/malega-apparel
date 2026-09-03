<?php

namespace App\Livewire\Finance;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Laporan Keuangan & Laba Bersih | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class FinancialReportIndex extends Component
{
    #[Url(history: true)]
    public string $period = 'this_month'; // this_month, last_month, this_quarter, this_year, all

    #[Url(history: true)]
    public ?string $startDate = null;

    #[Url(history: true)]
    public ?string $endDate = null;

    public function mount(): void
    {
        if (! $this->startDate) {
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (! $this->endDate) {
            $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }
    }

    /**
     * Compute comprehensive financial metrics.
     *
     * @return array<string, mixed>
     */
    public function getReportMetricsProperty(): array
    {
        $paymentQuery = Payment::query()->where('status', 'success');
        $this->applyDateScope($paymentQuery);

        $grossSales = (int) $paymentQuery->sum('amount');
        $totalGatewayFee = (int) $paymentQuery->sum('admin_fee');
        $netRevenue = (int) $paymentQuery->sum('net_amount');
        $paidCount = (int) $paymentQuery->count();

        // Calculate product subtotal and shipping totals from related orders
        $orderQuery = Order::query()->where('payment_status', \App\Enums\PaymentStatus::Paid);
        $this->applyOrderDateScope($orderQuery);

        $productSales = (int) $orderQuery->sum('subtotal');
        $shippingCollected = (int) $orderQuery->sum('shipping_total');
        $discountsGiven = (int) $orderQuery->sum('discount_total');

        return [
            'gross_sales' => $grossSales,
            'total_gateway_fee' => $totalGatewayFee,
            'net_revenue' => $netRevenue,
            'paid_count' => $paidCount,
            'product_sales' => $productSales,
            'shipping_collected' => $shippingCollected,
            'discounts_given' => $discountsGiven,
            'avg_order_value' => $paidCount > 0 ? (int) round($grossSales / $paidCount) : 0,
        ];
    }

    /**
     * Group financial performance by payment channel.
     */
    public function getChannelBreakdownProperty(): Collection
    {
        $query = Payment::query()->where('status', 'success');
        $this->applyDateScope($query);

        return $query->selectRaw('
                payment_method,
                payment_method_name,
                count(*) as tx_count,
                sum(amount) as total_gross,
                sum(admin_fee) as total_fee,
                sum(net_amount) as total_net
            ')
            ->groupBy('payment_method', 'payment_method_name')
            ->orderByDesc('total_gross')
            ->get();
    }

    protected function applyDateScope($query): void
    {
        match ($this->period) {
            'this_month' => $query->whereBetween('paid_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]),
            'last_month' => $query->whereBetween('paid_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]),
            'this_quarter' => $query->whereBetween('paid_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()]),
            'this_year' => $query->whereBetween('paid_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]),
            default => null
        };
    }

    protected function applyOrderDateScope($query): void
    {
        match ($this->period) {
            'this_month' => $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]),
            'last_month' => $query->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]),
            'this_quarter' => $query->whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()]),
            'this_year' => $query->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]),
            default => null
        };
    }

    public function render(): View
    {
        return view('livewire.finance.financial-report-index', [
            'metrics' => $this->reportMetrics,
            'channels' => $this->channelBreakdown,
        ]);
    }
}

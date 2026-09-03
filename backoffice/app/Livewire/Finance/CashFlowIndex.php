<?php

namespace App\Livewire\Finance;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Arus Kas & Pendapatan Bersih | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class CashFlowIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $period = 'this_month'; // today, 7days, this_month, last_month, all, custom

    #[Url(history: true)]
    public ?string $startDate = null;

    #[Url(history: true)]
    public ?string $endDate = null;

    #[Url(history: true)]
    public string $search = '';

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

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
     * Get paginated cash flow records.
     */
    public function getCashFlowEntriesProperty(): LengthAwarePaginator
    {
        $query = Payment::with(['order.customer', 'order.address', 'order.items'])
            ->where('status', 'success');

        $this->applyDateScope($query);

        if ($this->search) {
            $term = "%{$this->search}%";
            $query->where(function ($q) use ($term) {
                $q->where('merchant_order_id', 'like', $term)
                    ->orWhere('reference', 'like', $term)
                    ->orWhereHas('order.customer', function ($cq) use ($term) {
                        $cq->where('name', 'like', $term);
                    });
            });
        }

        return $query->latest('paid_at')->paginate(15);
    }

    /**
     * Compute total cash flow summary.
     *
     * @return array<string, int>
     */
    public function getSummaryProperty(): array
    {
        $query = Payment::query()->where('status', 'success');
        $this->applyDateScope($query);

        $gross = (int) $query->sum('amount');
        $gatewayFee = (int) $query->sum('admin_fee');
        $net = (int) $query->sum('net_amount');

        return [
            'gross_income' => $gross,
            'gateway_fee' => $gatewayFee,
            'net_revenue' => $net,
            'transaction_count' => (int) $query->count(),
            'avg_order_value' => $query->count() > 0 ? (int) round($gross / $query->count()) : 0,
        ];
    }

    protected function applyDateScope($query): void
    {
        match ($this->period) {
            'today' => $query->whereDate('paid_at', Carbon::today()),
            '7days' => $query->where('paid_at', '>=', Carbon::now()->subDays(7)),
            'this_month' => $query->whereBetween('paid_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]),
            'last_month' => $query->whereBetween('paid_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]),
            'custom' => $this->startDate && $this->endDate ? $query->whereBetween('paid_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()]) : null,
            default => null
        };
    }

    public function render(): View
    {
        return view('livewire.finance.cash-flow-index', [
            'entries' => $this->cashFlowEntries,
            'summary' => $this->summary,
        ]);
    }
}

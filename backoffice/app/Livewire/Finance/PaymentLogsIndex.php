<?php

namespace App\Livewire\Finance;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Services\Payment\DuitkuService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Logs Pembayaran & Transaksi | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class PaymentLogsIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $statusFilter = 'all'; // all, success, pending, failed

    #[Url(history: true)]
    public string $methodFilter = 'all';

    #[Url(history: true)]
    public string $dateFilter = 'all'; // all, today, 7days, 30days

    public ?int $selectedPaymentId = null;

    public bool $showDetailModal = false;

    public bool $isSyncing = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Live Sync status with Duitku Server.
     */
    public function syncLiveStatus(int $paymentId, DuitkuService $duitkuService): void
    {
        $payment = Payment::with('order')->find($paymentId);

        if (! $payment || ! $payment->order) {
            $this->dispatch('notify', ['message' => 'Data pembayaran tidak ditemukan.', 'type' => 'error']);

            return;
        }

        $res = $duitkuService->checkTransactionStatus($payment->merchant_order_id);

        if ($res['status'] === 'success') {
            $payment->update([
                'status' => 'success',
                'paid_at' => $payment->paid_at ?: now(),
                'reference' => $res['reference'] ?: $payment->reference,
            ]);

            $order = $payment->order;
            $order->payment_status = PaymentStatus::Paid;
            if ($order->order_status === OrderStatus::Pending) {
                $order->order_status = OrderStatus::Processing;
            }
            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => 'payment:unpaid',
                'to_status' => 'payment:paid|order:processing',
                'notes' => 'Status pembayaran disinkronisasi manual dengan server Duitku.',
            ]);

            $this->dispatch('notify', ['message' => "Pembayaran #{$payment->merchant_order_id} BERHASIL diverifikasi dari Duitku!", 'type' => 'success']);
        } elseif ($res['status'] === 'cancelled' || $res['status'] === 'failed') {
            $payment->update(['status' => 'failed']);
            $msg = $res['status_message'] ?? 'Gagal';
            $this->dispatch('notify', ['message' => "Status di Duitku: Dibatalkan / Gagal ({$msg})", 'type' => 'warning']);
        } else {
            $msg = $res['status_message'] ?? 'Pending';
            $this->dispatch('notify', ['message' => "Status di Duitku masih MENUNGGU PEMBAYARAN ({$msg})", 'type' => 'info']);
        }
    }

    /**
     * Open detail modal.
     */
    public function viewDetails(int $paymentId): void
    {
        $this->selectedPaymentId = $paymentId;
        $this->showDetailModal = true;
    }

    public function closeDetails(): void
    {
        $this->showDetailModal = false;
        $this->selectedPaymentId = null;
    }

    /**
     * Get paginated payment logs.
     */
    public function getPaymentsProperty(): LengthAwarePaginator
    {
        return Payment::with(['order.customer', 'order.address'])
            ->when($this->search, function ($q) {
                $term = "%{$this->search}%";
                $q->where('merchant_order_id', 'like', $term)
                    ->orWhere('reference', 'like', $term)
                    ->orWhere('va_number', 'like', $term)
                    ->orWhereHas('order.customer', function ($cq) use ($term) {
                        $cq->where('name', 'like', $term)->orWhere('phone', 'like', $term);
                    });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->methodFilter !== 'all', function ($q) {
                $q->where('payment_method', $this->methodFilter);
            })
            ->when($this->dateFilter !== 'all', function ($q) {
                match ($this->dateFilter) {
                    'today' => $q->whereDate('created_at', Carbon::today()),
                    '7days' => $q->where('created_at', '>=', Carbon::now()->subDays(7)),
                    '30days' => $q->where('created_at', '>=', Carbon::now()->subDays(30)),
                    default => null
                };
            })
            ->latest('created_at')
            ->paginate(15);
    }

    /**
     * Compute KPI statistics.
     *
     * @return array<string, mixed>
     */
    public function getKpiStatsProperty(): array
    {
        $all = Payment::query();

        return [
            'total_count' => (clone $all)->count(),
            'success_count' => (clone $all)->where('status', 'success')->count(),
            'pending_count' => (clone $all)->where('status', 'pending')->count(),
            'failed_count' => (clone $all)->where('status', 'failed')->count(),
            'gross_success' => (clone $all)->where('status', 'success')->sum('amount'),
            'fee_success' => (clone $all)->where('status', 'success')->sum('admin_fee'),
            'net_success' => (clone $all)->where('status', 'success')->sum('net_amount'),
        ];
    }

    public function render(): View
    {
        $selectedPayment = $this->selectedPaymentId ? Payment::with(['order.customer', 'order.items', 'order.address'])->find($this->selectedPaymentId) : null;

        return view('livewire.finance.payment-logs-index', [
            'payments' => $this->payments,
            'kpi' => $this->kpiStats,
            'selectedPayment' => $selectedPayment,
        ]);
    }
}

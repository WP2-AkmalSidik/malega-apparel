<?php

namespace App\Livewire\Public;

use App\Actions\Logistics\SyncBiteshipTrackingAction;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Lacak Status Pengiriman Pesanan | Malega Apparel')]
#[Layout('layouts.public')]
class OrderTracking extends Component
{
    #[Url(as: 'q')]
    public string $searchQuery = '';

    public ?Order $order = null;

    public ?array $liveTrackingData = null;

    public bool $hasSearched = false;

    public bool $isSyncing = false;

    public function mount(?string $order_number = null, ?SyncBiteshipTrackingAction $syncAction = null): void
    {
        $syncAction = $syncAction ?? app(SyncBiteshipTrackingAction::class);

        if ($order_number) {
            $this->searchQuery = trim($order_number);
        }

        if (! empty($this->searchQuery)) {
            $this->performSearch($syncAction);
        }
    }

    public function search(?SyncBiteshipTrackingAction $syncAction = null): void
    {
        $syncAction = $syncAction ?? app(SyncBiteshipTrackingAction::class);

        $this->validate([
            'searchQuery' => ['required', 'string', 'min:3', 'max:100'],
        ], [
            'searchQuery.required' => 'Masukkan nomor pesanan (MLG-...) atau nomor resi pengiriman.',
            'searchQuery.min' => 'Nomor pencarian minimal 3 karakter.',
        ]);

        $this->performSearch($syncAction);
    }

    public function refreshStatus(?SyncBiteshipTrackingAction $syncAction = null): void
    {
        if (! $this->order) {
            return;
        }

        $syncAction = $syncAction ?? app(SyncBiteshipTrackingAction::class);

        $this->isSyncing = true;
        $result = $syncAction->execute($this->order);
        $this->order = $this->order->fresh(['customer', 'items', 'address', 'shipment']);
        $this->liveTrackingData = $result['tracking'] ?? $this->order->shipment?->tracking_history;
        $this->isSyncing = false;

        $this->dispatch('toast', [
            'type' => $result['success'] ? 'success' : 'info',
            'title' => 'Status Pelacakan',
            'message' => $result['message'],
        ]);
    }

    protected function performSearch(SyncBiteshipTrackingAction $syncAction): void
    {
        $this->hasSearched = true;
        $term = trim($this->searchQuery);

        // 1. Search by Order Number
        $foundOrder = Order::with(['customer', 'items', 'address', 'shipment'])
            ->where('order_number', $term)
            ->first();

        // 2. Search by Waybill ID / Resi if not found by order number
        if (! $foundOrder) {
            $shipment = Shipment::where('waybill_id', $term)->first();
            if ($shipment) {
                $foundOrder = $shipment->order()->with(['customer', 'items', 'address', 'shipment'])->first();
            }
        }

        // 3. Search by Address tracking_number
        if (! $foundOrder) {
            $foundOrder = Order::with(['customer', 'items', 'address', 'shipment'])
                ->whereHas('address', fn ($a) => $a->where('tracking_number', $term))
                ->first();
        }

        $this->order = $foundOrder;

        if ($this->order && $this->order->shipment) {
            // Auto sync real-time tracking if shipment exists
            $result = $syncAction->execute($this->order);
            $this->order = $this->order->fresh(['customer', 'items', 'address', 'shipment']);
            $this->liveTrackingData = $result['tracking'] ?? $this->order->shipment?->tracking_history;
        } else {
            $this->liveTrackingData = null;
        }
    }

    /**
     * Compute current progress step index (1 to 5).
     */
    public function getProgressStepProperty(): int
    {
        if (! $this->order) {
            return 1;
        }

        if ($this->order->order_status->value === 'cancelled') {
            return 0;
        }

        $shipmentStatus = strtolower($this->order->shipment?->status ?? '');

        if ($this->order->fulfillment_status->value === 'delivered' || $shipmentStatus === 'delivered') {
            return 5;
        }

        if (in_array($shipmentStatus, ['in_transit', 'dropping_off', 'shipped'])) {
            return 4;
        }

        if (in_array($shipmentStatus, ['picking_up', 'picked', 'allocated', 'confirmed']) || $this->order->shipment?->waybill_id) {
            return 3;
        }

        if ($this->order->payment_status->value === 'paid' || $this->order->order_status->value === 'processing') {
            return 2;
        }

        return 1;
    }

    public function render(): View
    {
        return view('livewire.public.order-tracking');
    }
}

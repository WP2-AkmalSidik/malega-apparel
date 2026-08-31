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

    public string $activeTab = 'timeline'; // 'timeline' | 'package' | 'invoice'

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
     * Synthesize rich comprehensive milestone timeline including order lifecycle and Biteship events.
     *
     * @return array<int, array{title: string, note: string, status: string, timestamp: string, location: string, is_active: boolean, icon: string}>
     */
    public function getComprehensiveMilestonesProperty(): array
    {
        if (! $this->order) {
            return [];
        }

        $events = [];
        $createdAt = $this->order->created_at;
        $courierName = $this->order->shipment?->courier_company ?? $this->order->address?->courier_name ?? 'Kurir';
        $serviceName = $this->order->shipment?->courier_service_name ?? 'REG';

        // 1. Order Placed
        $events[] = [
            'title' => 'Pesanan Berhasil Dibuat',
            'note' => "Pesanan #{$this->order->order_number} berhasil masuk ke sistem Malega Apparel.",
            'status' => 'order_placed',
            'timestamp' => $createdAt->format('d M Y, H:i'),
            'location' => 'Malega Online Storefront',
            'is_active' => false,
            'icon' => 'shopping-bag',
        ];

        // 2. Payment Confirmed
        if ($this->order->payment_status->value === 'paid' || $this->progressStep >= 2) {
            $events[] = [
                'title' => 'Pembayaran Terverifikasi (Lunas)',
                'note' => 'Pembayaran telah diverifikasi secara otomatis. Pesanan diteruskan ke bagian pemenuhan gudang.',
                'status' => 'payment_confirmed',
                'timestamp' => $createdAt->copy()->addMinutes(2)->format('d M Y, H:i'),
                'location' => 'Sistem Otorisasi Pembayaran Malega',
                'is_active' => false,
                'icon' => 'credit-card',
            ];
        }

        // 3. AWB Generated & Ready at Warehouse
        if ($this->order->shipment?->waybill_id || $this->progressStep >= 3) {
            $shipmentTime = $this->order->shipment?->created_at ?? $createdAt->copy()->addMinutes(15);
            $waybill = $this->order->shipment?->waybill_id ?? $this->order->address?->tracking_number;

            $events[] = [
                'title' => "Resi Auto-AWB Terbit ({$courierName})",
                'note' => "Nomor resi resmi {$waybill} ({$courierName} {$serviceName}) telah diterbitkan. Paket selesai dipacking dengan segel eksklusif Malega.",
                'status' => 'awb_created',
                'timestamp' => $shipmentTime->format('d M Y, H:i'),
                'location' => 'Gudang Pusat Malega (Jakarta Pusat 10220)',
                'is_active' => $this->progressStep === 3 && empty($this->liveTrackingData['history']),
                'icon' => 'box',
            ];
        }

        // 4. Raw Biteship API Milestone History (if any)
        $biteshipHistory = $this->liveTrackingData['history'] ?? $this->order->shipment?->tracking_history ?? [];

        foreach ($biteshipHistory as $item) {
            $status = $item['status'] ?? 'update';
            $title = match ($status) {
                'picking_up', 'allocated' => "Kurir Ditugaskan ({$courierName})",
                'picked' => 'Paket Berhasil Di-Pickup Kurir',
                'dropping_off', 'in_transit' => 'Paket Sedang Dalam Perjalanan',
                'delivered' => 'Paket Berhasil Diterima',
                'returned' => 'Paket Retur',
                'cancelled' => 'Pengiriman Dibatalkan',
                default => 'Pembaruan Logistik'
            };

            $icon = match ($status) {
                'picking_up', 'allocated', 'picked' => 'truck',
                'dropping_off', 'in_transit' => 'navigation',
                'delivered' => 'check-circle',
                default => 'refresh'
            };

            $destCity = $this->order->address?->city ?? 'Transit';
            $destRecipient = $this->order->address?->recipient_name ?? 'Penerima';

            $location = match ($status) {
                'picking_up', 'allocated', 'picked' => 'Gudang Malega (Jakarta Pusat)',
                'dropping_off', 'in_transit' => "Sorting Hub Ekspedisi ({$destCity})",
                'delivered' => "{$destRecipient} ({$destCity})",
                default => 'Ekspedisi Logistik'
            };

            $events[] = [
                'title' => $title,
                'note' => $item['note'] ?? 'Status logistik kurir diperbarui.',
                'status' => $status,
                'timestamp' => ! empty($item['updated_at']) ? \Carbon\Carbon::parse($item['updated_at'])->format('d M Y, H:i') : '-',
                'location' => $location,
                'is_active' => false,
                'icon' => $icon,
            ];
        }

        // If in transit and no detailed sub-milestone from biteship sandbox yet
        if ($this->progressStep === 4 && count($biteshipHistory) <= 1) {
            $events[] = [
                'title' => "Paket Diberangkatkan ke Hub Sortir ({$courierName})",
                'note' => "Paket busana Malega telah keluar dari gudang asal dan sedang menuju fasilitas sortir logistik.",
                'status' => 'in_transit',
                'timestamp' => now()->format('d M Y, H:i'),
                'location' => 'Main Sorting Hub Jakarta',
                'is_active' => true,
                'icon' => 'truck',
            ];
        }

        // If delivered
        if ($this->progressStep === 5) {
            $deliveredTime = ($this->order->shipment && $this->order->shipment->delivered_at)
                ? $this->order->shipment->delivered_at->format('d M Y, H:i')
                : now()->format('d M Y, H:i');

            $events[] = [
                'title' => 'Paket Berhasil Diterima',
                'note' => "Paket telah diterima dengan baik oleh {$this->order->address?->recipient_name}.",
                'status' => 'delivered',
                'timestamp' => $deliveredTime,
                'location' => "Alamat Tujuan ({$this->order->address?->city})",
                'is_active' => true,
                'icon' => 'check-circle',
            ];
        }

        // Mark the very last chronological item as active
        if (! empty($events)) {
            $lastIndex = count($events) - 1;
            for ($i = 0; $i < count($events); $i++) {
                $events[$i]['is_active'] = ($i === $lastIndex);
            }
        }

        // Return reverse chronological (latest on top)
        return array_reverse($events);
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

    /**
     * Get estimated delivery time label based on courier type.
     */
    public function getEstimatedDeliveryProperty(): string
    {
        if (! $this->order) {
            return '-';
        }

        $service = strtolower($this->order->shipment?->courier_service_name ?? '');

        return match ($service) {
            'instant' => 'Hari Ini (1-3 Jam)',
            'same_day' => 'Hari Ini (6-8 Jam)',
            'next_day', 'yes', 'best' => '1 Hari Kerja (Besok Tiba)',
            'cargo' => '3 - 5 Hari Kerja',
            default => '1 - 3 Hari Kerja'
        };
    }

    public function render(): View
    {
        return view('livewire.public.order-tracking');
    }
}

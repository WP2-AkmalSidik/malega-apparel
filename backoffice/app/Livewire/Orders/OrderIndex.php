<?php

namespace App\Livewire\Orders;

use App\Actions\Orders\CreateOrderAction;
use App\Actions\Orders\UpdateFulfillmentAction;
use App\Actions\Orders\UpdateOrderStatusAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Pesanan & Transaksi | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class OrderIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $paymentFilter = 'all';

    public string $sortBy = 'latest';

    // Create Order Modal State
    public string $customerName = '';

    public string $customerEmail = '';

    public string $customerPhone = '';

    public array $orderItems = [];

    public ?int $selectedVariantId = null;

    public string $recipientName = '';

    public string $recipientPhone = '';

    public string $addressLine1 = '';

    public string $addressLine2 = '';

    public string $city = 'Jakarta Selatan';

    public string $province = 'DKI Jakarta';

    public string $postalCode = '12190';

    public string $courierName = 'JNE REG';

    public int $shippingTotal = 20000;

    public int $discountTotal = 0;

    public string $notes = '';

    // Order Detail Modal State
    public ?int $selectedOrderId = null;

    public string $fulfillmentCourier = 'JNE';

    public string $fulfillmentTrackingNumber = '';

    /**
     * Define custom pagination template.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.custom';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPaymentFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    /**
     * Open Create Order Modal.
     */
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'customerName', 'customerEmail', 'customerPhone', 'orderItems', 'selectedVariantId',
            'recipientName', 'recipientPhone', 'addressLine1', 'addressLine2', 'notes',
        ]);
        $this->city = 'Jakarta Selatan';
        $this->province = 'DKI Jakarta';
        $this->postalCode = '12190';
        $this->courierName = 'JNE REG';
        $this->shippingTotal = 20000;
        $this->discountTotal = 0;

        $this->dispatch('open-modal-create-order-modal');
    }

    /**
     * Add a variant item to order payload.
     */
    public function addVariantItem(): void
    {
        if (! $this->selectedVariantId) {
            return;
        }

        $variant = ProductVariant::with('product')->find($this->selectedVariantId);
        if (! $variant) {
            return;
        }

        // Check if already in order items
        foreach ($this->orderItems as &$item) {
            if ($item['variant_id'] === $variant->id) {
                $item['quantity']++;

                return;
            }
        }

        $this->orderItems[] = [
            'variant_id' => $variant->id,
            'sku' => $variant->sku,
            'title' => $variant->title,
            'product_name' => $variant->product->name,
            'price' => (int) $variant->price,
            'quantity' => 1,
        ];

        $this->selectedVariantId = null;
    }

    public function removeVariantItem(int $index): void
    {
        unset($this->orderItems[$index]);
        $this->orderItems = array_values($this->orderItems);
    }

    public function incrementQuantity(int $index): void
    {
        $this->orderItems[$index]['quantity']++;
    }

    public function decrementQuantity(int $index): void
    {
        if ($this->orderItems[$index]['quantity'] > 1) {
            $this->orderItems[$index]['quantity']--;
        } else {
            $this->removeVariantItem($index);
        }
    }

    /**
     * Calculate live preview totals for the modal.
     */
    public function getEstimatedSubtotalProperty(): int
    {
        return array_sum(array_map(fn ($item) => $item['price'] * $item['quantity'], $this->orderItems));
    }

    public function getEstimatedGrandTotalProperty(): int
    {
        return max(0, ($this->estimatedSubtotal - $this->discountTotal) + $this->shippingTotal);
    }

    /**
     * Save new order via CreateOrderAction.
     */
    public function saveOrder(CreateOrderAction $createOrder): void
    {
        $this->validate([
            'customerName' => ['required', 'string', 'max:255'],
            'customerEmail' => ['required', 'email', 'max:255'],
            'customerPhone' => ['required', 'string', 'max:30'],
            'orderItems' => ['required', 'array', 'min:1'],
            'recipientName' => ['required', 'string', 'max:255'],
            'recipientPhone' => ['required', 'string', 'max:30'],
            'addressLine1' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postalCode' => ['required', 'string', 'max:20'],
            'shippingTotal' => ['required', 'integer', 'min:0'],
            'discountTotal' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            $itemsPayload = array_map(fn ($item) => [
                'variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
            ], $this->orderItems);

            $order = $createOrder->execute([
                'customer' => [
                    'name' => $this->customerName,
                    'email' => $this->customerEmail,
                    'phone' => $this->customerPhone,
                ],
                'items' => $itemsPayload,
                'address' => [
                    'recipient_name' => $this->recipientName,
                    'phone' => $this->recipientPhone,
                    'address_line1' => $this->addressLine1,
                    'address_line2' => $this->addressLine2 ?: null,
                    'city' => $this->city,
                    'province' => $this->province,
                    'postal_code' => $this->postalCode,
                    'courier_name' => $this->courierName,
                ],
                'shipping_total' => $this->shippingTotal,
                'discount_total' => $this->discountTotal,
                'notes' => $this->notes ?: null,
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Pesanan Dibuat',
                'message' => "Pesanan #{$order->order_number} berhasil dibuat & stok telah direservasi.",
            ]);

            $this->dispatch('close-modal-create-order-modal');
        } catch (ValidationException $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Gagal Membuat Pesanan',
                'message' => $e->validator->errors()->first() ?? 'Terjadi kesalahan pada data pesanan.',
            ]);
        }
    }

    /**
     * Open Order Detail Modal.
     */
    public function openDetailModal(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
        $order = Order::with(['address'])->findOrFail($orderId);
        $this->fulfillmentCourier = $order->address?->courier_name ?? 'JNE';
        $this->fulfillmentTrackingNumber = $order->address?->tracking_number ?? '';

        $this->dispatch('open-modal-order-detail-modal');
    }

    /**
     * Quick Action: Mark Order as Paid.
     */
    public function markAsPaid(UpdateOrderStatusAction $updateStatus): void
    {
        if (! $this->selectedOrderId) {
            return;
        }

        $order = Order::findOrFail($this->selectedOrderId);
        $updateStatus->execute($order, [
            'payment_status' => PaymentStatus::Paid,
            'order_status' => $order->order_status === OrderStatus::Pending ? OrderStatus::Processing : $order->order_status,
            'notes' => 'Pembayaran dikonfirmasi lunas oleh staf backoffice',
            'user_id' => auth()->id(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Status Pembayaran Diperbarui',
            'message' => "Pesanan #{$order->order_number} telah ditandai Lunas.",
        ]);
    }

    /**
     * Quick Action: Mark Order as Completed.
     */
    public function markAsCompleted(UpdateOrderStatusAction $updateStatus): void
    {
        if (! $this->selectedOrderId) {
            return;
        }

        $order = Order::findOrFail($this->selectedOrderId);
        $updateStatus->execute($order, [
            'order_status' => OrderStatus::Completed,
            'notes' => 'Pesanan telah selesai diterima customer',
            'user_id' => auth()->id(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Pesanan Selesai',
            'message' => "Pesanan #{$order->order_number} telah diselesaikan.",
        ]);
    }

    /**
     * Quick Action: Cancel Order (releases stock).
     */
    public function cancelOrder(UpdateOrderStatusAction $updateStatus): void
    {
        if (! $this->selectedOrderId) {
            return;
        }

        $order = Order::findOrFail($this->selectedOrderId);
        $updateStatus->execute($order, [
            'order_status' => OrderStatus::Cancelled,
            'notes' => 'Pesanan dibatalkan oleh staf backoffice & stok dilepas',
            'user_id' => auth()->id(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Pesanan Dibatalkan',
            'message' => "Pesanan #{$order->order_number} dibatalkan & stok reservasi telah dilepas.",
        ]);
    }

    /**
     * Submit Fulfillment with Courier & Tracking Number.
     */
    public function submitFulfillment(UpdateFulfillmentAction $updateFulfillment): void
    {
        $this->validate([
            'fulfillmentCourier' => ['required', 'string', 'max:50'],
            'fulfillmentTrackingNumber' => ['required', 'string', 'max:100'],
        ]);

        if (! $this->selectedOrderId) {
            return;
        }

        $order = Order::findOrFail($this->selectedOrderId);

        try {
            $updateFulfillment->execute($order, [
                'courier_name' => $this->fulfillmentCourier,
                'tracking_number' => $this->fulfillmentTrackingNumber,
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Resi Dikirim & Stok Dipotong',
                'message' => "Pesanan #{$order->order_number} berhasil dikirimkan via {$this->fulfillmentCourier}.",
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Gagal Memproses Resi',
                'message' => $e->validator->errors()->first() ?? 'Terjadi kesalahan.',
            ]);
        }
    }

    public function render()
    {
        $query = Order::with(['customer', 'items', 'address'])
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('order_number', 'like', $term)
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $term)->orWhere('phone', 'like', $term))
                        ->orWhereHas('items', fn ($i) => $i->where('sku', 'like', $term)->orWhere('product_name', 'like', $term));
                });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('order_status', $this->statusFilter);
            })
            ->when($this->paymentFilter !== 'all', function ($q) {
                $q->where('payment_status', $this->paymentFilter);
            })
            ->when($this->sortBy === 'latest', fn ($q) => $q->latest('created_at'))
            ->when($this->sortBy === 'oldest', fn ($q) => $q->oldest('created_at'))
            ->when($this->sortBy === 'highest_total', fn ($q) => $q->orderByDesc('grand_total'))
            ->when($this->sortBy === 'lowest_total', fn ($q) => $q->orderBy('grand_total'));

        $orders = $query->paginate(15);

        // Status Counts
        $totalOrdersCount = Order::count();
        $pendingCount = Order::pending()->count();
        $processingCount = Order::processing()->count();
        $completedCount = Order::completed()->count();
        $cancelledCount = Order::cancelled()->count();

        // Active Selected Order for Detail Modal
        $activeOrder = $this->selectedOrderId
            ? Order::with(['customer', 'items', 'address', 'statusHistories.user'])->find($this->selectedOrderId)
            : null;

        // Available variants for create order selection
        $availableVariants = ProductVariant::active()->with(['product', 'inventoryItem'])->get();

        return view('livewire.orders.order-index', [
            'orders' => $orders,
            'totalOrdersCount' => $totalOrdersCount,
            'pendingCount' => $pendingCount,
            'processingCount' => $processingCount,
            'completedCount' => $completedCount,
            'cancelledCount' => $cancelledCount,
            'activeOrder' => $activeOrder,
            'availableVariants' => $availableVariants,
        ]);
    }
}

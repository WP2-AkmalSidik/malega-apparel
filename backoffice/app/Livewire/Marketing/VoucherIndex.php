<?php

namespace App\Livewire\Marketing;

use App\Enums\VoucherType;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Voucher & Kupon Promosi | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class VoucherIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = 'all';

    public string $statusFilter = 'all';

    // Form State
    public bool $isEditing = false;

    public ?int $editingVoucherId = null;

    public string $code = '';

    public string $name = '';

    public string $description = '';

    public string $type = 'percentage';

    public int $amount = 15;

    public ?int $max_discount_amount = 50000;

    public int $min_order_amount = 200000;

    public ?int $usage_limit_total = 1000;

    public int $usage_limit_per_user = 1;

    public string $valid_from = '';

    public string $valid_until = '';

    public bool $is_active = true;

    public bool $is_public = true;

    public bool $allow_guest = true;

    // View Usages Modal State
    public ?int $viewingVoucherId = null;

    public ?Voucher $viewingVoucher = null;

    // Delete Modal State
    public ?int $deletingVoucherId = null;

    public string $deletingVoucherCode = '';

    public function mount(): void
    {
        $this->valid_from = now()->format('Y-m-d\TH:i');
        $this->valid_until = now()->addMonths(6)->format('Y-m-d\TH:i');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function generateRandomCode(): void
    {
        $prefix = match ($this->type) {
            'free_shipping' => 'FREESHIP',
            'fixed_amount' => 'PROMO',
            default => 'MALEGA',
        };

        $random = strtoupper(Str::random(4));
        $this->code = "{$prefix}{$random}";
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->isEditing = false;
        $this->editingVoucherId = null;
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->type = 'percentage';
        $this->amount = 15;
        $this->max_discount_amount = 50000;
        $this->min_order_amount = 200000;
        $this->usage_limit_total = 1000;
        $this->usage_limit_per_user = 1;
        $this->valid_from = now()->format('Y-m-d\TH:i');
        $this->valid_until = now()->addMonths(6)->format('Y-m-d\TH:i');
        $this->is_active = true;
        $this->is_public = true;
        $this->allow_guest = true;

        $this->generateRandomCode();
        $this->dispatch('open-modal-voucher-modal');
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $voucher = Voucher::findOrFail($id);

        $this->isEditing = true;
        $this->editingVoucherId = $voucher->id;
        $this->code = $voucher->code;
        $this->name = $voucher->name;
        $this->description = $voucher->description ?? '';
        $this->type = $voucher->type->value;
        $this->amount = $voucher->amount;
        $this->max_discount_amount = $voucher->max_discount_amount;
        $this->min_order_amount = $voucher->min_order_amount;
        $this->usage_limit_total = $voucher->usage_limit_total;
        $this->usage_limit_per_user = $voucher->usage_limit_per_user;
        $this->valid_from = $voucher->valid_from ? $voucher->valid_from->format('Y-m-d\TH:i') : '';
        $this->valid_until = $voucher->valid_until ? $voucher->valid_until->format('Y-m-d\TH:i') : '';
        $this->is_active = $voucher->is_active;
        $this->is_public = $voucher->is_public;
        $this->allow_guest = (bool) ($voucher->allow_guest ?? true);

        $this->dispatch('open-modal-voucher-modal');
    }

    public function saveVoucher(): void
    {
        $rules = [
            'code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:vouchers,code,' . $this->editingVoucherId],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'in:percentage,fixed_amount,free_shipping'],
            'amount' => ['required', 'integer', 'min:1'],
            'max_discount_amount' => ['nullable', 'integer', 'min:0'],
            'min_order_amount' => ['required', 'integer', 'min:0'],
            'usage_limit_total' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_user' => ['required', 'integer', 'min:1'],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['boolean'],
            'is_public' => ['boolean'],
            'allow_guest' => ['boolean'],
        ];

        if ($this->type === 'percentage') {
            $rules['amount'][] = 'max:100';
        }

        $validated = $this->validate($rules);
        $validated['code'] = strtoupper(trim($validated['code']));

        if ($this->isEditing && $this->editingVoucherId) {
            $voucher = Voucher::findOrFail($this->editingVoucherId);
            $voucher->update($validated);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Voucher Diperbarui',
                'message' => "Master voucher #{$voucher->code} berhasil disimpan.",
            ]);
        } else {
            $voucher = Voucher::create($validated);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Voucher Dibuat',
                'message' => "Voucher promosi #{$voucher->code} berhasil dibuat.",
            ]);
        }

        $this->dispatch('close-modal-voucher-modal');
    }

    public function toggleStatus(int $id): void
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->is_active = ! $voucher->is_active;
        $voucher->save();

        $statusText = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('toast', [
            'type' => $voucher->is_active ? 'success' : 'info',
            'title' => 'Status Voucher Berubah',
            'message' => "Status voucher #{$voucher->code} berhasil {$statusText}.",
        ]);
    }

    public function openUsagesModal(int $id): void
    {
        $this->viewingVoucherId = $id;
        $this->viewingVoucher = Voucher::with(['usages.order', 'usages.customer'])->findOrFail($id);
        $this->dispatch('open-modal-usages-modal');
    }

    public function confirmDelete(int $id): void
    {
        $voucher = Voucher::findOrFail($id);
        $this->deletingVoucherId = $voucher->id;
        $this->deletingVoucherCode = $voucher->code;
        $this->dispatch('open-confirmation-delete-voucher-modal', [
            'title' => 'Konfirmasi Hapus Voucher',
            'message' => "Apakah Anda yakin ingin menghapus voucher promo '{$voucher->code}'? Kupon ini tidak dapat digunakan lagi di Storefront.",
        ]);
    }

    public function deleteVoucher(): void
    {
        if ($this->deletingVoucherId) {
            $voucher = Voucher::findOrFail($this->deletingVoucherId);
            $code = $voucher->code;
            $voucher->delete();

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Voucher Dihapus',
                'message' => "Voucher #{$code} berhasil dihapus dari sistem.",
            ]);

            $this->deletingVoucherId = null;
            $this->deletingVoucherCode = '';
            $this->dispatch('close-confirmation-delete-voucher-modal');
        }
    }

    public function render()
    {
        $query = Voucher::withCount('usages');

        if (! empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', $term)
                    ->orWhere('name', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->statusFilter === 'expired') {
            $query->whereNotNull('valid_until')->where('valid_until', '<', now());
        } elseif ($this->statusFilter === 'exhausted') {
            $query->whereNotNull('usage_limit_total')->whereColumn('used_count', '>=', 'usage_limit_total');
        }

        $vouchers = $query->orderBy('is_active', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Compute Marketing KPI summary
        $totalVouchers = Voucher::count();
        $activeVouchers = Voucher::where('is_active', true)->count();
        $totalUsages = VoucherUsage::count();
        $totalDiscountGiven = (int) VoucherUsage::sum('discount_amount');

        return view('livewire.marketing.voucher-index', [
            'vouchers' => $vouchers,
            'totalVouchers' => $totalVouchers,
            'activeVouchers' => $activeVouchers,
            'totalUsages' => $totalUsages,
            'totalDiscountGiven' => $totalDiscountGiven,
        ]);
    }
}

<?php

namespace App\Livewire\Customers;

use App\Actions\Customers\CreateCustomerAction;
use App\Actions\Customers\UpdateCustomerAction;
use App\Models\Customer;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Pelanggan & CRM | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $tierFilter = ''; // all, Silver, Gold, VIP Platinum

    public string $marketingFilter = ''; // all, opt_in, opt_out

    public string $sortBy = 'latest'; // latest, spend_desc, orders_desc

    // Form Modal State
    public ?int $customerId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $membershipTier = 'Silver';

    public bool $marketingOptIn = true;

    // History Modal State
    public ?int $viewingCustomerId = null;

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

    public function updatedTierFilter(): void
    {
        $this->resetPage();
    }

    public function updatedMarketingFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset(['customerId', 'name', 'email', 'phone', 'membershipTier', 'marketingOptIn']);
        $this->dispatch('open-modal-customer-modal');
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->membershipTier = $customer->membership_tier ?: 'Silver';
        $this->marketingOptIn = (bool) $customer->marketing_opt_in;

        $this->dispatch('open-modal-customer-modal');
    }

    public function saveCustomer(CreateCustomerAction $createAction, UpdateCustomerAction $updateAction): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'membershipTier' => ['required', 'string'],
            'marketingOptIn' => ['boolean'],
        ]);

        try {
            if ($this->customerId) {
                $customer = Customer::findOrFail($this->customerId);
                $updateAction->execute($customer, [
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'membership_tier' => $this->membershipTier,
                    'marketing_opt_in' => $this->marketingOptIn,
                ]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'title' => 'Pelanggan Diperbarui',
                    'message' => "Data pelanggan '{$this->name}' berhasil disimpan.",
                ]);
            } else {
                $createAction->execute([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'membership_tier' => $this->membershipTier,
                    'marketing_opt_in' => $this->marketingOptIn,
                ]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'title' => 'Pelanggan Ditambahkan',
                    'message' => "Pelanggan baru '{$this->name}' berhasil ditambahkan.",
                ]);
            }

            $this->dispatch('close-modal-customer-modal');
            $this->reset(['customerId', 'name', 'email', 'phone']);
        } catch (ValidationException $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Gagal Menyimpan Pelanggan',
                'message' => $e->validator->errors()->first() ?? 'Terjadi kesalahan.',
            ]);
        }
    }

    public function openHistoryModal(int $id): void
    {
        $this->viewingCustomerId = $id;
        $this->dispatch('open-modal-customer-history-modal');
    }

    /**
     * Export customer audience to CSV for WhatsApp & Email Marketing broadcast.
     */
    public function exportMarketingCsv()
    {
        $query = Customer::query();

        if ($this->tierFilter) {
            $query->where('membership_tier', $this->tierFilter);
        }

        if ($this->marketingFilter === 'opt_in') {
            $query->where('marketing_opt_in', true);
        } elseif ($this->marketingFilter === 'opt_out') {
            $query->where('marketing_opt_in', false);
        }

        $customers = $query->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="malega_customers_marketing_'.date('Ymd_His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama Pelanggan', 'Email', 'No WhatsApp', 'Tier Keanggotaan', 'Total Pesanan', 'Total Belanja (IDR)', 'Marketing Opt-in', 'Tanggal Bergabung']);

            foreach ($customers as $c) {
                fputcsv($file, [
                    $c->id,
                    $c->name,
                    $c->email,
                    $c->phone,
                    $c->membership_tier,
                    $c->total_orders_count,
                    $c->total_spend_amount,
                    $c->marketing_opt_in ? 'YA' : 'TIDAK',
                    $c->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Export CSV Berhasil',
            'message' => 'Daftar kontak pelanggan siap diunduh untuk kebutuhan pemasaran.',
        ]);

        return Response::stream($callback, 200, $headers);
    }

    public function render()
    {
        $query = Customer::withCount('orders')
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            })
            ->when($this->tierFilter, function ($q) {
                $q->where('membership_tier', $this->tierFilter);
            })
            ->when($this->marketingFilter === 'opt_in', function ($q) {
                $q->where('marketing_opt_in', true);
            })
            ->when($this->marketingFilter === 'opt_out', function ($q) {
                $q->where('marketing_opt_in', false);
            });

        match ($this->sortBy) {
            'spend_desc' => $query->orderByDesc('total_spend_amount'),
            'orders_desc' => $query->orderByDesc('total_orders_count'),
            default => $query->latest('id'),
        };

        $customers = $query->paginate(15);
        $totalCustomersCount = Customer::count();
        $vipCount = Customer::where('membership_tier', 'VIP Platinum')->count();
        $marketingSubscribersCount = Customer::where('marketing_opt_in', true)->count();

        $activeCustomer = $this->viewingCustomerId
            ? Customer::with(['orders.items', 'orders.address'])->find($this->viewingCustomerId)
            : null;

        return view('livewire.customers.customer-index', [
            'customers' => $customers,
            'totalCustomersCount' => $totalCustomersCount,
            'vipCount' => $vipCount,
            'marketingSubscribersCount' => $marketingSubscribersCount,
            'activeCustomer' => $activeCustomer,
        ]);
    }
}

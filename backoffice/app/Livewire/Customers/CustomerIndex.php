<?php

namespace App\Livewire\Customers;

use App\Actions\Customers\CreateCustomerAction;
use App\Actions\Customers\UpdateCustomerAction;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Pelanggan | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'latest'; // latest, spend_desc, orders_desc

    // Form Modal State
    public ?int $customerId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

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

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset(['customerId', 'name', 'email', 'phone']);
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

        $this->dispatch('open-modal-customer-modal');
    }

    public function saveCustomer(CreateCustomerAction $createAction, UpdateCustomerAction $updateAction): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        try {
            if ($this->customerId) {
                $customer = Customer::findOrFail($this->customerId);
                $updateAction->execute($customer, [
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
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

    public function render()
    {
        $query = Customer::withCount('orders')
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });

        match ($this->sortBy) {
            'spend_desc' => $query->orderByDesc('total_spend_amount'),
            'orders_desc' => $query->orderByDesc('total_orders_count'),
            default => $query->latest('id'),
        };

        $customers = $query->paginate(15);
        $totalCustomersCount = Customer::count();

        $activeCustomer = $this->viewingCustomerId
            ? Customer::with(['orders.items', 'orders.address'])->find($this->viewingCustomerId)
            : null;

        return view('livewire.customers.customer-index', [
            'customers' => $customers,
            'totalCustomersCount' => $totalCustomersCount,
            'activeCustomer' => $activeCustomer,
        ]);
    }
}

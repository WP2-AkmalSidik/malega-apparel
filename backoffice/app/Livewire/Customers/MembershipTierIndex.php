<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\MembershipTier;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Master Tingkatan Member | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class MembershipTierIndex extends Component
{
    // Form Modal State
    public bool $isEditing = false;
    public ?int $tierId = null;
    public string $name = '';
    public string $slug = '';
    public int $min_spend = 0;
    public ?int $voucher_id = null;
    public string $badge_text = '';
    public string $badge_color = 'slate';
    public string $discount_label = '';
    public string $description = '';
    public int $order = 0;
    public bool $is_active = true;
    public array $perks = [];

    // Delete Confirmation State
    public ?int $deletingTierId = null;

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->isEditing = false;
        $this->tierId = null;
        $this->name = '';
        $this->slug = '';
        $this->min_spend = 0;
        $this->voucher_id = null;
        $this->badge_text = '';
        $this->badge_color = 'slate';
        $this->discount_label = '';
        $this->description = '';
        $this->order = (MembershipTier::max('order') ?? 0) + 1;
        $this->is_active = true;
        $this->perks = [
            ['icon' => 'gift', 'title' => '', 'desc' => ''],
            ['icon' => 'zap', 'title' => '', 'desc' => ''],
        ];

        $this->dispatch('open-modal-tier-modal');
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $tier = MembershipTier::findOrFail($id);

        $this->isEditing = true;
        $this->tierId = $tier->id;
        $this->name = $tier->name;
        $this->slug = $tier->slug;
        $this->min_spend = $tier->min_spend;
        $this->voucher_id = $tier->voucher_id;
        $this->badge_text = $tier->badge_text ?? "★ {$tier->name} Member";
        $this->badge_color = $tier->badge_color ?? 'slate';
        $this->discount_label = $tier->discount_label ?? '';
        $this->description = $tier->description ?? '';
        $this->order = $tier->order;
        $this->is_active = $tier->is_active;
        $this->perks = is_array($tier->perks) ? $tier->perks : [];

        if (empty($this->perks)) {
            $this->perks = [['icon' => 'gift', 'title' => '', 'desc' => '']];
        }

        $this->dispatch('open-modal-tier-modal');
    }

    public function addPerk(): void
    {
        $this->perks[] = ['icon' => 'gift', 'title' => '', 'desc' => ''];
    }

    public function removePerk(int $index): void
    {
        unset($this->perks[$index]);
        $this->perks = array_values($this->perks);
    }

    public function saveTier(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:50', 'unique:membership_tiers,name,' . $this->tierId],
            'min_spend' => ['required', 'integer', 'min:0'],
            'voucher_id' => ['nullable', 'exists:vouchers,id'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'badge_color' => ['required', 'in:slate,amber,gold,emerald,rose'],
            'discount_label' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'perks' => ['nullable', 'array'],
            'perks.*.title' => ['nullable', 'string', 'max:100'],
            'perks.*.desc' => ['nullable', 'string', 'max:255'],
            'perks.*.icon' => ['nullable', 'string', 'max:30'],
        ];

        $validated = $this->validate($rules);
        $validated['slug'] = Str::slug($validated['name']);

        // Filter out empty perks
        if (!empty($validated['perks'])) {
            $validated['perks'] = array_values(array_filter($validated['perks'], function ($p) {
                return !empty(trim($p['title'] ?? ''));
            }));
        }

        if (empty($validated['badge_text'])) {
            $validated['badge_text'] = "★ {$validated['name']} Member";
        }

        if ($this->isEditing && $this->tierId) {
            $tier = MembershipTier::findOrFail($this->tierId);
            $tier->update($validated);

            // Re-sync customer tiers in case name or min_spend changed
            Customer::where('membership_tier_id', $tier->id)->update(['membership_tier' => $tier->name]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Tingkatan Member Diperbarui',
                'message' => "Master tier \"{$tier->name}\" berhasil disimpan.",
            ]);
        } else {
            $tier = MembershipTier::create($validated);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Tingkatan Member Dibuat',
                'message' => "Master tier baru \"{$tier->name}\" berhasil ditambahkan.",
            ]);
        }

        $this->dispatch('close-modal-tier-modal');
    }

    public function toggleStatus(int $id): void
    {
        $tier = MembershipTier::findOrFail($id);
        $tier->is_active = !$tier->is_active;
        $tier->save();

        $statusText = $tier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Status Diperbarui',
            'message' => "Tingkatan member \"{$tier->name}\" telah {$statusText}.",
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingTierId = $id;
        $this->dispatch('open-confirmation-delete-tier-modal');
    }

    public function deleteTier(): void
    {
        if (!$this->deletingTierId) return;

        $tier = MembershipTier::find($this->deletingTierId);
        if ($tier) {
            $name = $tier->name;
            $tier->delete();

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Tier Dihapus',
                'message' => "Tingkatan member \"{$name}\" telah berhasil dihapus.",
            ]);
        }

        $this->deletingTierId = null;
    }

    public function render()
    {
        $tiers = MembershipTier::with('voucher')
            ->withCount('customers')
            ->ordered()
            ->get();

        $vouchers = Voucher::active()
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'name', 'type', 'amount']);

        $totalCustomers = Customer::count();

        return view('livewire.customers.membership-tier-index', [
            'tiers' => $tiers,
            'vouchers' => $vouchers,
            'totalCustomers' => $totalCustomers,
        ]);
    }
}

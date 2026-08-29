<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case WarehouseStaff = 'warehouse_staff';
    case CustomerService = 'customer_service';

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Super Admin',
            self::Manager => 'Operations Manager',
            self::WarehouseStaff => 'Warehouse Staff',
            self::CustomerService => 'Customer Service',
        };
    }

    /**
     * Get the Tailwind badge color class for the role.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Admin => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
            self::Manager => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
            self::WarehouseStaff => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
            self::CustomerService => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
        };
    }

    /**
     * Check if the role can manage staff users.
     */
    public function canManageStaff(): bool
    {
        return $this === self::Admin;
    }

    /**
     * Check if the role can manage the product catalog.
     */
    public function canManageCatalog(): bool
    {
        return in_array($this, [self::Admin, self::Manager], true);
    }

    /**
     * Check if the role can manage inventory adjustments.
     */
    public function canManageInventory(): bool
    {
        return in_array($this, [self::Admin, self::Manager, self::WarehouseStaff], true);
    }

    /**
     * Check if the role can process orders.
     */
    public function canManageOrders(): bool
    {
        return in_array($this, [self::Admin, self::Manager, self::WarehouseStaff, self::CustomerService], true);
    }
}

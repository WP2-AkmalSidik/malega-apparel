<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Super Admin (Owner)
        User::firstOrCreate(
            ['email' => 'admin@malega.id'],
            [
                'name' => 'Malega Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Operations Manager
        User::firstOrCreate(
            ['email' => 'manager@malega.id'],
            [
                'name' => 'Operations Manager',
                'password' => Hash::make('password'),
                'role' => UserRole::Manager,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Warehouse Staff
        User::firstOrCreate(
            ['email' => 'warehouse@malega.id'],
            [
                'name' => 'Gudang Utama Staff',
                'password' => Hash::make('password'),
                'role' => UserRole::WarehouseStaff,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 4. Customer Service
        User::firstOrCreate(
            ['email' => 'cs@malega.id'],
            [
                'name' => 'Customer Service Staff',
                'password' => Hash::make('password'),
                'role' => UserRole::CustomerService,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 5. Seed Catalog (Categories, Products & Variants)
        $this->call(CatalogSeeder::class);
    }
}

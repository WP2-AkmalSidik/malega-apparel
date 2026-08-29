<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_users(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $targetUser = User::factory()->create(['role' => UserRole::WarehouseStaff]);

        $policy = new UserPolicy;

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $targetUser));
        $this->assertTrue($policy->manageAccess($admin, $targetUser));
        $this->assertTrue($policy->delete($admin, $targetUser));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $policy = new UserPolicy;

        $this->assertFalse($policy->delete($admin, $admin));
        $this->assertFalse($policy->manageAccess($admin, $admin));
    }

    public function test_manager_cannot_create_or_delete_staff(): void
    {
        $manager = User::factory()->manager()->create();
        $targetUser = User::factory()->create(['role' => UserRole::WarehouseStaff]);

        $policy = new UserPolicy;

        $this->assertTrue($policy->viewAny($manager));
        $this->assertFalse($policy->create($manager));
        $this->assertFalse($policy->manageAccess($manager, $targetUser));
        $this->assertFalse($policy->delete($manager, $targetUser));
    }

    public function test_warehouse_staff_cannot_manage_staff(): void
    {
        $staff = User::factory()->warehouseStaff()->create();
        $otherStaff = User::factory()->create();

        $policy = new UserPolicy;

        $this->assertFalse($policy->viewAny($staff));
        $this->assertFalse($policy->create($staff));
        $this->assertFalse($policy->update($staff, $otherStaff));
        $this->assertFalse($policy->delete($staff, $otherStaff));
    }
}

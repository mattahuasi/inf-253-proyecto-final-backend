<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// use PHPUnit\Framework\TestCase;

class RolePermissionTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[Test]
    public function can_assign_permissions_to_a_role(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        $role->givePermissionTo($permission);
        $this->assertCount(1, $role->permissions);
    }

    #[Test]
    public function cannot_assign_the_same_permission_twice(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        $role->givePermissionTo($permission);
        $role->givePermissionTo($permission);
        $this->assertCount(1, $role->permissions);
    }
}

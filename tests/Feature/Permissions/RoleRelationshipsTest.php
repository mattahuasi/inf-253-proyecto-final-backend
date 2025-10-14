<?php

namespace Tests\Feature\Permissions;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class RoleRelationshipsTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_role_resources(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['permission:show']);

        $permission = Permission::factory()->create();
        $roles[] = Role::factory()->create();
        $roles[] = Role::factory()->create();

        $permission->givePermissionTo($roles[0]);
        $permission->givePermissionTo($roles[1]);

        $response = $this->getJson(route('api.permissions.roles', $permission));
        $response->assertJsonApiResourceCollection($roles, ['name']);
    }

    #[Test]
    public function can_fetch_role_relationships()
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['permission:show']);

        $r1 = Role::factory()->create();
        $r2 = Role::factory()->create();
        $permission = Permission::factory()->create();
        $permission->givePermissionTo($r1);
        $permission->givePermissionTo($r2);

        $response = $this->getJson(route('api.permissions.relationships.roles.show', $permission));
        $response->assertExactJson([
            'data' => [
                [
                    'id' => (string)$r1->getRouteKey(),
                    'type' => 'roles',
                ],
                [
                    'id' => (string)$r2->getRouteKey(),
                    'type' => 'roles',
                ],
            ]
        ]);
    }
}

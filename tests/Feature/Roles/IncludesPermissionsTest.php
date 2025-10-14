<?php

namespace Tests\Feature\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class IncludesPermissionsTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_include_related_permissions_of_an_role(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:show']);

        $role = Role::factory()->create();

        $p1 = Permission::factory()->create(['name' => 'name A']);
        $p2 = Permission::factory()->create(['name' => 'name B']);
        Permission::factory()->create();

        $role->givePermissionTo($p1);
        $role->givePermissionTo($p2);

        $url = route('api.roles.show', [
            'role' => $role,
            'include' => 'permissions'
        ]);

        $this->getJson($url)
            ->assertJson([
                'data' => [],
                'included' => [
                    [
                        'type' => 'permissions',
                        'id' => $role->permissions[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $role->permissions[0]->name
                        ]
                    ],
                    [
                        'type' => 'permissions',
                        'id' => $role->permissions[1]->getRouteKey(),
                        'attributes' => [
                            'name' => $role->permissions[1]->name
                        ]
                    ],
                ]
            ]);
    }

    #[Test]
    public function can_include_related_permissions_for_multiple_roles(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:index']);

        $role = Role::factory()->create();
        $p1 = Permission::factory()->create(['name' => 'name 1']);
        $p2 = Permission::factory()->create(['name' => 'name 2']);
        $role->givePermissionTo($p1);
        $role->givePermissionTo($p2);

        $role1 = Role::factory()->create();
        $p3 = Permission::factory()->create(['name' => 'name 3']);
        $role1->givePermissionTo($p3);

        $url = route('api.roles.index', [
            'include' => 'permissions'
        ]);

        $this->getJson($url)
            ->assertJson([
                'data' => [],
                'included' => [
                    [
                        'type' => 'permissions',
                        'id' => $role->permissions[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $role->permissions[0]->name
                        ]
                    ],
                    [
                        'type' => 'permissions',
                        'id' => $role->permissions[1]->getRouteKey(),
                        'attributes' => [
                            'name' => $role->permissions[1]->name
                        ]
                    ],
                    [
                        'type' => 'permissions',
                        'id' => $role1->permissions[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $role1->permissions[0]->name
                        ]
                    ],
                ]
            ]);
    }

    #[Test]
    public function cannot_include_unknown_relationships(): void
    {
        $role = Role::factory()->create();
        $this->authenticateUser(['role:index', 'role:show']);

        $url = route('api.roles.show', [
            'role' => $role,
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The included relationship 'unknown' is not allowed in the 'roles' resource.",
                status: "400",
            );

        $url = route('api.roles.index', [
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)->assertJsonApiError(
            title: trans('httpCodes.400'),
            detail: "The included relationship 'unknown' is not allowed in the 'roles' resource.",
            status: "400",
        );
    }
}

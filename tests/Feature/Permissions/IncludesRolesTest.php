<?php

namespace Tests\Feature\Permissions;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class IncludesRolesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_include_related_roles_of_an_permission(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['permission:show']);

        $permission = Permission::factory()->create();

        $r1 = Role::factory()->create(['name' => 'name A']);
        $r2 = Role::factory()->create(['name' => 'name B']);
        Role::factory()->create();

        $permission->givePermissionTo($r1);
        $permission->givePermissionTo($r2);

        $url = route('api.permissions.show', [
            'permission' => $permission,
            'include' => 'roles'
        ]);

        $this->getJson($url)
            ->assertJson([
                'data' => [],
                'included' => [
                    [
                        'type' => 'roles',
                        'id' => $permission->roles[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $permission->roles[0]->name
                        ]
                    ],
                    [
                        'type' => 'roles',
                        'id' => $permission->roles[1]->getRouteKey(),
                        'attributes' => [
                            'name' => $permission->roles[1]->name
                        ]
                    ],
                ]
            ]);
    }

    #[Test]
    public function can_include_related_roles_for_multiple_permissions(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['permission:index']);

        $permission = Permission::factory()->create();
        $r1 = Role::factory()->create(['name' => 'name 1']);
        $r2 = Role::factory()->create(['name' => 'name 2']);
        $permission->givePermissionTo($r1);
        $permission->givePermissionTo($r2);

        $permission1 = Permission::factory()->create();
        $r3 = Role::factory()->create(['name' => 'name 3']);
        $permission1->givePermissionTo($r3);

        $url = route('api.permissions.index', [
            'include' => 'roles'
        ]);

        $this->getJson($url)
            ->assertJson([
                'data' => [],
                'included' => [
                    [
                        'type' => 'roles',
                        'id' => $permission->roles[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $permission->roles[0]->name
                        ]
                    ],
                    [
                        'type' => 'roles',
                        'id' => $permission->roles[1]->getRouteKey(),
                        'attributes' => [
                            'name' => $permission->roles[1]->name
                        ]
                    ],
                    [
                        'type' => 'roles',
                        'id' => $permission1->roles[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $permission1->roles[0]->name
                        ]
                    ],
                ]
            ]);
    }

    #[Test]
    public function cannot_include_unknown_relationships(): void
    {
        $permission = Permission::factory()->create();
        $this->authenticateUser(['permission:index', 'permission:show']);

        $url = route('api.permissions.show', [
            'permission' => $permission,
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The included relationship 'unknown' is not allowed in the 'permissions' resource.",
                status: "400",
            );

        $url = route('api.permissions.index', [
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)->assertJsonApiError(
            title: trans('httpCodes.400'),
            detail: "The included relationship 'unknown' is not allowed in the 'permissions' resource.",
            status: "400",
        );
    }
}

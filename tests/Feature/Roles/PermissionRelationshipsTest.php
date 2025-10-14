<?php

namespace Tests\Feature\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class PermissionRelationshipsTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_permission_resources(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:show']);

        $role = Role::factory()->create();
        $permissions[] = Permission::factory()->create();
        $permissions[] = Permission::factory()->create();
        $role->givePermissionTo($permissions[0]);
        $role->givePermissionTo($permissions[1]);

        $response = $this->getJson(route('api.roles.permissions', $role));
        $response->assertJsonApiResourceCollection($permissions, [
            'name',
            'description',
            'type'
        ]);
    }

    #[Test]
    public function can_fetch_permission_relationships()
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:show']);

        $p1 = Permission::factory()->create();
        $p2 = Permission::factory()->create();
        $role = Role::factory()->create();
        $role->givePermissionTo($p1);
        $role->givePermissionTo($p2);

        $response = $this->getJson(route('api.roles.relationships.permissions.show', $role));
        $response->assertExactJson([
            'data' => [
                [
                    'id' => (string)$p1->getRouteKey(),
                    'type' => 'permissions',
                ],
                [
                    'id' => (string)$p2->getRouteKey(),
                    'type' => 'permissions',
                ],
            ]
        ]);
    }

    #[Test]
    public function can_update_permission_relationships()
    {
        $this->withoutExceptionHandling();
        $this->withoutJsonApiDocumentFormatting();
        $this->authenticateUser(['role:update']);

        $p1 = Permission::factory()->create();
        $role = Role::factory()->create();
        $role->givePermissionTo($p1);

        $p2 = Permission::factory()->create();
        $p3 = Permission::factory()->create();
        $p4 = Permission::factory()->create();

        $data = [
            'data' => [
                [
                    'id' => (string)$p2->getRouteKey(),
                    'type' => 'permissions',
                ],
                [
                    'id' => (string)$p3->getRouteKey(),
                    'type' => 'permissions',
                ],
                [
                    'id' => (string)$p4->getRouteKey(),
                    'type' => 'permissions',
                ],
            ]
        ];

        $url = route('api.roles.relationships.permissions.update', $role);

        $response = $this->patchJson($url, $data);
        $response->assertExactJson([
            'data' => [
                [
                    'id' => (string)$p2->getRouteKey(),
                    'type' => 'permissions',
                ],
                [
                    'id' => (string)$p3->getRouteKey(),
                    'type' => 'permissions',
                ],
                [
                    'id' => (string)$p4->getRouteKey(),
                    'type' => 'permissions',
                ],
            ]
        ]);
        $response->assertOk();
    }

    #[Test]
    public function can_attach_permission_relationships()
    {
        $this->withoutExceptionHandling();
        $this->withoutJsonApiDocumentFormatting();
        $this->authenticateUser(['role:create']);

        $p1 = Permission::factory()->create();
        $role = Role::factory()->create();
        $role->givePermissionTo($p1);

        $p2 = Permission::factory()->create();

        $data = [
            'data' => [
                [
                    'id' => (string)$p2->getRouteKey(),
                    'type' => 'permissions',
                ]
            ]
        ];

        $url = route('api.roles.relationships.permissions.attach', $role);

        $response = $this->postJson($url, $data);
        $response->assertNoContent();

        $this->assertTrue($role->permissions->contains($p1));
        $this->assertTrue($role->permissions->contains($p2));
    }

    #[Test]
    public function can_detach_permission_relationships()
    {
        $this->withoutExceptionHandling();
        $this->withoutJsonApiDocumentFormatting();
        $this->authenticateUser(['role:delete']);

        $p1 = Permission::factory()->create();
        $p2 = Permission::factory()->create();

        $role = Role::factory()->create();
        $role->givePermissionTo($p1);
        $role->givePermissionTo($p2);


        $data = [
            'data' => [
                [
                    'id' => (string)$p1->getRouteKey(),
                    'type' => 'permissions',
                ]
            ]
        ];

        $url = route('api.roles.relationships.permissions.detach', $role);

        $response = $this->deleteJson($url, $data);
        $response->assertNoContent();

        $this->assertFalse($role->permissions->contains($p1));
        $this->assertTrue($role->permissions->contains($p2));
    }


    #[Test]
    public function permissions_must_exist_in_the_database(): void
    {
        $this->withoutJsonApiDocumentFormatting();
        $this->authenticateUser(['role:update', 'role:create', 'role:delete']);

        $role = Role::factory()->create();

        $data = [
            'data' => [
                [
                    'id' => 'no-existing-1',
                    'type' => 'permissions',
                ],
                [
                    'id' => 'no-existing-2',
                    'type' => 'permissions',
                ]
            ]
        ];

        $url = route('api.roles.relationships.permissions.update', $role);
        $response = $this->patchJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonApiValidationErrors('data.0.id');
        $response->assertJsonApiValidationErrors('data.1.id');

        $url = route('api.roles.relationships.permissions.attach', $role);
        $response = $this->postJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonApiValidationErrors('data.0.id');
        $response->assertJsonApiValidationErrors('data.1.id');

        $url = route('api.roles.relationships.permissions.detach', $role);
        $response = $this->deleteJson($url, $data);
        $response->assertStatus(422);
        $response->assertJsonApiValidationErrors('data.0.id');
        $response->assertJsonApiValidationErrors('data.1.id');
    }
}

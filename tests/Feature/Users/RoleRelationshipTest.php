<?php

namespace Tests\Feature\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class RoleRelationshipTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_role_relationship(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $response = $this->getJson(route('api.users.relationships.role.show', $user));
        $response->assertExactJson([
            'data' => [
                'id' => (string)$user->role->getRouteKey(),
                'type' => $user->role->getResourceType()
            ]
        ]);
    }

    #[Test]
    public function can_fetch_the_associated_role_resource(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $response = $this->getJson(route('api.users.role', $user));
        $response->assertJsonApiResource($user->role, [
            'name' => $user->role->name
        ]);
    }

    #[Test]
    public function can_update_role_relationship(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $this->withoutJsonApiDocumentFormatting();

        $url = route('api.users.relationships.role.update', $user);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'roles',
                'id' => (string)$role->getRouteKey(),
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'roles',
                'id' => (string)$role->getRouteKey()
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role_id' => $role->id
        ]);
    }

    #[Test]
    public function role_must_exist_in_database(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();
        $url = route('api.users.relationships.role.update', $user);

        $this->patchJson($url, [
            'data' => [
                'type' => 'roles',
                'id' => 'no-existing',
            ]
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role_id' => $user->role_id
        ]);
    }
}

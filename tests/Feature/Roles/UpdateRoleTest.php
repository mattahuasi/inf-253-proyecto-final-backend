<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UpdateRoleTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_update_roles(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:update']);

        $role = Role::factory()->create();

        $response = $this->patchJson(route('api.roles.update', $role), [
            'name' => 'test to update a role',
        ]);

        $response->assertOk();

        $response->assertJsonApiResource($role, [
            'name' => 'test to update a role'
        ]);
    }

    #[Test]
    public function name_is_required()
    {
        $this->authenticateUser(['role:update']);
        $role = Role::factory()->create();
        $this->patchJson(route('api.roles.update', $role), [
            'name' => null,
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function name_must_be_unique()
    {
        $this->authenticateUser(['role:update']);
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $this->patchJson(route('api.roles.update', $role1),  [
            'name' => $role2->name,
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function guests_cannot_update_roles(): void
    {
        $role = Role::factory()->create();

        $this->patchJson(route('api.roles.update', $role))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

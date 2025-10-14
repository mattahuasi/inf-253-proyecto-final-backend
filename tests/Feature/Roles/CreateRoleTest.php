<?php

namespace Tests\Feature\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateRoleTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_roles(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:create']);

        $response = $this->postJson(route('api.roles.store'), [
            'name' => 'test to create a role'
        ]);

        $response->assertCreated();

        $role = Role::where('name', 'test to create a role')->first();
        $response->assertJsonApiResource($role, [
            'name' => 'test to create a role'
        ]);
    }

    #[Test]
    public function name_is_required()
    {
        $this->authenticateUser(['role:create']);
        $this->postJson(route('api.roles.store'), [
            'name' => null,
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function name_must_be_unique()
    {
        $this->authenticateUser(['role:create']);
        $role = Role::factory()->create();

        $this->postJson(route('api.roles.store'),  [
            'name' => $role->name,
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function guests_cannot_create_roles()
    {
        $this->postJson(route('api.roles.store'))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );

        $this->assertDatabaseCount('roles', 0);
    }
}

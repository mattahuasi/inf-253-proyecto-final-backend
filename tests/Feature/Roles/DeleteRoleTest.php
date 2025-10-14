<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class DeleteRoleTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_delete_roles(): void
    {
        $this->authenticateUser(['role:delete']);
        $role = Role::factory()->create();
        $this->deleteJson(route('api.roles.destroy', $role))
            ->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_roles(): void
    {
        $role = Role::factory()->create();
        $this->deleteJson(route('api.roles.destroy', $role))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

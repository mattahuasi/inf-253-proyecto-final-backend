<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchRolesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_role(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:show']);

        $role = Role::factory()->create();

        $response = $this->getJson(route('api.roles.show', $role));
        $response->assertJsonApiResource($role, [
            'name' => $role->name,
        ])->assertJsonApiRelationshipLinks($role, ['permissions']);
    }

    #[Test]
    public function it_returns_a_json_api_error_object_when_an_role_is_not_found(): void
    {
        $this->authenticateUser(['role:show']);

        $url = route('api.roles.show', [
            'role' => 'not-existing'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.404'),
                detail: "No records found with the id 'not-existing' in the 'roles' resource.",
                status: "404"
            );
    }

    #[Test]
    public function can_fetch_all_roles(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:index']);

        $roles[] = Role::factory()->create();
        $roles[] = Role::factory()->create();
        $roles[] = Role::factory()->create();

        $response = $this->getJson(route('api.roles.index'));
        $response->assertJsonApiResourceCollection($roles, ['name']);
        $response->assertJsonApiCollectionRelationshipLinks($roles, ['permissions']);
    }
}

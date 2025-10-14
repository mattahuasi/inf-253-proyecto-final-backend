<?php

namespace Tests\Feature\Permissions;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchPermissionsTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_permission(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['permission:show']);

        $permission = Permission::factory()->create();

        $response = $this->getJson(route('api.permissions.show', $permission));
        $response->assertJsonApiResource($permission, [
            'name' => $permission->name,
            'description' => $permission->description,
            'type' => $permission->type,
        ])->assertJsonApiRelationshipLinks($permission, ['roles']);
    }

    #[Test]
    public function it_returns_a_json_api_error_object_when_an_permission_is_not_found(): void
    {
        $this->authenticateUser(['permission:show']);

        $url = route('api.permissions.show', [
            'permission' => 'not-existing'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.404'),
                detail: "No records found with the id 'not-existing' in the 'permissions' resource.",
                status: "404"
            );
    }

    #[Test]
    public function can_fetch_all_permissions(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['permission:index']);

        $permissions[] = Permission::factory()->create();
        $permissions[] = Permission::factory()->create();
        $permissions[] = Permission::factory()->create();

        $response = $this->getJson(route('api.permissions.index'));
        $response->assertJsonApiResourceCollection($permissions, ['name','description','type']);
        $response->assertJsonApiCollectionRelationshipLinks($permissions, ['roles']);
    }
}

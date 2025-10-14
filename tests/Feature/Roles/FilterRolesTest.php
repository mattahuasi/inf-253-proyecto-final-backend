<?php

namespace Tests\Feature\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FilterRolesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_filter_roles_by_name(): void
    {
        $this->authenticateUser(['role:index']);

        Role::factory()->create(['name' => 'C name']);
        Role::factory()->create(['name' => 'B name test']);
        Role::factory()->create(['name' => 'A name']);


        $url = route('api.roles.index', [
            'filter' => [
                'name' => 'test'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('B name test')
            ->assertDontSee([
                'C name',
                'A name'
            ]);
    }



    #[Test]
    public function can_filter_roles_by_permission_ids(): void
    {
        $this->authenticateUser(['role:index']);

        Role::factory(2)->create();
        $permissions[] = Permission::factory()->hasRoles(2)->create(['name' => 'test:index']);
        $permissions[] = Permission::factory()->hasRoles(1)->create(['name' => 'test:delete']);

        $url = route('api.roles.index', [
            'filter' => [
                'permissions' => '2'
            ],
            'include' => 'permissions'
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee($permissions[1]->name);
    }


    #[Test]
    public function cannot_filter_roles_by_unknown(): void
    {
        $this->authenticateUser(['role:index']);

        Role::factory(5)->create();

        $url = route('api.roles.index', [
            'filter' => [
                'unknown' => '-'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The filter field 'unknown' is not allowed in the 'roles' resource.",
                status: "400",
            );
    }
}

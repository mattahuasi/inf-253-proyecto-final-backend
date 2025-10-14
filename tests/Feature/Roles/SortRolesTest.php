<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SortRolesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_sort_roles_by_name(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:index']); // with 'role A'

        Role::factory()->create(['name' => 'role B']);
        Role::factory()->create(['name' => 'role C']);
        Role::factory()->create(['name' => 'role D']);

        $url = route('api.roles.index', ['sort' => 'name']);

        $this->getJson($url)->assertSeeInOrder([
            'role A',
            'role B',
            'role C',
            'role D',
        ]);
    }

    #[Test]
    public function can_sort_roles_by_name_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:index']); // with 'role A'

        Role::factory()->create(['name' => 'role B']);
        Role::factory()->create(['name' => 'role C']);
        Role::factory()->create(['name' => 'role D']);

        $url = route('api.roles.index', ['sort' => '-name']);

        $this->getJson($url)->assertSeeInOrder([
            'role D',
            'role C',
            'role B',
            'role A',
        ]);
    }

    #[Test]
    public function can_sort_roles_by_desc_name_and_asc_id(): void
    {
        $this->withoutExceptionHandling();

        Role::factory()->create(['name' => 'role D']);
        Role::factory()->create(['name' => 'role C']);
        Role::factory()->create(['name' => 'role B']);

        $this->authenticateUser(['role:index']); // with 'role A'

        $url = route('api.roles.index', ['sort' => 'id,-name']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'role D',
                'role C',
                'role B',
                'role A',
            ]);
    }

    #[Test]
    public function can_sort_roles_by_unknown_fields(): void
    {
        $this->authenticateUser(['role:index']);
        Role::factory(3)->create();

        $url = route('api.roles.index', ['sort' => 'unknown']);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The sort field 'unknown' is not allowed in the 'roles' resource.",
                status: "400",
            );
    }
}

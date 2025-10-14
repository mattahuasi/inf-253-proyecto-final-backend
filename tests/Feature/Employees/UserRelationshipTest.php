<?php

namespace Tests\Feature\Employees;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UserRelationshipTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_user_resource_when_user_exists(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:show']);

        $employee = Employee::factory()->create();
        User::factory()->create(['person_id' => $employee->person_id]);

        $response = $this->getJson(route('api.employees.user', $employee));
        $response->assertJsonApiResource($employee->user, [
            'username' => $employee->user->username,
            'email' => $employee->user->email,
            'user_type' => 'employee'
        ]);
    }

    #[Test]
    public function can_fetch_user_resource_when_user_does_not_exist(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:show']);

        $employee = Employee::factory()->create();

        $response = $this->getJson(route('api.employees.user', $employee));
        $response->assertJson([
            'data' => null
        ]);
    }

    #[Test]
    public function can_fetch_user_relationship_when_user_exists(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:show']);

        $employee = Employee::factory()->create();
        User::factory()->create(['person_id' => $employee->person_id]);

        $response = $this->getJson(route('api.employees.relationships.user.show', $employee));
        $response->assertExactJson([
            'data' => [
                'id' => (string)$employee->user->getRouteKey(),
                'type' => $employee->user->getResourceType()
            ]
        ]);
    }

    #[Test]
    public function can_fetch_user_relationship_when_user_does_not_exist(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:show']);

        $employee = Employee::factory()->create();

        $response = $this->getJson(route('api.employees.relationships.user.show', $employee));
        $response->assertExactJson([
            'data' => null
        ]);
    }

    // #[Test]
    // public function can_fetch_the_associated_role_resource(): void
    // {
    //     $this->withoutExceptionHandling();

    //     $user = User::factory()->create();
    //     $response = $this->getJson(route('api.users.role', $user));
    //     $response->assertJsonApiResource($user->role, [
    //         'name' => $user->role->name
    //     ]);
    // }
}

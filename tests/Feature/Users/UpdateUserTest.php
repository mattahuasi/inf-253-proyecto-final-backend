<?php

namespace Tests\Feature\Users;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_update_user_customer(): void
    {
        $this->withoutExceptionHandling();
        $user = $this->authenticateUser(['user:update']);

        $role = Role::factory()->create(['name' => 'Customer']);
        $role2 = Role::factory()->create(['name' => 'Customer 2']);
        Customer::factory(3)->create();
        $customer = Customer::factory()->create();
        $user = User::factory()->create(['person_id' => $customer->person_id, 'role_id' => $role->id]);

        $response = $this->patchJson(route('api.users.update', $user), [
            'username' => 'CustomerTest',
            'email' => 'customer@gmail.com',
            'enabled' => true,
            '_relationships' => [
                'role' => $role2
            ]
        ]);

        $response->assertOk();

        $user = User::whereEmail('customer@gmail.com')->first();

        $response->assertJsonApiResource($user, [
            'username' => 'CustomerTest',
            'email' => 'customer@gmail.com',
            'user_type' => 'customer',
            'enabled' => true,
        ])->assertJsonApiRelationshipLinks($user, ['role', 'customer']);

        $this->assertDatabaseHas('customers', ['person_id' => $user->person_id]);
        $this->assertDatabaseHas('users', ['person_id' => $user->person_id, 'role_id' => $role2->id]);
    }

    #[Test]
    public function can_update_user_employee(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['user:update']);

        $role = Role::factory()->create(['name' => 'Employee']);
        $role2 = Role::factory()->create(['name' => 'Employee 2']);
        Employee::factory(3)->create();
        $employee = Employee::factory()->create();
        $user = User::factory()->create(['person_id' => $employee->person_id, 'role_id' => $role->id]);

        $response = $this->patchJson(route('api.users.update', $user), [
            'username' => 'EmployeeTest',
            'email' => 'employee@gmail.com',
            'enabled' => true,
            '_relationships' => [
                'role' => $role2
            ]
        ]);

        $response->assertOk();

        $user = User::whereEmail('employee@gmail.com')->first();

        $response->assertJsonApiResource($user, [
            'username' => 'EmployeeTest',
            'email' => 'employee@gmail.com',
            'user_type' => 'employee',
            'enabled' => true,
        ])->assertJsonApiRelationshipLinks($user, ['role', 'employee']);

        $this->assertDatabaseHas('employees', ['person_id' => $user->person_id]);
        $this->assertDatabaseHas('users', ['person_id' => $user->person_id, 'role_id' => $role2->id]);
    }

    #[Test]
    public function guests_cannot_update_users(): void
    {
        $user = User::factory()->create();

        $this->patchJson(route('api.users.update', $user))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }

    #[Test]
    public function can_reset_password_users(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['user:update']);
        $user = User::factory()->create(['password' => Hash::make('otherPassword')]);

        $response = $this->patchJson(route('api.users.resetPassword', $user));
        $response->assertNoContent();

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}

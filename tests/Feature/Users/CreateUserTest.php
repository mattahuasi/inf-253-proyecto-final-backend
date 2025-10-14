<?php

namespace Tests\Feature\Users;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateUserTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_user_customer(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['user:create']);

        $role = Role::factory()->create(['name' => 'Cliente']);
        Customer::factory(3)->create();
        $customer = Customer::factory()->create();

        $response = $this->postJson(route('api.users.store'), [
            'username' => 'CustomerTest',
            'email' => 'customer@gmail.com',
            'user_type' => 'customer',
            'enabled' => true,
            '_relationships' => [
                'role' => $role,
                'customer' => $customer
            ]
        ]);

        $response->assertCreated();

        $user = User::whereEmail('customer@gmail.com')->first();

        $response->assertJsonApiResource($user, [
            'username' => 'CustomerTest',
            'email' => 'customer@gmail.com',
            'user_type' => 'customer',
            'enabled' => true,
        ])->assertJsonApiRelationshipLinks($user, ['role','customer']);

        $this->assertDatabaseHas('customers',['person_id' => $user->person_id]);
    }

    #[Test]
    public function can_create_user_employee(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['user:create']);

        $role = Role::factory()->create(['name' => 'Employee']);
        Employee::factory(3)->create();
        $employee = Employee::factory()->create();

        $response = $this->postJson(route('api.users.store'), [
            'username' => 'EmployeeTest',
            'email' => 'employee@gmail.com',
            'user_type' => 'employee',
            'enabled' => true,
            '_relationships' => [
                'role' => $role,
                'employee' => $employee
            ]
        ]);

        $response->assertCreated();

        $user = User::whereEmail('employee@gmail.com')->first();

        $response->assertJsonApiResource($user, [
            'username' => 'EmployeeTest',
            'email' => 'employee@gmail.com',
            'user_type' => 'employee',
            'enabled' => true,
        ])->assertJsonApiRelationshipLinks($user, ['role','employee']);

        $this->assertDatabaseHas('employees',['person_id' => $user->person_id]);
    }

    #[Test]
    public function relationship_customer_is_invalid(): void
    {
        $this->authenticateUser(['user:create']);

        $role = Role::factory()->create(['name' => 'Cliente']);
        $customer = Customer::factory()->make();

        $response = $this->postJson(route('api.users.store'), [
            'username' => 'CustomerTest',
            'email' => 'customer@gmail.com',
            'user_type' => 'customer',
            'enabled' => true,
            '_relationships' => [
                'role' => $role,
                'customer' => $customer
            ]
        ]);
        $response->assertJsonApiValidationErrors('relationships.customer');
    }

    #[Test]
    public function relationship_employee_is_invalid(): void
    {
        $this->authenticateUser(['user:create']);

        $role = Role::factory()->create(['name' => 'Employee']);
        $employee = Employee::factory()->make();

        $response = $this->postJson(route('api.users.store'), [
            'username' => 'EmployeeTest',
            'email' => 'employee@gmail.com',
            'user_type' => 'employee',
            'enabled' => true,
            '_relationships' => [
                'role' => $role,
                'employee' => $employee
            ]
        ]);
        $response->assertJsonApiValidationErrors('relationships.employee');
    }

    #[Test]
    public function guests_cannot_create_users(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('api.users.store', $user))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

<?php

namespace Tests\Feature\Users;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_delete_user_customer(): void
    {
        $this->authenticateUser(['user:delete']);

        $role = Role::factory()->create(['name' => 'Customer']);
        $customer = Customer::factory()->create();
        $user = User::factory()->create(['person_id' => $customer->person_id, 'role_id' => $role->id]);

        $this->deleteJson(route('api.users.destroy', $user))
            ->assertNoContent();
        $this->assertDatabaseHas('customers', ['person_id' => $customer->person_id]);
    }

    #[Test]
    public function can_delete_user_employee(): void
    {
        $this->authenticateUser(['user:delete']);

        $role = Role::factory()->create(['name' => 'Employee']);
        $employee = Employee::factory()->create();
        $user = User::factory()->create(['person_id' => $employee->person_id, 'role_id' => $role->id]);

        $this->deleteJson(route('api.users.destroy', $user))
            ->assertNoContent();
        $this->assertDatabaseHas('employees', ['person_id' => $employee->person_id]);
    }
    
    #[Test]
    public function guests_cannot_delete_users(): void
    {
        $user = User::factory()->create();
        $this->deleteJson(route('api.users.destroy', $user))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

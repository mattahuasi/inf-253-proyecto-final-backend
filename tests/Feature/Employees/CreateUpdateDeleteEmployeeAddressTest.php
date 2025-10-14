<?php

namespace Tests\Feature\Employees;

use App\Models\Address;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateUpdateDeleteEmployeeAddressTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_employee_addresses(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:create']);
        $this->incrementLevelJsonApiDocumentFormatting();

        $employee = Employee::factory()->create();

        $response = $this->postJson(route('api.employees.addresses.store', $employee), [
            'zona' => 'test zona',
            'street' => 'test street',
            'detail' => 'test detail',
        ]);

        $response->assertCreated();

        $address = $employee->person->addresses()->first();
        $response->assertJsonApiResource($address, [
            'zona' => 'test zona',
            'street' => 'test street',
            'detail' => 'test detail',
        ], route('api.employees.addresses.show', [
            'employee' => $employee->person_id,
            'address' => $address->id
        ]));
    }

    #[Test]
    public function can_update_employee_addresses(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:update']);
        $this->incrementLevelJsonApiDocumentFormatting();

        $employee = Employee::factory()->create();
        $address = Address::factory()->create(['person_id' => $employee->person_id]);

        $response = $this->patchJson(route('api.employees.addresses.update', [
            'employee' => $employee,
            'address' => $address
        ]), [
            'zona' => 'test update zona',
            'street' => 'test update street',
            'detail' => 'test update detail',
        ]);

        $response->assertOk();

        $response->assertJsonApiResource($address, [
            'zona' => 'test update zona',
            'street' => 'test update street',
            'detail' => 'test update detail',
        ], route('api.employees.addresses.show', [
            'employee' => $employee->person_id,
            'address' => $address->id
        ]));
    }

    #[Test]
    public function can_delete_employee_addresses(): void
    {
        $this->authenticateUser(['employee:delete']);
        $employee = Employee::factory()->create();
        $address = Address::factory()->create(['person_id' => $employee->person_id]);
        $this->deleteJson(route('api.employees.addresses.update', [
            'employee' => $employee,
            'address' => $address
        ]))->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_employee_addresses(): void
    {
        $employee = Employee::factory()->create();
        $address = Address::factory()->create(['person_id' => $employee->person_id]);
        $this->deleteJson(route('api.employees.addresses.update', [
            'employee' => $employee,
            'address' => $address
        ]))->assertJsonApiError(
            title: trans('httpCodes.401'),
            detail: trans('myMessages.ActionRequiresAuth'),
            status: '401'
        );
    }
}

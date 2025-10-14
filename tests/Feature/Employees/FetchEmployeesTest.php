<?php

namespace Tests\Feature\Employees;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchEmployeesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_employee(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:show']);

        $employee = Employee::factory()->create();
        User::factory()->create(['person_id' => $employee->person_id]);

        $response = $this->getJson(route('api.employees.show', [
            'employee' => $employee
        ]));

        $response->assertJsonApiResource($employee, [
            'paternal_surname' => $employee->person->paternal_surname,
            'maternal_surname' => $employee->person->maternal_surname,
            'names'  => $employee->person->names,
            'gender' => $employee->person->gender,
            'phone'  => $employee->person->phone,
        ]);

        $response->assertJsonApiRelationshipLinks($employee, ['user']);
    }

    #[Test]
    public function can_fetch_all_employees(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:index']);

        $c1 = Employee::factory()->create();
        User::factory()->create(['person_id' => $c1->person_id]);
        $employees[] = $c1;
        $employees[] = Employee::factory()->create();
        $employees[] = Employee::factory()->create();


        $response = $this->getJson(route('api.employees.index'));

        $response->assertJsonApiResourceCollection($employees, [
            'paternal_surname',
            'maternal_surname',
            'names',
            'gender',
            'phone',
        ]);

        $response->assertJsonApiCollectionRelationshipLinks($employees, ['user']);
    }

    #[Test]
    public function it_returns_a_json_api_error_object_when_an_employee_is_not_found(): void
    {
        $this->authenticateUser(['employee:show']);

        $url = route('api.employees.show', [
            'employee' => 'not-existing'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.404'),
                detail: "No records found with the id 'not-existing' in the 'employees' resource.",
                status: "404"
            );
    }
}
// administrator AD,
// cook CO,
// cashier CA,
// waiter WA
//
//
// superadmin
// empleado
// cliente
//
//
// people
//  id
//
// users
//  id
//  person_id
//
// employee
//  person_id

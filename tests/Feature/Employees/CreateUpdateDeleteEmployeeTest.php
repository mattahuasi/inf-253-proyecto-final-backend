<?php

namespace Tests\Feature\Employees;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateUpdateDeleteEmployeeTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_employees(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:create']);

        $response = $this->postJson(route('api.employees.store'), [
            'paternal_surname' => 'Guarachi',
            'maternal_surname' => 'Montenegro',
            'names' => 'Ana Monica',
            'gender' =>  'F',
            'phone' => '70000000',
            'type' => 'AD'
        ]);

        $response->assertCreated();

        $employee = Employee::first();

        $response->assertJsonApiResource($employee, [
            'paternal_surname' => $employee->person->paternal_surname,
            'maternal_surname' => $employee->person->maternal_surname,
            'names' => $employee->person->names,
            'gender' =>  $employee->person->gender,
            'phone' => $employee->person->phone,
            'type' => $employee->type,
        ]);
    }

    #[Test]
    public function can_update_employees(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['employee:update']);

        $employee = Employee::factory()->create();

        $response = $this->patchJson(route('api.employees.update', $employee), [
            'paternal_surname' => 'Ramirez',
            'maternal_surname' => 'Manrique',
            'names' => 'Ana Mónica',
            'gender' =>  'F',
            'phone' => '70000000',
            'type' => 'WA'
        ]);

        $response->assertOk();

        $employee = Employee::first();

        $response->assertJsonApiResource($employee, [
            'paternal_surname' => 'Ramirez',
            'maternal_surname' => 'Manrique',
            'names' => 'Ana Mónica',
            'gender' =>  'F',
            'phone' => '70000000',
            'type' => 'WA'
        ]);
    }

    /*
    * VALIDATION TESTS
    */

    #[Test]
    public function can_delete_employees(): void
    {
        $this->authenticateUser(['employee:delete']);
        $employee = Employee::factory()->create();
        $this->deleteJson(route('api.employees.destroy', $employee))
            ->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_employees(): void
    {
        $employee = Employee::factory()->create();
        $this->deleteJson(route('api.employees.destroy', $employee))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

// Para construir mi API REST, estoy siguiendo las especificaciones JsonAPI, pero ahora tengo una duda, al momento de realizar un CRUD para el método create(store), tengo mi tabla  empleado y usuario, un empleado pude tener o no un usuario, ósea que el empleado_id pasa para la tabla usuario. como se maneja esa relaciona al momento de hacer el create mediante  la API, si se que existe la rutas /empleados/1/relationships/user y /empleados/1/user. Pero necesito saber como se maneja la crea cion del usuario para el empleado ya que al registrar un empleado la creacion del usuario es opcional, osea que se podra crear su usuario en un futuro o al momento de relizar el registro del empleado,
// el usuario del empleado si se creara al momento de crear el empleado se necesita dos campo username y email

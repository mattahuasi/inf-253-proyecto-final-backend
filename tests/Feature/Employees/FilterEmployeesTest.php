<?php

namespace Tests\Feature\Employees;

use App\Models\Employee;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FilterEmployeesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    private function createDataEmployees(): void
    {
        $p1 = Person::factory()->create([
            'names' => 'Noelia Ursula',
            'paternal_surname' => 'Manrique',
            'maternal_surname' => 'Cortez',
        ]);

        User::factory()->create(['person_id' => $p1->id, 'username' => 'mmmtest']);
        Employee::factory()->create(['person_id' => $p1->id , 'type' => 'CO']);

        $p2 = Person::factory()->create([
            'names' => 'Macario Aron',
            'paternal_surname' => 'Villanueva',
            'maternal_surname' => 'Jaimes'
        ]);
        Employee::factory()->create(['person_id' => $p2->id, 'type' => 'CA']);

        $p3 = Person::factory()->create([
            'names' => 'Gabriela Ana',
            'paternal_surname' => 'Valdez',
            'maternal_surname' => 'Jiménez'
        ]);
        Employee::factory()->create(['person_id' => $p3->id, 'type' => 'CA']);

        $p4 = Person::factory()->create([
            'names' => 'Carlos Eduardo',
            'paternal_surname' => 'Sánchez',
            'maternal_surname' => 'López'
        ]);
        Employee::factory()->create(['person_id' => $p4->id, 'type' => 'WA']);

        $p5 = Person::factory()->create([
            'names' => 'Diana Sofía',
            'paternal_surname' => 'Cordero',
            'maternal_surname' => 'Rivas'
        ]);
        Employee::factory()->create(['person_id' => $p5->id, 'type' => 'AD']);
    }

    #[Test]
    public function can_filter_employees_by_like_type(): void
    {
        $this->authenticateUser(['employee:index']);

        $this->createDataEmployees();

        $url = route('api.employees.index', [
            'filter' => [
                'type' => 'AD',
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(1, 'data')
            ->assertJsonFragment(['names' => 'Diana Sofía']);
    }

    #[Test]
    public function can_filter_employees_by_like_names(): void
    {
        $this->authenticateUser(['employee:index']);

        $this->createDataEmployees();

        $url = route('api.employees.index', [
            'filter' => [
                'names' => 'el',
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(2, 'data')
            ->assertJsonFragment(['names' => 'Noelia Ursula'])
            ->assertJsonFragment(['names' => 'Gabriela Ana']);
    }


    #[Test]
    public function cannot_filter_employees_by_unknown(): void
    {
        $this->authenticateUser(['employee:index']);

        $url = route('api.employees.index', [
            'filter' => [
                'unknown' => '-'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The filter field 'unknown' is not allowed in the 'employees' resource.",
                status: "400",
            );
    }
}

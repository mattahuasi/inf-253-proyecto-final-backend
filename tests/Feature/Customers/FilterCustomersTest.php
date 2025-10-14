<?php

namespace Tests\Feature\Customers;

use App\Models\Customer;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FilterCustomersTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    private function createDataCustomers(): void
    {
        $p1 = Person::factory()->create([
            'names' => 'Noelia Ursula',
            'paternal_surname' => 'Manrique',
            'maternal_surname' => 'Cortez',
        ]);

        User::factory()->create(['person_id' => $p1->id, 'username' => 'mmmtest']);
        Customer::factory()->create(['person_id' => $p1->id]);

        $p2 = Person::factory()->create([
            'names' => 'Macario Aron',
            'paternal_surname' => 'Villanueva',
            'maternal_surname' => 'Jaimes'
        ]);
        Customer::factory()->create(['person_id' => $p2->id]);

        $p3 = Person::factory()->create([
            'names' => 'Gabriela Ana',
            'paternal_surname' => 'Valdez',
            'maternal_surname' => 'Jiménez'
        ]);
        Customer::factory()->create(['person_id' => $p3->id]);

        $p4 = Person::factory()->create([
            'names' => 'Carlos Eduardo',
            'paternal_surname' => 'Sánchez',
            'maternal_surname' => 'López'
        ]);
        Customer::factory()->create(['person_id' => $p4->id]);

        $p5 = Person::factory()->create([
            'names' => 'Diana Sofía',
            'paternal_surname' => 'Cordero',
            'maternal_surname' => 'Rivas'
        ]);
        Customer::factory()->create(['person_id' => $p5->id]);
    }

    #[Test]
    public function can_filter_customers_by_like_names(): void
    {
        $this->authenticateUser(['customer:index']);

        $this->createDataCustomers();

        $url = route('api.customers.index', [
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
    public function cannot_filter_customers_by_unknown(): void
    {
        $this->authenticateUser(['customer:index']);

        $url = route('api.customers.index', [
            'filter' => [
                'unknown' => '-'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The filter field 'unknown' is not allowed in the 'customers' resource.",
                status: "400",
            );
    }
}

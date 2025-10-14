<?php

namespace Tests\Feature\Customers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchCustomersTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_customer(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:show']);

        $customer = Customer::factory()->create();
        User::factory()->create(['person_id' => $customer->person_id]);

        $response = $this->getJson(route('api.customers.show', [
            'customer' => $customer
        ]));

        $response->assertJsonApiResource($customer, [
            'paternal_surname' => $customer->person->paternal_surname,
            'maternal_surname' => $customer->person->maternal_surname,
            'names'  => $customer->person->names,
            'gender' => $customer->person->gender,
            'phone'  => $customer->person->phone,
        ]);

        $response->assertJsonApiRelationshipLinks($customer, ['user']);
    }

    #[Test]
    public function can_fetch_all_customers(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:index']);

        $c1 = Customer::factory()->create();
        User::factory()->create(['person_id' => $c1->person_id]);
        $customers[] = $c1;
        $customers[] = Customer::factory()->create();
        $customers[] = Customer::factory()->create();


        $response = $this->getJson(route('api.customers.index'));

        $response->assertJsonApiResourceCollection($customers, [
            'paternal_surname',
            'maternal_surname',
            'names',
            'gender',
            'phone',
        ]);

        $response->assertJsonApiCollectionRelationshipLinks($customers, ['user']);
    }

    #[Test]
    public function it_returns_a_json_api_error_object_when_an_customer_is_not_found(): void
    {
        $this->authenticateUser(['customer:show']);

        $url = route('api.customers.show', [
            'customer' => 'not-existing'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.404'),
                detail: "No records found with the id 'not-existing' in the 'customers' resource.",
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
// customer
//  person_id

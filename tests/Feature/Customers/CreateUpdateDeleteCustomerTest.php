<?php

namespace Tests\Feature\Customers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateUpdateDeleteCustomerTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_customers(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:create']);

        $response = $this->postJson(route('api.customers.store'), [
            'paternal_surname' => 'Guarachi',
            'maternal_surname' => 'Montenegro',
            'names' => 'Ana Monica',
            'gender' =>  'F',
            'phone' => '70000000'
            // '_relationships' => [
            //     'user' => $user
            // ]
        ]);

        $response->assertCreated();

        $customer = Customer::first();

        $response->assertJsonApiResource($customer, [
            'paternal_surname' => $customer->person->paternal_surname,
            'maternal_surname' => $customer->person->maternal_surname,
            'names' => $customer->person->names,
            'gender' =>  $customer->person->gender,
            'phone' => $customer->person->phone,
        ]);
    }

    #[Test]
    public function can_update_customers(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:update']);

        $customer = Customer::factory()->create();

        $response = $this->patchJson(route('api.customers.update', $customer), [
            'paternal_surname' => 'Ramirez',
            'maternal_surname' => 'Manrique',
            'names' => 'Ana Mónica',
            'gender' =>  'F',
            'phone' => '70000000'
        ]);

        $response->assertOk();

        $customer = Customer::first();

        $response->assertJsonApiResource($customer, [
            'paternal_surname' => 'Ramirez',
            'maternal_surname' => 'Manrique',
            'names' => 'Ana Mónica',
            'gender' =>  'F',
            'phone' => '70000000'
        ]);
    }

    /*
    * VALIDATION TESTS
    */

    #[Test]
    public function can_delete_customers(): void
    {
        $this->authenticateUser(['customer:delete']);
        $customer = Customer::factory()->create();
        $this->deleteJson(route('api.customers.destroy', $customer))
            ->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_customers(): void
    {
        $customer = Customer::factory()->create();
        $this->deleteJson(route('api.customers.destroy', $customer))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

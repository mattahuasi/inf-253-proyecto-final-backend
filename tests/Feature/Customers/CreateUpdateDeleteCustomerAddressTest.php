<?php

namespace Tests\Feature\Customers;

use App\JsonApi\MyDocument;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateUpdateDeleteCustomerAddressTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_customer_addresses(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:create']);
        $this->incrementLevelJsonApiDocumentFormatting();

        $customer = Customer::factory()->create();

        $response = $this->postJson(route('api.customers.addresses.store', $customer), [
            'zona' => 'test zona',
            'street' => 'test street',
            'detail' => 'test detail',
        ]);

        $response->assertCreated();

        $address = $customer->person->addresses()->first();
        $response->assertJsonApiResource($address, [
            'zona' => 'test zona',
            'street' => 'test street',
            'detail' => 'test detail',
        ], route('api.customers.addresses.show', [
            'customer' => $customer->person_id,
            'address' => $address->id
        ]));
    }

    #[Test]
    public function can_update_customer_addresses(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:update']);
        $this->incrementLevelJsonApiDocumentFormatting();

        $customer = Customer::factory()->create();
        $address = Address::factory()->create(['person_id' => $customer->person_id]);

        $response = $this->patchJson(route('api.customers.addresses.update', [
            'customer' => $customer,
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
        ], route('api.customers.addresses.show', [
            'customer' => $customer->person_id,
            'address' => $address->id
        ]));
    }

    #[Test]
    public function can_delete_customer_addresses(): void
    {
        $this->authenticateUser(['customer:delete']);
        $customer = Customer::factory()->create();
        $address = Address::factory()->create(['person_id' => $customer->person_id]);
        $this->deleteJson(route('api.customers.addresses.update', [
            'customer' => $customer,
            'address' => $address
        ]))->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_customer_addresses(): void
    {
        $customer = Customer::factory()->create();
        $address = Address::factory()->create(['person_id' => $customer->person_id]);
        $this->deleteJson(route('api.customers.addresses.update', [
            'customer' => $customer,
            'address' => $address
        ]))->assertJsonApiError(
            title: trans('httpCodes.401'),
            detail: trans('myMessages.ActionRequiresAuth'),
            status: '401'
        );
    }
}

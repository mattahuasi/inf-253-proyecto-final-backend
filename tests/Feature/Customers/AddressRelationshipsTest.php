<?php

namespace Tests\Feature\Customers;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class AddressRelationshipsTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_the_associated_addresses_resources(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:index']);

        $customer = Customer::factory()->create();
        $addresses[] = Address::factory()->make();
        $addresses[] = Address::factory()->make();

        $customer->person->addresses()->save($addresses[0]);
        $customer->person->addresses()->save($addresses[1]);

        $response = $this->getJson(route('api.customers.addresses', $customer));
        $response->assertJsonApiResourceCollection($addresses, [
            'zona',
            'street',
            'detail'
        ]);
    }

    #[Test]
    public function can_fetch_address_relationships()
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:index']);

        $a1 = Address::factory()->create();
        $a2 = Address::factory()->create();
        $customer = Customer::factory()->create();
        $customer->person->addresses()->save($a1);
        $customer->person->addresses()->save($a2);

        $response = $this->getJson(route('api.customers.relationships.addresses.show', $customer));
        $response->assertExactJson([
            'data' => [
                [
                    'id' => (string)$a1->getRouteKey(),
                    'type' => 'addresses',
                ],
                [
                    'id' => (string)$a2->getRouteKey(),
                    'type' => 'addresses',
                ],
            ]
        ]);
    }

    //     #[Test]
    //     public function can_update_address_relationships()
    //     {
    //         $this->withoutExceptionHandling();
    //         $this->withoutJsonApiDocumentFormatting();

    //         $p1 = Address::factory()->create();
    //         $customer = Customer::factory()->create();
    //         $customer->person->addresses()->save($p1);

    //         $p2 = Address::factory()->create();
    //         $p3 = Address::factory()->create();
    //         $p4 = Address::factory()->create();

    //         $data = [
    //             'data' => [
    //                 [
    //                     'id' => (string)$p2->getRouteKey(),
    //                     'type' => 'addresses',
    //                 ],
    //                 [
    //                     'id' => (string)$p3->getRouteKey(),
    //                     'type' => 'addresses',
    //                 ],
    //                 [
    //                     'id' => (string)$p4->getRouteKey(),
    //                     'type' => 'addresses',
    //                 ],
    //             ]
    //         ];

    //         $url = route('api.customers.relationships.addresses.update', $customer);

    //         $response = $this->patchJson($url, $data);
    //         $response->assertExactJson([
    //             'data' => [
    //                 [
    //                     'id' => (string)$p2->getRouteKey(),
    //                     'type' => 'addresses',
    //                 ],
    //                 [
    //                     'id' => (string)$p3->getRouteKey(),
    //                     'type' => 'addresses',
    //                 ],
    //                 [
    //                     'id' => (string)$p4->getRouteKey(),
    //                     'type' => 'addresses',
    //                 ],
    //             ]
    //         ]);
    //         $response->assertOk();
    //     }

    //     #[Test]
    //     public function can_attach_address_relationships()
    //     {
    //         $this->withoutExceptionHandling();
    //         $this->withoutJsonApiDocumentFormatting();

    //         $a1 = Address::factory()->create();
    //         $customer = Customer::factory()->create();
    //         $customer->person->addresses()->save($a1);

    //         $a2 = Address::factory()->create();

    //         $data = [
    //             'data' => [
    //                 [
    //                     'id' => (string)$a2->getRouteKey(),
    //                     'type' => 'addresses',
    //                 ]
    //             ]
    //         ];

    //         $url = route('api.customers.relationships.addresses.attach', $customer);

    //         $response = $this->postJson($url, $data);
    //         $response->assertNoContent();

    //         $this->assertTrue($customer->person->addresses->contains($a1));
    //         $this->assertTrue($customer->person->addresses->contains($a2));
    //     }

    //     #[Test]
    //     public function can_detach_address_relationships()
    //     {
    //         $this->withoutExceptionHandling();
    //         $this->withoutJsonApiDocumentFormatting();

    //         $a1 = Address::factory()->create();
    //         $a2 = Address::factory()->create();

    //         $customer = Customer::factory()->create();
    //         $customer->person->addresses()->save($a1);
    //         $customer->person->addresses()->save($a2);


    //         $data = [
    //             'data' => [
    //                 [
    //                     'id' => (string)$a1->getRouteKey(),
    //                     'type' => 'addresses',
    //                 ]
    //             ]
    //         ];

    //         $url = route('api.customers.relationships.addresses.detach', $customer);

    //         $response = $this->deleteJson($url, $data);
    //         $response->assertNoContent();

    //         $this->assertFalse($customer->person->addresses->contains($a1));
    //         $this->assertTrue($customer->person->addresses->contains($a2));
    //     }

    //     #[Test]
    //     public function addresses_must_exist_in_the_database(): void
    //     {
    //         $this->withoutJsonApiDocumentFormatting();
    //         $customer = Customer::factory()->create();

    //         $data = [
    //             'data' => [
    //                 [
    //                     'id' => 'no-existing-1',
    //                     'type' => 'addresses',
    //                 ],
    //                 [
    //                     'id' => 'no-existing-2',
    //                     'type' => 'addresses',
    //                 ]
    //             ]
    //         ];

    //         $url = route('api.customers.relationships.addresses.update', $customer);
    //         $response = $this->patchJson($url, $data);
    //         $response->assertStatus(422);
    //         $response->assertJsonApiValidationErrors('data.0.id');
    //         $response->assertJsonApiValidationErrors('data.1.id');

    //         $url = route('api.customers.relationships.addresses.attach', $customer);
    //         $response = $this->postJson($url, $data);
    //         $response->assertStatus(422);
    //         $response->assertJsonApiValidationErrors('data.0.id');
    //         $response->assertJsonApiValidationErrors('data.1.id');

    //         $url = route('api.customers.relationships.addresses.detach', $customer);
    //         $response = $this->deleteJson($url, $data);
    //         $response->assertStatus(422);
    //         $response->assertJsonApiValidationErrors('data.0.id');
    //         $response->assertJsonApiValidationErrors('data.1.id');
    //     }
}

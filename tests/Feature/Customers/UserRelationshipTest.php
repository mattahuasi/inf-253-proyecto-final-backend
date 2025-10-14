<?php

namespace Tests\Feature\Customers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UserRelationshipTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_user_resource_when_user_exists(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:show']);

        $customer = Customer::factory()->create();
        User::factory()->create(['person_id' => $customer->person_id]);
        // dd($customer->user);
        $response = $this->getJson(route('api.customers.user', $customer));
        $response->assertJsonApiResource($customer->user, [
            'username' => $customer->user->username,
            'email' => $customer->user->email,
            'user_type' => 'customer'
        ]);
    }

    #[Test]
    public function can_fetch_user_resource_when_user_does_not_exist(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:show']);

        $customer = Customer::factory()->create();

        $response = $this->getJson(route('api.customers.user', $customer));
        $response->assertJson([
            'data' => null
        ]);
    }

    #[Test]
    public function can_fetch_user_relationship_when_user_exists(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:show']);

        $customer = Customer::factory()->create();
        User::factory()->create(['person_id' => $customer->person_id]);

        $response = $this->getJson(route('api.customers.relationships.user.show', $customer));
        $response->assertExactJson([
            'data' => [
                'id' => (string)$customer->user->getRouteKey(),
                'type' => $customer->user->getResourceType()
            ]
        ]);
    }

    #[Test]
    public function can_fetch_user_relationship_when_user_does_not_exist(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['customer:show']);

        $customer = Customer::factory()->create();

        $response = $this->getJson(route('api.customers.relationships.user.show', $customer));
        $response->assertExactJson([
            'data' => null
        ]);
    }

    // #[Test]
    // public function can_fetch_the_associated_role_resource(): void
    // {
    //     $this->withoutExceptionHandling();

    //     $user = User::factory()->create();
    //     $response = $this->getJson(route('api.users.role', $user));
    //     $response->assertJsonApiResource($user->role, [
    //         'name' => $user->role->name
    //     ]);
    // }
}

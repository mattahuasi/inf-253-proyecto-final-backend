<?php

namespace Tests\Feature\Dashboard;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Person;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class IndexTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_data_dashboard(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser([]);

        Person::factory(4)
            ->hasEmployee(['type' => 'WA'])
            ->create();

        Person::factory(12)
            ->hasCustomer()
            ->hasAddresses(rand(0, 3))
            ->create();

        State::factory(6)->create();

        $employeeWaiterIds = Employee::where('type', 'WA')->get()->pluck('person_id');
        $stateIds = State::get()->pluck('id');
        foreach (Customer::all() as $customer) {
            Order::factory(rand(0, 3))
                ->create([
                    'customer_id' => $customer->person_id,
                    'employee_id' => $employeeWaiterIds->random(),
                    'state_id' => $stateIds->random()
                ]);
        }
        Menu::factory()->create();
        $customer = Customer::factory()->create();
        Order::factory()->create([
            'ordered_at' => now(),
            'employee_id' => $employeeWaiterIds->random(),
            'customer_id' => $customer->person_id
        ]);

        $response = $this->getJson(route('api.dashboard.index'));
        $response->assertJson([
            "orders_today" => 1,
            "total_customers" =>  13,
            "total_employees" =>  4,
            "total_users" =>  1
        ]);
    }
}

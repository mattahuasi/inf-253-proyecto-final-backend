<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\State;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tracking_code' => strtoupper('P-' . now('Y') . Str::random(10)),
            'type' => $this->faker->randomElement(['O1', 'O2', 'O3']),
            'ordered_at' => $this->faker->dateTimeThisYear(),
            'delivered_at' => $this->faker->optional()->dateTimeThisYear(),
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'comment' => $this->faker->optional()->sentence(10),
            'payment_made' => $this->faker->boolean(),
            'state_id' => State::factory(),
            'customer_id' => Customer::factory(),
            'address_id' => Address::factory(),
            'employee_id' => Employee::factory(),
            'table_number' => $this->faker->optional()->randomElement(Table::pluck('number')->toArray()),
        ];
    }
}

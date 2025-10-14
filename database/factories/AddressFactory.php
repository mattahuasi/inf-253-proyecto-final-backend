<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Category;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'zona' => $this->faker->address(),
            'street' => $this->faker->word() . $this->faker->numerify(),
            'detail' => $this->faker->sentence(3),
            'person_id' => Person::factory(),
        ];
    }
}

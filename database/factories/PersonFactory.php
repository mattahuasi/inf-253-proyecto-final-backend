<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Person::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['M','F']);

        return [
            'paternal_surname' => $this->faker->lastName(),
            'maternal_surname' => $this->faker->lastName(),
            'names' => $this->faker->firstName($gender == 'F' ? 'female': 'male'),
            'gender' => $gender,
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\State>
 */
class StateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = State::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . '-' . rand(9898741, 99999999),
            'slug' => $this->faker->slug(3),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor()
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Table::class;

    public function definition(): array
    {
        return [
            'number' => $this->faker->numberBetween(1, 10),
            'status' => 'A',
            'ability' => $this->faker->randomElement([4, 6]),
        ];
    }
}

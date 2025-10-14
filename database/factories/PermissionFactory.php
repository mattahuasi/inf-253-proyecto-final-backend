<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->generateUniqueString(),
            'description' => fake()->unique()->sentence(3),
            'type' => fake()->randomElement(['api', 'web']),
        ];
    }

    /**
     * Generate a unique string composed of two words separated by ":".
     *
     * @return string
     */
    protected function generateUniqueString(): string
    {
        do {
            $uniqueString = fake()->word . ':' . fake()->word;
        } while (Permission::where('name', $uniqueString)->exists());

        return $uniqueString;
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salle>
 */
class SalleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => 'Salle ' . fake()->unique()->word(),
            'capacite' => fake()->numberBetween(5, 50),
            'surface' => fake()->randomFloat(2, 10, 100),
        ];
    }
}

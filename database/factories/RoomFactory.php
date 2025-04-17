<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Room ' . fake()->unique()->word(),
            'capacity' => fake()->numberBetween(2, 30),
            'equipment' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'is_active' => fake()->boolean(80),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Salle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $heure_debut = fake()->dateTimeBetween('-1 week', '+2 weeks');
        $heure_fin = (clone $heure_debut)->modify('+' . fake()->numberBetween(1, 3) . ' hours');

        return [
            'heure_debut' => $heure_debut,
            'heure_fin' => $heure_fin,
            'user_id' => User::factory(),
            'salle_id' => Salle::factory(),
        ];
    }
}

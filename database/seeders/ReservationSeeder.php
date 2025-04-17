<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Salle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $salles = Salle::all();

        for ($i = 0; $i < 20; $i++) {
            $day = rand(0, 7);
            $hour = rand(8, 16);
            $duration = rand(1, 3);

            $heure_debut = Carbon::now()->addDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
            $heure_fin = (clone $heure_debut)->addHours($duration);

            // Vérifier si la salle est déjà réservée pour ce créneau
            $user = $users->random();
            $salle = $salles->random();

            $existing = Reservation::where('salle_id', $salle->id)
                ->where(function ($query) use ($heure_debut, $heure_fin) {
                    $query->whereBetween('heure_debut', [$heure_debut, $heure_fin])
                        ->orWhereBetween('heure_fin', [$heure_debut, $heure_fin])
                        ->orWhere(function ($q) use ($heure_debut, $heure_fin) {
                            $q->where('heure_debut', '<=', $heure_debut)
                                ->where('heure_fin', '>=', $heure_fin);
                        });
                })->exists();

            // Si pas de chevauchement, créer la réservation
            if (!$existing) {
                Reservation::create([
                    'heure_debut' => $heure_debut,
                    'heure_fin' => $heure_fin,
                    'user_id' => $user->id,
                    'salle_id' => $salle->id,
                ]);
            }
        }
    }
}

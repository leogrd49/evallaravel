<?php

namespace Database\Seeders;

use App\Models\Salle;
use Illuminate\Database\Seeder;

class SalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salles = [
            [
                'nom' => 'Salle Belle-Ile',
                'capacite' => 20,
                'surface' => 35.5,
            ],
            [
                'nom' => 'Salle Fidji',
                'capacite' => 15,
                'surface' => 25.0,
            ],
            [
                'nom' => 'Salle Ouessant',
                'capacite' => 30,
                'surface' => 50.0,
            ],
            [
                'nom' => 'Salle Dumet',
                'capacite' => 10,
                'surface' => 18.5,
            ],
            [
                'nom' => 'Salle Noirmoutier',
                'capacite' => 40,
                'surface' => 75.0,
            ],
        ];

        foreach ($salles as $salle) {
            Salle::create($salle);
        }
    }
}
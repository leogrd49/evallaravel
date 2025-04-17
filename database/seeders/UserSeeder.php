<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Création du compte administrateur
        User::create([
            'nom' => 'Admin',
            'prenom' => 'System',
            'email' => 'admin@evallaravel.fr',
            'password' => Hash::make('password'),
            'role' => 'administrateur',
        ]);

        // Création du compte employé
        User::create([
            'nom' => 'John',
            'prenom' => 'Doe',
            'email' => 'johndoe@evallaravel.fr',
            'password' => Hash::make('user'),
            'role' => 'employe',
        ]);
    }
}
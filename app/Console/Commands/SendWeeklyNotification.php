<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\WeeklyPlanningNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

/** @codeCoverageIgnoreStart */
class SendWeeklyNotification extends Command
{
    /**
     * Summary of signature
     *
     * @var string
     */
    protected $signature = 'notify:weekly';

    /**
     * Summary of description
     *
     * @var string
     */
    protected $description = 'Envoi une notification de rappel de validation des taches à tout les administrateurs';

    /**
     * Summary of handle
     *
     * @return void
     */
    public function handle()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        if ($admins->isEmpty()) {
            $this->info('No administrators found to notify.');

            return;
        }

        // Préparer les données hebdomadaires pour la notification
        $weeklyData = [
            'date' => now()->format('Y-m-d'),
            'summary' => 'Résumé hebdomadaire des réservations de salles'
        ];
        
        Notification::send($admins, new WeeklyPlanningNotification($weeklyData));

        $this->info('Notification envoyé avec succès.');
    }
}

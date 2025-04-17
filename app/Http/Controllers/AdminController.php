<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Salle;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Afficher le tableau de bord administrateur
     */
    public function dashboard(Request $request)
    {
        $weekType = $request->input('weekType', 'current');

        $now = Carbon::now();

        if ($weekType === 'previous') {
            // Semaine précédente
            $startDate = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
            $endDate = $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY);
        } elseif ($weekType === 'next') {
            // Semaine suivante
            $startDate = $now->copy()->addWeek()->startOfWeek(Carbon::MONDAY);
            $endDate = $now->copy()->addWeek()->endOfWeek(Carbon::SUNDAY);
        } else {
            // Semaine courante (par défaut)
            $startDate = $now->copy()->startOfWeek(Carbon::MONDAY);
            $endDate = $now->copy()->endOfWeek(Carbon::SUNDAY);
        }

        $salles = Salle::all();

        $roomStats = $this->calculateRoomStats($salles, $startDate, $endDate);

        $topUsers = $this->getTopUsers($startDate, $endDate);
        // Préparer les données pour le graphique
        $chartData = $this->prepareWeeklyChartData($salles, $startDate, $endDate);

        return view('admin.dashboard', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'roomStats' => $roomStats,
            'topUsers' => $topUsers,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Préparer les données pour le graphique hebdomadaire
     */
    private function prepareWeeklyChartData($salles, $startDate, $endDate)
    {
        $labels = [];
        $data = [];

        // Génération des données par jour sur une semaine
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            // Nom du jour pour l'étiquette
            $labels[] = $date->translatedFormat('l');

            $dayStart = (clone $date)->startOfDay();
            $dayEnd = (clone $date)->endOfDay();

            // Calculer le taux d'occupation pour ce jour
            $occupancyRate = $this->calculateDailyOccupancyRate($salles, $dayStart, $dayEnd);
            $data[] = $occupancyRate;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Calculer le taux d'occupation journalier
     */
    private function calculateDailyOccupancyRate($salles, $startDate, $endDate)
    {
        // Heures ouvrables (8h à 18h = 10 heures par jour)
        $workingHoursPerDay = 10;
        $totalPossibleHours = $salles->count() * $workingHoursPerDay;

        if ($totalPossibleHours === 0) {
            return 0;
        }

        // Calculer le nombre total d'heures réservées pendant cette période
        $reservedHours = 0;

        foreach ($salles as $salle) {
            $reservations = Reservation::where('salle_id', $salle->id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('heure_debut', [$startDate, $endDate])
                        ->orWhereBetween('heure_fin', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('heure_debut', '<=', $startDate)
                                ->where('heure_fin', '>=', $endDate);
                        });
                })
                ->get();

            foreach ($reservations as $reservation) {
                $debut = Carbon::parse($reservation->heure_debut);
                $fin = Carbon::parse($reservation->heure_fin);

                // Ajuster les dates si nécessaire pour ne compter que les heures dans la période
                if ($debut < $startDate) {
                    $debut = clone $startDate;
                }
                if ($fin > $endDate) {
                    $fin = clone $endDate;
                }

                // Calculer directement la différence d'heures sans limiter aux heures ouvrables
                // pour prendre en compte toute la durée de la réservation
                if ($debut < $fin) {
                    $reservedHours += $debut->floatDiffInHours($fin);
                }
            }
        }

        return round($reservedHours / $totalPossibleHours * 100, 1);
    }

    /**
     * Calculer les statistiques par salle
     */
    private function calculateRoomStats($salles, $startDate, $endDate)
    {
        $stats = [];

        foreach ($salles as $salle) {
            $reservations = Reservation::where('salle_id', $salle->id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('heure_debut', [$startDate, $endDate])
                        ->orWhereBetween('heure_fin', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('heure_debut', '<=', $startDate)
                                ->where('heure_fin', '>=', $endDate);
                        });
                })
                ->get();

            // Calculer le nombre total d'heures réservées
            $totalHours = 0;
            foreach ($reservations as $reservation) {
                $debut = Carbon::parse($reservation->heure_debut);
                $fin = Carbon::parse($reservation->heure_fin);

                // Ajuster les dates si nécessaire
                if ($debut < $startDate) {
                    $debut = clone $startDate;
                }
                if ($fin > $endDate) {
                    $fin = clone $endDate;
                }

                // Calculer directement la différence d'heures sans limiter aux heures ouvrables
                // pour prendre en compte toute la durée de la réservation
                $totalHours += $debut->floatDiffInHours($fin);
            }

            // Calculer le taux d'occupation
            $workingDays = $startDate->diffInWeekdays($endDate) + 1;
            $totalPossibleHours = $workingDays * 10; // 10 heures ouvrables par jour
            $occupancyRate = $totalPossibleHours > 0 ? round($totalHours / $totalPossibleHours * 100, 1) : 0;

            $stats[] = [
                'id' => $salle->id,
                'name' => $salle->nom,
                'reservationCount' => $reservations->count(),
                'hoursReserved' => round($totalHours, 1),
                'occupancyRate' => $occupancyRate,
            ];
        }

        return $stats;
    }

    /**
     * Récupérer les 5 utilisateurs ayant fait le plus de réservations
     */
    private function getTopUsers($startDate, $endDate)
    {
        $topUsers = User::withCount(['reservations' => function ($query) use ($startDate, $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('heure_debut', [$startDate, $endDate])
                    ->orWhereBetween('heure_fin', [$startDate, $endDate])
                    ->orWhere(function ($subq) use ($startDate, $endDate) {
                        $subq->where('heure_debut', '<=', $startDate)
                            ->where('heure_fin', '>=', $endDate);
                    });
            });
        },
        ])
            ->having('reservations_count', '>', 0)
            ->orderBy('reservations_count', 'desc')
            ->take(5)
            ->get();

        $result = [];

        foreach ($topUsers as $user) {
            $reservations = $user->reservations()
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('heure_debut', [$startDate, $endDate])
                        ->orWhereBetween('heure_fin', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('heure_debut', '<=', $startDate)
                                ->where('heure_fin', '>=', $endDate);
                        });
                })
                ->get();

            $totalHours = 0;
            foreach ($reservations as $reservation) {
                $debut = Carbon::parse($reservation->heure_debut);
                $fin = Carbon::parse($reservation->heure_fin);

                // Ajuster les dates si nécessaire
                if ($debut < $startDate) {
                    $debut = clone $startDate;
                }
                if ($fin > $endDate) {
                    $fin = clone $endDate;
                }

                // Calculer directement la différence d'heures sans limiter aux heures ouvrables
                // pour prendre en compte toute la durée de la réservation
                $totalHours += $debut->floatDiffInHours($fin);
            }

            $result[] = [
                'id' => $user->id,
                'name' => $user->prenom . ' ' . $user->nom,
                'reservationCount' => $reservations->count(),
                'totalHours' => round($totalHours, 1),
            ];
        }

        return $result;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Salle;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Afficher le tableau de bord administrateur
     */
    public function dashboard()
    {
        // Récupérer la date du premier jour du mois courant par défaut
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $periodType = 'month';
        
        // Récupérer toutes les salles
        $salles = Salle::all();
        
        // Déterminer la date de fin en fonction du type de période
        $endDate = Carbon::parse($startDate)->endOfMonth();
        
        // Récupérer les statistiques par salle
        $roomStats = $this->calculateRoomStats($salles, Carbon::parse($startDate), $endDate);
        
        // Récupérer les top utilisateurs
        $topUsers = $this->getTopUsers(Carbon::parse($startDate), $endDate);
        
        // Préparer les données pour le graphique
        $chartData = $this->prepareChartData($salles, Carbon::parse($startDate), $endDate, $periodType);
        
        return view('admin.dashboard', [
            'startDate' => $startDate,
            'periodType' => $periodType,
            'roomStats' => $roomStats,
            'topUsers' => $topUsers,
            'chartData' => $chartData
        ]);
    }

    /**
     * Récupérer les statistiques d'occupation des salles
     */
    public function getStats(Request $request)
    {
        // Récupérer les paramètres de la requête
        $periodType = $request->input('periodType', 'week');
        $startDate = $request->input('startDate', Carbon::now()->startOfWeek()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate);
        
        // Déterminer la date de fin en fonction du type de période
        if ($periodType === 'week') {
            $endDate = (clone $startDate)->addDays(6);
            $periodLabel = 'Semaine du ' . $startDate->format('d/m/Y');
        } else {
            $endDate = (clone $startDate)->endOfMonth();
            $periodLabel = 'Mois de ' . $startDate->translatedFormat('F Y');
        }
        
        // Récupérer toutes les salles
        $salles = Salle::all();
        
        // Préparer les données pour le graphique
        $chartData = $this->prepareChartData($salles, $startDate, $endDate, $periodType);
        
        // Calculer les statistiques par salle
        $roomStats = $this->calculateRoomStats($salles, $startDate, $endDate);
        
        // Récupérer les top utilisateurs
        $topUsers = $this->getTopUsers($startDate, $endDate);
        
        return response()->json([
            'chartData' => $chartData,
            'roomStats' => $roomStats,
            'topUsers' => $topUsers,
            'periodLabel' => $periodLabel
        ]);
    }
    
    /**
     * Préparer les données pour le graphique
     */
    private function prepareChartData($salles, $startDate, $endDate, $periodType)
    {
        $labels = [];
        $data = [];
        
        if ($periodType === 'week') {
            // Génération des données par jour sur une semaine
            $period = CarbonPeriod::create($startDate, $endDate);
            
            foreach ($period as $date) {
                $labels[] = $date->translatedFormat('l');
                
                $dayStart = (clone $date)->startOfDay();
                $dayEnd = (clone $date)->endOfDay();
                
                // Calculer le taux d'occupation pour ce jour
                $occupancyRate = $this->calculateDailyOccupancyRate($salles, $dayStart, $dayEnd);
                $data[] = $occupancyRate;
            }
        } else {
            // Génération des données par semaine sur un mois
            $currentDate = clone $startDate;
            
            while ($currentDate <= $endDate) {
                $weekStart = (clone $currentDate)->startOfWeek();
                $weekEnd = (clone $weekStart)->endOfWeek();
                
                if ($weekEnd > $endDate) {
                    $weekEnd = clone $endDate;
                }
                
                $labels[] = 'Sem. ' . $weekStart->weekOfYear;
                
                // Calculer le taux d'occupation pour cette semaine
                $occupancyRate = $this->calculateDailyOccupancyRate($salles, $weekStart, $weekEnd);
                $data[] = $occupancyRate;
                
                $currentDate->addWeek();
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
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
                
                // Ne compter que les heures ouvrables (8h à 18h)
                $debutHour = $debut->hour;
                $finHour = $fin->hour;
                
                if ($debutHour < 8) {
                    $debut->setTime(8, 0, 0);
                }
                
                if ($finHour > 18) {
                    $fin->setTime(18, 0, 0);
                }
                
                if ($debut < $fin) {
                    $reservedHours += $debut->floatDiffInHours($fin);
                }
            }
        }
        
        return round(($reservedHours / $totalPossibleHours) * 100, 1);
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
                
                // Ne compter que les heures ouvrables (8h à 18h)
                $debutHour = $debut->hour;
                $finHour = $fin->hour;
                
                if ($debutHour < 8) {
                    $debut->setTime(8, 0, 0);
                }
                
                if ($finHour > 18) {
                    $fin->setTime(18, 0, 0);
                }
                
                // Ne compter que les heures ouvrables (8h à 18h)
                $debutHour = $debut->hour;
                $finHour = $fin->hour;
                
                if ($debutHour < 8) {
                    $debut->setTime(8, 0, 0);
                }
                
                if ($finHour > 18) {
                    $fin->setTime(18, 0, 0);
                }
                
                $totalHours += $debut->floatDiffInHours($fin);
            }
            
            // Calculer le taux d'occupation
            $workingDays = $startDate->diffInWeekdays($endDate) + 1;
            $totalPossibleHours = $workingDays * 10; // 10 heures ouvrables par jour
            $occupancyRate = ($totalPossibleHours > 0) ? round(($totalHours / $totalPossibleHours) * 100, 1) : 0;
            
            $stats[] = [
                'id' => $salle->id,
                'name' => $salle->nom,
                'capacity' => $salle->capacite,
                'reservationCount' => $reservations->count(),
                'hoursReserved' => round($totalHours, 1),
                'occupancyRate' => $occupancyRate
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
            }])
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
                
                // Ne compter que les heures ouvrables (8h à 18h)
                $debutHour = $debut->hour;
                $finHour = $fin->hour;
                
                if ($debutHour < 8) {
                    $debut->setTime(8, 0, 0);
                }
                
                if ($finHour > 18) {
                    $fin->setTime(18, 0, 0);
                }
                
                $totalHours += $debut->floatDiffInHours($fin);
            }
            
            $result[] = [
                'id' => $user->id,
                'name' => $user->prenom . ' ' . $user->nom,
                'reservationCount' => $reservations->count(),
                'totalHours' => round($totalHours, 1)
            ];
        }
        
        return $result;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Salle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReservationController extends Controller
{
    /**
     * Affiche la liste des réservations
     */
    public function index(): View
    {
        $reservations = Reservation::with(['user', 'salle'])
            ->orderBy('heure_debut', 'asc')
            ->get();

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Affiche mes réservations
     */
    public function mesReservations(): View
    {
        $reservations = Reservation::with(['salle'])
            ->where('user_id', Auth::id())
            ->orderBy('heure_debut', 'asc')
            ->get();

        return view('reservations.mes-reservations', compact('reservations'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(): View
    {
        $salles = Salle::all();

        return view('reservations.create', compact('salles'));
    }

    /**
     * Enregistre une nouvelle réservation
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'salle_id' => 'required|exists:salles,id',
            'date' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required',
            'heure_fin' => 'required|after:heure_debut',
        ]);

        // Formatage des dates et heures
        $dateDebut = Carbon::parse($validated['date'] . ' ' . $validated['heure_debut']);
        $dateFin = Carbon::parse($validated['date'] . ' ' . $validated['heure_fin']);

        // Vérification des conflits de réservation
        $existingReservation = Reservation::where('salle_id', $validated['salle_id'])
            ->where(function ($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('heure_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('heure_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($q) use ($dateDebut, $dateFin) {
                        $q->where('heure_debut', '<=', $dateDebut)
                            ->where('heure_fin', '>=', $dateFin);
                    });
            })
            ->exists();

        if ($existingReservation) {
            return back()->withInput()->with('error', 'Cette salle est déjà réservée pour ce créneau horaire.');
        }

        // Création de la réservation
        Reservation::create([
            'salle_id' => $validated['salle_id'],
            'user_id' => Auth::id(),
            'heure_debut' => $dateDebut,
            'heure_fin' => $dateFin,
        ]);

        return redirect()->route('reservations.mes-reservations')
            ->with('success', 'Réservation créée avec succès.');
    }

    /**
     * Affiche les détails d'une réservation
     */
    public function show(Reservation $reservation): View
    {
        return view('reservations.show', compact('reservation'));
    }

    /**
     * Supprime une réservation
     */
    public function destroy(Reservation $reservation): RedirectResponse
    {
        // Vérifie si l'utilisateur est le propriétaire ou un administrateur
        if (Auth::id() !== $reservation->user_id && Auth::user()->role !== 'administrateur') {
            abort(403, 'Accès non autorisé');
        }

        $reservation->delete();

        return redirect()->route('reservations.mes-reservations')
            ->with('success', 'Réservation annulée avec succès.');
    }
}

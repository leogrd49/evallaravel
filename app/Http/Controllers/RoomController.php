<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Affiche la liste des salles
     */
    public function index(): View
    {
        $rooms = Room::all();

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(): View
    {
        // Vérifie si l'utilisateur est admin
        if (! Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }

        return view('rooms.create');
    }

    /**
     * Enregistre une nouvelle salle
     */
    public function store(Request $request): RedirectResponse
    {
        // Vérifie si l'utilisateur est admin
        if (! Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'equipment' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Salle créée avec succès');
    }

    /**
     * Affiche les détails d'une salle
     */
    public function show(Room $room): View
    {
        return view('rooms.show', compact('room'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Room $room): View
    {
        // Vérifie si l'utilisateur est admin
        if (! Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }

        return view('rooms.edit', compact('room'));
    }

    /**
     * Met à jour une salle
     */
    public function update(Request $request, Room $room): RedirectResponse
    {
        // Vérifie si l'utilisateur est admin
        if (! Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'equipment' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Salle mise à jour avec succès');
    }

    /**
     * Supprime une salle
     */
    public function destroy(Room $room): RedirectResponse
    {
        // Vérifie si l'utilisateur est admin
        if (! Auth::user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifie si la salle a des réservations
        if ($room->reservations()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette salle car elle possède des réservations');
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Salle supprimée avec succès');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalleController extends Controller
{
    /**
     * Affiche la liste des salles
     */
    public function index(): View
    {
        $salles = Salle::all();

        return view('salles.index', compact('salles'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(): View
    {
        // Vérifie si l'utilisateur est admin
        if (Auth::user()->role !== 'administrateur') {
            abort(403, 'Accès non autorisé');
        }

        return view('salles.create');
    }

    /**
     * Enregistre une nouvelle salle
     */
    public function store(Request $request): RedirectResponse
    {
        // Vérifie si l'utilisateur est admin
        if (Auth::user()->role !== 'administrateur') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:salles',
            'capacite' => 'required|integer|min:1',
            'surface' => 'required|numeric|min:0',
        ]);

        Salle::create($validated);

        return redirect()->route('salles.index')
            ->with('success', 'Salle créée avec succès');
    }

    /**
     * Affiche les détails d'une salle
     */
    public function show(Salle $salle): View
    {
        return view('salles.show', compact('salle'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Salle $salle): View
    {
        // Vérifie si l'utilisateur est admin
        if (Auth::user()->role !== 'administrateur') {
            abort(403, 'Accès non autorisé');
        }

        return view('salles.edit', compact('salle'));
    }

    /**
     * Met à jour une salle
     */
    public function update(Request $request, Salle $salle): RedirectResponse
    {
        // Vérifie si l'utilisateur est admin
        if (Auth::user()->role !== 'administrateur') {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:salles,nom,' . $salle->id,
            'capacite' => 'required|integer|min:1',
            'surface' => 'required|numeric|min:0',
        ]);

        $salle->update($validated);

        return redirect()->route('salles.index')
            ->with('success', 'Salle mise à jour avec succès');
    }

    /**
     * Supprime une salle
     */
    public function destroy(Salle $salle): RedirectResponse
    {
        // Vérifie si l'utilisateur est admin
        if (Auth::user()->role !== 'administrateur') {
            abort(403, 'Accès non autorisé');
        }

        // Vérifie si la salle a des réservations
        if ($salle->reservations()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette salle car elle possède des réservations');
        }

        $salle->delete();

        return redirect()->route('salles.index')
            ->with('success', 'Salle supprimée avec succès');
    }
}

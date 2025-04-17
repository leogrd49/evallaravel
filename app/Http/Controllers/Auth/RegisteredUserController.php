<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $this->createUser([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Désactivation de l'événement Registered pour éviter l'envoi d'email de vérification
        // event(new Registered($user));

        // Auth::login($user);
        // On ne fait pas d'authentification ici pour éviter la double authentification

        return redirect(route('login', absolute: false))->with('status', 'Votre compte a été créé avec succès. Veuillez vous connecter.');
    }

    /**
     * Create a new user instance.
     * 
     * @param array<string, string> $input
     * @return User
     */
    protected function createUser(array $input): User
    {
        return User::create([
            'nom' => $input['nom'],
            'prenom' => $input['prenom'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => 'employe',
            'email_verified_at' => now(), // Marquer automatiquement l'email comme vérifié
        ]);
    }
}

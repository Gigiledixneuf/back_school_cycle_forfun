<?php

namespace App\Http\Controllers\Auth;

// Importation des événements et classes nécessaires
use App\Events\UserRegistered; // (optionnel, semble inutilisé ici)
use App\Events\UserRegisteredEvent; // Événement personnalisé déclenché après l'inscription
use App\Http\Controllers\Controller;
use App\Models\User; // Modèle User
use Illuminate\Auth\Events\Registered; // Événement standard Laravel (non utilisé ici)
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Validator; // (inutilisé ici mais peut servir pour des validations personnalisées)

class RegisteredUserController extends Controller
{
    /**
     * Gère l'inscription d'un nouvel utilisateur.
     *
     * @param Request $request La requête HTTP contenant les données d'inscription
     * @return Response Réponse HTTP sans contenu (204) en cas de succès
     * 
     * @throws \Illuminate\Validation\ValidationException Si la validation échoue
     */
    public function store(Request $request): Response
    {
        // ✅ Étape 1 : Validation des données d'entrée
        $request->validate([
            'name' => ['required', 'string', 'max:255'], // nom obligatoire
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', 'unique:' . User::class], // email valide et unique
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // mot de passe confirmé (password_confirmation requis)
            'role' => 'in:admin,tutor|nullable' // rôle facultatif mais limité à "admin" ou "tutor"
        ]);

        // ✅ Étape 2 : Création de l'utilisateur dans la base de données
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // hachage du mot de passe
            'role' => $request->role ?? 'tutor' // rôle par défaut = "tutor" si non fourni
        ]);

        // ✅ Étape 3 : Lancement d’un événement personnalisé après l’inscription
        event(new UserRegisteredEvent($user));

        // ✅ Étape 4 : Réponse vide avec le code HTTP 204 (No Content)
        return response()->noContent();
    }
}

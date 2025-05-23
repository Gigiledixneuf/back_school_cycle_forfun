<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\Auth\AuthLoginResource;

class AuthenticatedSessionController extends Controller
{
    /**
     * Gère une requête de connexion (authentification).
     *
     * @param LoginRequest $request Requête contenant les données de connexion (email + mot de passe validés)
     * @return AuthLoginResource|JsonResponse Retourne soit un objet ressource avec token, soit une réponse d'erreur
     */
    public function store(LoginRequest $request): AuthLoginResource | JsonResponse
    {
        // Recherche de l'utilisateur par son adresse email
        $user = User::where('email', '=', $request->validated('email'))->first();

        // Vérifie si l'utilisateur existe et si le mot de passe est correct
        if (!($user instanceof User) || !Hash::check($request->validated('password'), $user->password)) {
            // Si l'utilisateur n'existe pas ou le mot de passe est incorrect, on retourne une erreur 401
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if(!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Votre adresse email n\'est pas encore verifiée.'], 401);
        }

        // Génération d’un token d’authentification via Laravel Sanctum
        $token = $user->createToken($user->email)->plainTextToken;

        // Ajoute manuellement le token dans l’objet utilisateur (pour pouvoir l'envoyer dans la ressource)
        $user->token = $token;

        // Retourne la ressource contenant les infos utilisateur + token
        return new AuthLoginResource($user);
    }

    /**
     * Déconnecte l'utilisateur (suppression du token d'accès actuel).
     *
     * @param Request $request La requête contenant l'utilisateur authentifié
     * @return JsonResponse Réponse confirmant la déconnexion
     */
    public function destroy(Request $request): JsonResponse
    {
        $user  = $request->user(); // Récupère l'utilisateur actuellement connecté via le token

        // Vérifie si l'utilisateur est bien authentifié
        if (!($user instanceof User)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Supprime le token actuel (le déconnecte)
        $request->user()->currentAccessToken()->delete();

        // Réponse de confirmation
        return response()->json(['logout' => true]);
    }
}

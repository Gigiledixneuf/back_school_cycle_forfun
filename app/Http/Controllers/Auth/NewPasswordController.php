<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Gère la réinitialisation du mot de passe d'un utilisateur.
     *
     * @param Request $request La requête contenant le token, l'email, et le nouveau mot de passe
     * @return JsonResponse Réponse JSON avec le statut de l'opération
     *
     * @throws ValidationException Si les données sont invalides ou si la réinitialisation échoue
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des champs envoyés par le formulaire
        $request->validate([
            'token' => ['required'], // Le token de réinitialisation (envoyé par e-mail)
            'email' => ['required', 'email'], // L'adresse e-mail de l'utilisateur
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // Nouveau mot de passe + confirmation + règles Laravel par défaut
        ]);

        // Tentative de réinitialisation du mot de passe via le broker Laravel
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'), // Données nécessaires à la réinitialisation
            function ($user) use ($request) {
                // Mise à jour du mot de passe et du remember_token (pour invalider les sessions existantes)
                $user->forceFill([
                    'password' => Hash::make($request->string('password')), // Hachage sécurisé du mot de passe
                    'remember_token' => Str::random(60), // Nouveau token de session
                ])->save();

                // Déclenchement de l'événement PasswordReset (utile pour logs, notifications, etc.)
                event(new PasswordReset($user));
            }
        );

        // Si la réinitialisation a échoué, on renvoie une exception de validation avec le message d'erreur
        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        // Si tout a bien fonctionné, on retourne un message de succès
        return response()->json(['status' => __($status)]);
    }
}

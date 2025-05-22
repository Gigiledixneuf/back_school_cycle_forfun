<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Gère l'envoi d'un lien de réinitialisation de mot de passe à l'utilisateur.
     *
     * @param Request $request Requête contenant l'adresse e-mail
     * @return JsonResponse Réponse JSON avec le statut de l'envoi
     *
     * @throws ValidationException Si l'adresse e-mail est invalide ou non reconnue
     */
    public function store(Request $request): JsonResponse
    {
        // On valide que l'utilisateur a bien envoyé une adresse e-mail valide
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // On tente d'envoyer le lien de réinitialisation via le système de mot de passe Laravel
        $status = Password::sendResetLink(
            $request->only('email') // On extrait uniquement l'e-mail de la requête
        );

        // Si l'envoi a échoué (ex: l'e-mail n'est pas associé à un utilisateur), on lance une exception avec un message d'erreur
        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        // Si l'envoi du lien a réussi, on retourne une réponse JSON avec le message de succès
        return response()->json(['status' => __($status)]);
    }
}

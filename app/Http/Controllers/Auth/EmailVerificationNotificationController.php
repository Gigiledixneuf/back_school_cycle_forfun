<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Envoie un nouvel e-mail de vérification à l'utilisateur connecté.
     *
     * @param Request $request La requête HTTP contenant l'utilisateur authentifié
     * @return JsonResponse Une réponse JSON indiquant le statut de l'envoi
     */
    public function store(Request $request): JsonResponse
    {
        // Vérifie si l'utilisateur a déjà vérifié son adresse e-mail
        if ($request->user()->hasVerifiedEmail()) {
            // Si c'est le cas, on retourne une réponse indiquant que le lien a déjà été validé
            return response()->json(['status' => 'verification-link-already']);
        }

        // Si l'e-mail n'a pas encore été vérifié, on envoie un nouveau lien de vérification
        $request->user()->sendEmailVerificationNotification();

        // Réponse confirmant l'envoi du lien
        return response()->json(['status' => 'verification-link-sent']);
    }
}

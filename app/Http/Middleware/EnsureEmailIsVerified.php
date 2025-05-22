<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Gère une requête entrante.
     * 
     * Ce middleware vérifie si l'utilisateur connecté a une adresse e-mail vérifiée.
     * 
     * @param  \Illuminate\Http\Request  $request La requête HTTP entrante
     * @param  \Closure  $next La prochaine étape à exécuter dans la chaîne de middleware
     * @return \Symfony\Component\HttpFoundation\Response La réponse HTTP
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie s'il n'y a pas d'utilisateur connecté,
        // ou si l'utilisateur doit vérifier son e-mail
        // et que l'e-mail n'a pas encore été vérifié
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            
            // Retourne une réponse JSON avec un message d'erreur
            // et un code HTTP 409 (Conflict)
            return response()->json(['message' => 'Your email address is not verified.'], 409);
        }

        // Si l'utilisateur est authentifié et que son e-mail est vérifié,
        // la requête est transmise au middleware suivant / contrôleur
        return $next($request);
    }
}

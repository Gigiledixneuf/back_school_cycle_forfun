<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Intercepte une requête entrante et la modifie pour s'assurer
     * que la réponse soit au format JSON pour toutes les routes API.
     *
     * @param \Illuminate\Http\Request $request La requête HTTP reçue
     * @param \Closure $next La fonction qui permet de passer au middleware ou contrôleur suivant
     * @return \Symfony\Component\HttpFoundation\Response La réponse HTTP
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'URL de la requête commence par "api/", cela signifie que c'est une route d'API
        if ($request->is('api/*')) {
            // On force le header "Accept" à "application/json"
            // Cela garantit que les réponses seront formatées en JSON
            $request->headers->set('Accept', 'application/json');
        }

        // On continue le traitement de la requête (middleware suivant ou contrôleur)
        return $next($request);
    }
}

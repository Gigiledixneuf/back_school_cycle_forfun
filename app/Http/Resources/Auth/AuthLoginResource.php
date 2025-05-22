<?php
namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthLoginResource extends JsonResource
{
    /**
     * Transforme la ressource en un tableau.
     *
     * Cette méthode est appelée quand on retourne la ressource dans une réponse JSON.
     * Elle permet de personnaliser les données envoyées au client lors d'une connexion réussie.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP entrante
     * @return array<string, mixed> Le tableau des données formatées à retourner au client
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,                      // L'identifiant unique de l'utilisateur
            'name' => $this->name,                  // Le nom de l'utilisateur
            'email' => $this->email,                // L'adresse e-mail de l'utilisateur
            'email_verified_at' => $this->email_verified_at, // Date et heure de vérification de l'e-mail (null si non vérifié)
            'token' => $this->token,                // Le token d'authentification généré (ex: JWT ou token API)
        ];
    }
}

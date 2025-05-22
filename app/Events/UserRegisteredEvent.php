<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegisteredEvent implements ShouldQueue
{
    // Utilise le trait pour sérialiser proprement les modèles Eloquent (ici User) quand l'événement est mis en file
    use SerializesModels;

    /**
     * Crée une nouvelle instance de l'événement.
     * Grâce au constructeur, on passe l'utilisateur nouvellement inscrit à tous les listeners.
     *
     * @param User $user L'utilisateur qui vient de s'enregistrer
     */
    public function __construct(public User $user)
    {
        // L'utilisateur est automatiquement stocké dans une propriété publique
        // grâce à la nouvelle syntaxe de promotion des propriétés en PHP 8+
    }
}

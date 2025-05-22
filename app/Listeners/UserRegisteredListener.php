<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Mail\UserRegisteredMail;
use Illuminate\Contracts\Mail\Mailer;

class UserRegisteredListener
{
    /**
     * Crée une instance du listener.
     *
     * Le Mailer est injecté via le constructeur pour permettre l'envoi d'emails.
     *
     * @param Mailer $mailer Le service d'envoi d'emails de Laravel
     */
    public function __construct(private Mailer $mailer)
    {
    }

    /**
     * Gère l'événement UserRegisteredEvent.
     *
     * Cette méthode est appelée automatiquement quand l'événement UserRegisteredEvent est déclenché.
     * Elle envoie un email de bienvenue à l'utilisateur qui vient de s'inscrire.
     *
     * @param UserRegisteredEvent $event L'événement contenant les données liées à l'utilisateur inscrit
     * @return void
     */
    public function handle(UserRegisteredEvent $event): void
    {
        // Envoi d'un email en utilisant la classe mailable UserRegisteredMail,
        // en lui passant l'utilisateur inscrit ($event->user)
        $this->mailer->send(new UserRegisteredMail($event->user));
    }
}

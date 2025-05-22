<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class UserRegisteredMail extends Mailable
{
    use Queueable, SerializesModels; 
    // Traits pour gérer la mise en file d'attente (queue) des emails et la sérialisation des modèles

    /**
     * Crée une nouvelle instance du message.
     *
     * Le modèle User est injecté ici, ce qui permet d'accéder aux données de l'utilisateur dans l'email.
     *
     * @param User $user L'utilisateur inscrit
     */
    public function __construct(public User $user)
    {
    }

    /**
     * Définit l'enveloppe du message (métadonnées de l'email).
     * 
     * Ici, on définit l'objet du mail ainsi que le destinataire.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'User Registered Mail', // Sujet de l'email
            to: [$this->user->email]          // Email du destinataire (l'utilisateur inscrit)
        );
    }

    /**
     * Définit le contenu du message.
     * 
     * On utilise ici un template Markdown pour la mise en forme du mail.
     * On transmet aussi des variables au template (l'utilisateur et l'URL signée).
     *
     * @return Content
     */
    public function content(): Content
    {
        $url = $this->getUrlSigned(); // Génère une URL signée pour vérification d'email

        return new Content(
            markdown: 'mail.user-registered-mail',  // Vue Markdown pour l'email
            with: [
                'user' => $this->user,               // Données utilisateur à passer au template
                'url' => $url,                       // URL de vérification à inclure dans l'email
            ]
        );
    }

    /**
     * Retourne la liste des pièces jointes (vide ici).
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Génère une URL signée temporaire pour la vérification de l'adresse email.
     *
     * Cette URL permet à l'utilisateur de confirmer son adresse email en toute sécurité.
     * L'URL est valide 60 minutes.
     *
     * @return string L'URL signée à destination du frontend
     */
    private function getUrlSigned(): string
    {
        // Génération d'une route temporaire signée côté backend (Laravel)
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',          // Nom de la route backend de vérification d'email
            now()->addMinutes(60),          // Durée de validité : 60 minutes
            [
                'id' => $this->user->id,    // Paramètre id de l'utilisateur
                'hash' => sha1($this->user->email), // Hash SHA1 de l'email (sécurité)
            ]
        );

        // Parse l'URL générée pour récupérer la query string
        $parsed = parse_url($signedUrl);
        parse_str($parsed['query'] ?? '', $queryParams);

        // Construit une URL vers le frontend (ex: SPA) en passant les paramètres signés
        // config('app.frontend_url') correspond à l'URL du frontend (ex: http://localhost:4200)
        return config('app.frontend_url') . '/verify-email/' . $this->user->id . '/' . sha1($this->user->email) . '?' . http_build_query($queryParams);

    }
}

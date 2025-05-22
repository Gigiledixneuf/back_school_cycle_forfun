<x-mail::message>
    # 🎉 Vérifiez votre adresse e-mail

    <p style="font-style: italic; font-size: 1.1rem; color: #555;">
        Bonjour {{ $user->name }},
    </p>

    <p style="font-size: 1rem; color: #333;">
        Merci beaucoup pour votre inscription sur <strong>{{ config('app.name') }}</strong> !  
        Pour finaliser votre inscription et accéder à toutes les fonctionnalités, veuillez vérifier votre adresse e-mail en cliquant sur le bouton ci-dessous :
    </p>

    <x-mail::button :url="$url" color="primary" style="border-radius: 8px; padding: 12px 24px; font-weight: bold;">
        Vérifier mon adresse e-mail
    </x-mail::button>

    <p style="margin-top: 20px; font-size: 0.9rem; color: #888;">
        Ce lien expirera dans <strong>60 minutes</strong>.
    </p>

    <p style="font-size: 0.9rem; color: #888;">
        Si vous n’avez pas créé de compte, vous pouvez simplement ignorer cet e-mail.
    </p>

    <p style="margin-top: 30px; font-weight: 600; color: #444;">
        Merci,<br>
        L’équipe <span style="color: #0069ff;">{{ config('app.name') }}</span>
    </p>
</x-mail::message>

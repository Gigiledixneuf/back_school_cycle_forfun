<x-mail::message>
    # 🎉 Vérifiez votre adresse e-mail

    Bonjour **{{ $user->name }}**,

    Merci beaucoup pour votre inscription sur **{{ config('app.name') }}** !
    Pour finaliser votre inscription et accéder à toutes les fonctionnalités, veuillez vérifier votre adresse e-mail en cliquant sur le bouton ci-dessous :

    <x-mail::button :url="$url" color="primary">
        Vérifier mon adresse e-mail
    </x-mail::button>

    Ce lien expirera dans **60 minutes**.

    Si vous n’avez pas créé de compte, vous pouvez simplement ignorer cet e-mail.

    Merci,
    L’équipe **{{ config('app.name') }}**
</x-mail::message>

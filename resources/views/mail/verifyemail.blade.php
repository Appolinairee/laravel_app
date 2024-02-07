@component('mail::message')
# Vérification de votre email

Bienvenue {{$username}} sur {{ config('app.name') }} !.
Cliquez sur le bouton ci-dessous pour vérifier votre adresse e-mail.

@component('mail::button', ['url' => $url])
Vérification d'email
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent

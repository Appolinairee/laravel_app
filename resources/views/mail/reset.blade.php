@component('mail::message')
# Réinitialisation de mot de passe

Cliquez sur le bouton ci-dessous pour changer votre mot de passe.

@component('mail::button', ['url' => $url])
Réinitialisation de mot de passe
@endcomponent

Merci, AtounAfrica n'est pas le même sans vous!<br>
{{ config('app.name') }}
@endcomponent

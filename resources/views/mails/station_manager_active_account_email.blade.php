<!DOCTYPE html>
<html>
<head>
    <title>Bienvenue à la gestion de la station</title>
</head>
    <body>
        <h1>Bienvenue à la gestion de la station !</h1>
        <p>Bonjour, {{ $user->name  }}</p>
        <p>Nous sommes ravis de vous accueillir en tant que manager pour la gestion de votre station.</p>
        <p>Pour activer votre compte, veuillez cliquer sur le lien ci-dessous :</p>
        <a href="{{ $activationLink }}">Activer mon compte</a>
        <p>Merci et bienvenue à bord !</p>
    </body>
</html>

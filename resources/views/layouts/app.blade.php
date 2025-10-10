<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat du Questionnaire {{ $type }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1>Résultat du Questionnaire {{ $type }}</h1>
        <p>Votre score : {{ $score }}</p>
        <p>Interprétation : {{ $interpretation }}</p>
        <p>Ceci est un indicateur scientifique de votre état psychologique. Consultez un professionnel si nécessaire.</p>
        <a href="{{ route('questionnaires.show', $type) }}">Recommencer</a>
    </div>
</body>
</html>
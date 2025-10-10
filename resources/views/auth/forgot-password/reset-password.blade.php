<!-- resources/views/auth/forgot-password/reset-password.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
</head>
<body>
    <h1>Réinitialiser votre mot de passe</h1>

    <form id="resetForm" action="{{ route('forgot.password.reset') }}" method="POST">
        @csrf
        <input type="hidden" name="email" value="{{ request()->email }}">
        <input type="hidden" name="otp" value="{{ request()->otp }}">

        <label>Nouveau mot de passe :</label>
        <input type="password" name="password" required>
        <br>
        <label>Confirmer le mot de passe :</label>
        <input type="password" name="password_confirmation" required>
        <br>
        <button type="submit">Réinitialiser</button>
    </form>

    <script>
        const form = document.getElementById('resetForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                alert(data.message); // affiche "Mot de passe réinitialisé avec succès"
                window.location.href = '/'; // redirige vers la page d'accueil ou login
            } else {
                alert(data.error || 'Erreur lors de la réinitialisation');
            }
        });
    </script>
</body>
</html>

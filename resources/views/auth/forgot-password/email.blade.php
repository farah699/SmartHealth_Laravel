<!DOCTYPE html>
<html>
<head>
    <title>Mot de passe oublié</title>
</head>
<body>
    <h2>Mot de passe oublié</h2>
    <form id="emailForm" method="POST" action="{{ route('forgot.password.sendOtp') }}">
        @csrf
        <input type="email" name="email" placeholder="Entrez votre email" required>
        <button type="submit">Envoyer OTP</button>
    </form>

    <script>
        const form = document.getElementById('emailForm');
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            try {
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
                    alert(data.message);
                    // Redirection vers verify-otp avec l'email
                    window.location.href = `/verify-otp?email=${encodeURIComponent(formData.get('email'))}`;
                } else {
                    alert(data.error || 'Erreur lors de l\'envoi');
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        });
    </script>
</body>
</html>
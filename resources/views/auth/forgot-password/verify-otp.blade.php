<!DOCTYPE html>
<html>
<head>
    <title>Vérification OTP</title>
</head>
<body>
    <h2>Vérification du code OTP</h2>
    <form id="otpForm" method="POST" action="{{ route('forgot.password.verifyOtp') }}">
        @csrf
        <input type="email" name="email" placeholder="Votre email" value="{{ request()->email }}" required>
        <input type="text" name="otp" placeholder="Entrez le code OTP" required>
        <button type="submit">Vérifier OTP</button>
    </form>

    <script>
        const form = document.getElementById('otpForm');
        
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
                    // Redirection vers reset-password avec email et otp
                    window.location.href = `/reset-password?email=${encodeURIComponent(formData.get('email'))}&otp=${encodeURIComponent(formData.get('otp'))}`;
                } else {
                    alert(data.error || 'Erreur lors de la vérification');
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        });
    </script>
</body>
</html>
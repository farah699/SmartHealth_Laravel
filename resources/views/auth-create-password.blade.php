
@extends('partials.layouts.master_auth')

@section('title', 'Create Password | FabKin Admin & Dashboards Template')

@section('content')
    <!-- START -->
    <div>
        <img src="assets/images/auth/login_bg.jpg" alt="Auth Background"
            class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
        <img src="assets/images/auth/auth_bg_dark.jpg" alt="Auth Background" class="auth-bg d-none dark">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100 py-10">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card mx-xxl-8">
                        <div class="card-body py-12 px-8">
                            <img src="assets/images/logo-dark.png" alt="Logo Dark" height="30"
                                class="mb-4 mx-auto d-block">
                            <h6 class="mb-3 mb-8 fw-medium text-center">Create a Strong Password</h6>
                            <form id="resetForm" action="{{ route('forgot.password.reset') }}" method="POST">
                                @csrf
                                <input type="hidden" name="email" value="{{ request()->email }}">
                                <input type="hidden" name="otp" value="{{ request()->otp }}">
                                <div class="row g-4">
                                    <div class="col-12 password-field-wrapper">
                                        <label for="password" class="form-label">Password :</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="password-field-wrapper">
                                        <label for="password_confirmation" class="form-label">Confirm Password :</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                    </div>
                                    <div class="error text-danger" id="confirmPasswordError"></div>
                                    <button type="submit" id="submitBtn" class="btn btn-primary w-full">Create Password</button>
                                    <p class="mb-0 fw-semibold position-relative text-center fs-12">Return to the? <a
                                            href="auth-signin" class="text-decoration-underline text-primary">Sign In
                                            here</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                    <p class="position-relative text-center fs-12 mb-0">© 2025 Fabkin. Crafted with ❤️ by Pixeleyez</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const form = document.getElementById('resetForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    const errorDiv = document.getElementById('confirmPasswordError');

    // Validation des mots de passe
    function validatePasswords() {
        if (password.value && confirmPassword.value) {
            if (password.value === confirmPassword.value) {
                errorDiv.textContent = '';
                submitBtn.disabled = false;
                return true;
            } else {
                errorDiv.textContent = 'Les mots de passe ne correspondent pas';
                submitBtn.disabled = true;
                return false;
            }
        }
        return false;
    }

    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!validatePasswords()) {
            Swal.fire({
                icon: 'warning',
                title: 'Mots de passe différents!',
                text: 'Les mots de passe ne correspondent pas',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Vérifier la longueur du mot de passe
        if (password.value.length < 8) {
            Swal.fire({
                icon: 'warning',
                title: 'Mot de passe trop court!',
                text: 'Le mot de passe doit contenir au moins 8 caractères',
                confirmButtonText: 'OK'
            });
            return;
        }

        const formData = new FormData(form);

        // Afficher un loader
        Swal.fire({
            title: 'Mise à jour en cours...',
            html: 'Veuillez patienter',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

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
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: data.message,
                    confirmButtonText: 'Se connecter'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/auth-signin'; // redirige vers la page de connexion
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: data.error || 'Erreur lors de la réinitialisation',
                    confirmButtonText: 'Réessayer'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur de connexion!',
                text: 'Veuillez vérifier votre connexion internet',
                confirmButtonText: 'OK'
            });
        }
    });
</script>
@endsection
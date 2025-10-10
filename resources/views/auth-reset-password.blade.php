
@extends('partials.layouts.master_auth')

@section('title', 'Reset Password | FabKin Admin & Dashboards Template')

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
                            <h6 class="mb-3 mb-8 fw-medium text-center">Log in with the new password once it's set.</h6>
                            <form id="emailForm" method="POST" action="{{ route('forgot.password.sendOtp') }}">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter your Email" required>
                                    </div>

                                    <div class="col-12 mt-8">
                                        <button type="submit" class="btn btn-primary w-full mb-5">Send reset email</button>
                                    </div>
                                </div>
                                <p class="mb-0 fw-semibold position-relative text-center fs-12">Back to <a
                                        href="auth-signin" class="text-decoration-underline text-primary">Log in</a></p>
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
    const form = document.getElementById('emailForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Afficher un loader
        Swal.fire({
            title: 'Envoi en cours...',
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
                    confirmButtonText: 'Continuer'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirection vers verify-otp avec l'email
                        window.location.href = `/auth-two-step-verify?email=${encodeURIComponent(formData.get('email'))}`;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: data.error || 'Erreur lors de l\'envoi',
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

@extends('partials.layouts.master_auth')

@section('title', 'Two Step Verification | FabKin Admin & Dashboards Template')

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
                            <img src="assets/images/auth/email.png" alt="Vector Image"
                                class="h-56px w-56px mx-auto d-block mb-3">
                            <h3 class="mb-2 text-center text-capitalize">Two-Factor Authentication</h3>
                            <p class="text-muted text-center">6 digit OTP is send to email <span
                                    class="fw-bold">{{ request()->email }}</span></p>
                            <form id="otpForm" method="POST" action="{{ route('forgot.password.verifyOtp') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ request()->email }}">
                                <div id="otp-container" class="d-flex align-items-center justify-content-center gap-2">
                                    <input type="text"
                                        class="form-control text-center min-h-50px border-0 border-bottom border-2 rounded-0"
                                        placeholder="0" data-otp-input maxlength="1" inputmode="numeric" pattern="[0-9]"
                                        aria-label="OTP digit 1">
                                    <input type="text"
                                        class="form-control text-center min-h-50px border-0 border-bottom border-2 rounded-0"
                                        placeholder="0" data-otp-input maxlength="1" inputmode="numeric" pattern="[0-9]"
                                        aria-label="OTP digit 2">
                                    <input type="text"
                                        class="form-control text-center min-h-50px border-0 border-bottom border-2 rounded-0"
                                        placeholder="0" data-otp-input maxlength="1" inputmode="numeric" pattern="[0-9]"
                                        aria-label="OTP digit 3">
                                    <input type="text"
                                        class="form-control text-center min-h-50px border-0 border-bottom border-2 rounded-0"
                                        placeholder="0" data-otp-input maxlength="1" inputmode="numeric" pattern="[0-9]"
                                        aria-label="OTP digit 4">
                                    <input type="text"
                                        class="form-control text-center min-h-50px border-0 border-bottom border-2 rounded-0"
                                        placeholder="0" data-otp-input maxlength="1" inputmode="numeric" pattern="[0-9]"
                                        aria-label="OTP digit 5">
                                    <input type="text"
                                        class="form-control text-center min-h-50px border-0 border-bottom border-2 rounded-0"
                                        placeholder="0" data-otp-input maxlength="1" inputmode="numeric" pattern="[0-9]"
                                        aria-label="OTP digit 6">
                                </div>
                                <input type="hidden" name="otp" id="otpValue">
                                <button type="submit" class="btn btn-primary rounded-2 w-100 btn-loader text-center mt-10">Verify OTP</button>
                                <p class="text-center mb-0 mt-3">
                                    <a href="javascript:void(0)" class="link link-primary text-body fw-medium fs-12">Having
                                        trouble getting the code?</a>
                                </p>
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
<script src="assets/js/auth/auth.init.js"></script>
<script>
    const form = document.getElementById('otpForm');
    const otpInputs = document.querySelectorAll('[data-otp-input]');
    const otpValue = document.getElementById('otpValue');

    // Collecte automatique de l'OTP quand tous les champs sont remplis
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            // Auto-focus sur le champ suivant
            if (this.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            
            // Collecte l'OTP complet
            let otp = '';
            otpInputs.forEach(inp => otp += inp.value);
            otpValue.value = otp;
        });
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Vérifier que l'OTP est complet
        if (formData.get('otp').length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Code incomplet!',
                text: 'Veuillez saisir les 6 chiffres du code OTP',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Afficher un loader
        Swal.fire({
            title: 'Vérification en cours...',
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
                    title: 'Code vérifié!',
                    text: data.message,
                    confirmButtonText: 'Continuer'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirection vers reset-password avec email et otp
                        window.location.href = `/auth-create-password?email=${encodeURIComponent(formData.get('email'))}&otp=${encodeURIComponent(formData.get('otp'))}`;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Code invalide!',
                    text: data.error || 'Erreur lors de la vérification',
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
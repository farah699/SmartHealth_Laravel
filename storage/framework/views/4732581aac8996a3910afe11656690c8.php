<?php $__env->startSection('title', 'Verify Registration | SmartHealth'); ?>

<?php $__env->startSection('content'); ?>
<!-- START -->
<div>
    <img src="<?php echo e(asset('assets/images/auth/login_bg.jpg')); ?>" alt="Auth Background"
        class="auth-bg light w-full h-full opacity-60 position-absolute top-0">
    <img src="<?php echo e(asset('assets/images/auth/auth_bg_dark.jpg')); ?>" alt="Auth Background" class="auth-bg d-none dark">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-10">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card mx-xxl-8">
                    <div class="card-body py-12 px-8">
                        <img src="<?php echo e(asset('assets/images/auth/email.png')); ?>" alt="Vector Image"
                            class="h-56px w-56px mx-auto d-block mb-3">
                        <h3 class="mb-2 text-center text-capitalize">Verify Your Registration</h3>
                        <p class="text-muted text-center">6 digit OTP is sent to email <span
                                class="fw-bold"><?php echo e($email); ?></span></p>
                        
                        <!-- Messages -->
                        <?php if(session('success')): ?>
                            <div class="alert alert-success">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div><?php echo e($error); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form id="otpForm" method="POST" action="<?php echo e(route('register.verify.otp.submit')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="email" value="<?php echo e($email); ?>">
                            
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
                            
                            <button type="submit" id="verifyBtn" class="btn btn-primary rounded-2 w-100 btn-loader text-center mt-10">
                                Verify Registration Code
                            </button>
                            
                            <p class="text-center mb-0 mt-3">
                                <a href="javascript:void(0)" id="resendOtp" class="link link-primary text-body fw-medium fs-12">
                                    Resend verification code
                                </a>
                            </p>
                            
                            <p class="text-center mb-0 mt-2">
                                <a href="<?php echo e(route('register')); ?>" class="link link-primary text-body fw-medium fs-12">
                                    ← Back to registration
                                </a>
                            </p>
                        </form>
                    </div>
                </div>
                <p class="position-relative text-center fs-12 mb-0">© 2025 SmartHealth. Crafted with ❤️</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo e(asset('assets/js/auth/auth.init.js')); ?>"></script>
<script>
    const form = document.getElementById('otpForm');
    const otpInputs = document.querySelectorAll('[data-otp-input]');
    const otpValue = document.getElementById('otpValue');
    const resendBtn = document.getElementById('resendOtp');

    // Focus sur le premier champ au chargement
    if (otpInputs.length > 0) {
        otpInputs[0].focus();
    }

    // Collecte automatique de l'OTP quand tous les champs sont remplis - identique à Two Step
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            // Permettre seulement les chiffres
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-focus sur le champ suivant
            if (this.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            
            // Collecte l'OTP complet
            let otp = '';
            otpInputs.forEach(inp => otp += inp.value);
            otpValue.value = otp;
        });

        // Gestion du backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        // Gestion du paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/[^0-9]/g, '').split('');
            
            digits.forEach((digit, i) => {
                if (i < otpInputs.length) {
                    otpInputs[i].value = digit;
                }
            });
            
            // Focus sur le dernier champ rempli
            const lastIndex = Math.min(digits.length - 1, otpInputs.length - 1);
            if (lastIndex >= 0) {
                otpInputs[lastIndex].focus();
            }
            
            // Collecte l'OTP complet
            let otp = '';
            otpInputs.forEach(inp => otp += inp.value);
            otpValue.value = otp;
        });
    });

    // Soumission du formulaire - identique à Two Step mais adapté pour registration
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
                    title: 'Compte vérifié!',
                    text: data.message,
                    confirmButtonText: 'Se connecter',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = data.redirect_url;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Code invalide!',
                    text: data.error || 'Erreur lors de la vérification',
                    confirmButtonText: 'Réessayer'
                });
                
                // Effacer les champs
                otpInputs.forEach(input => input.value = '');
                otpValue.value = '';
                if (otpInputs.length > 0) {
                    otpInputs[0].focus();
                }
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

    // Renvoyer le code
    resendBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        // Désactiver temporairement le lien
        const originalText = resendBtn.innerHTML;
        resendBtn.style.pointerEvents = 'none';
        resendBtn.style.color = '#ccc';
        resendBtn.innerHTML = 'Envoi en cours...';
        
        try {
            const response = await fetch('<?php echo e(route("register.resend.otp")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: '<?php echo e($email); ?>'
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Code renvoyé!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false
                });
                
                // Effacer les champs pour le nouveau code
                otpInputs.forEach(input => input.value = '');
                otpValue.value = '';
                if (otpInputs.length > 0) {
                    otpInputs[0].focus();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: data.error || 'Impossible de renvoyer le code',
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur!',
                text: 'Erreur de connexion',
            });
        }
        
        // Réactiver le lien après 30 secondes
        setTimeout(() => {
            resendBtn.style.pointerEvents = 'auto';
            resendBtn.style.color = '';
            resendBtn.innerHTML = originalText;
        }, 30000);
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.layouts.master_auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/auth-verify-registration.blade.php ENDPATH**/ ?>
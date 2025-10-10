<?php $__env->startSection('title', 'Sign Up | SmartHealth'); ?>

<?php $__env->startSection('content'); ?>

<!-- START -->
<div>
    <img src="<?php echo e(asset('assets/images/auth/login_bg.jpg')); ?>" alt="Auth Background"
        class="auth-bg light w-full h-full auth-bg-cover opacity-60 position-absolute top-0">
    <img src="<?php echo e(asset('assets/images/auth/auth_bg_dark.jpg')); ?>" alt="Auth Background" class="auth-bg d-none dark">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-10">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card mx-xxl-8">
                    <div class="card-body py-12 px-8">
                        <img src="<?php echo e(asset('assets/images/logo-dark.png')); ?>" alt="Logo Dark" height="30"
                            class="mb-4 mx-auto d-block">
                        <h6 class="mb-3 mb-8 fw-medium text-center">Create Your Account in Minutes</h6>

                        <!-- Messages d'erreur -->
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div><?php echo e($error); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('register')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="row g-4">
                                <div class="col-12">
                                    <label for="name" class="form-label">Username <span
                                            class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name"
                                           name="name"
                                           value="<?php echo e(old('name')); ?>"
                                           placeholder="Enter your username"
                                           required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="email"
                                           name="email"
                                           value="<?php echo e(old('email')); ?>"
                                           placeholder="Email"
                                           required>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-control <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="role" 
                                            name="role" 
                                            required>
                                        <option value="">Select your role</option>
                                        <option value="Student" <?php echo e(old('role') == 'Student' ? 'selected' : ''); ?>>Student</option>
                                        <option value="Teacher" <?php echo e(old('role') == 'Teacher' ? 'selected' : ''); ?>>Teacher</option>
                                    </select>
                                    <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12">
                                    <label for="password" class="form-label">Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="password"
                                           name="password"
                                           placeholder="Password"
                                           required>
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Confirm Password" 
                                           required>
                                </div>
                                <div class="col-12 mt-8">
                                    <button type="submit" class="btn btn-primary w-full mb-4">Sign Up<i
                                            class="bi bi-box-arrow-in-right ms-1 fs-16"></i></button>
                                </div>
                            </div>
                            
                            <p class="mb-0 fw-semibold position-relative text-center fs-12">Already have an account? <a
                                    href="<?php echo e(route('login')); ?>" class="text-decoration-underline text-primary">Sign In here</a>
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
<?php echo $__env->make('partials.layouts.master_auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/auth-signup.blade.php ENDPATH**/ ?>
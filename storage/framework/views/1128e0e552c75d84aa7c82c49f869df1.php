<?php $__env->startSection('title', 'Modifier mon profil | SmartHealth'); ?>
<?php $__env->startSection('title-sub', 'Profil'); ?>
<?php $__env->startSection('pagetitle', 'Modifier mon profil'); ?>

<?php $__env->startSection('content'); ?>
<div id="layout-wrapper">
    <div class="row">
        <div class="col-12">
            <!-- Debug des erreurs (temporaire) -->
            <?php if($errors->any()): ?>
                <div class="alert alert-danger mb-4">
                    <h5>Erreurs détectées :</h5>
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Onglets -->
            <div class="mb-4">
                <ul class="nav nav-pills" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-info-tab" data-bs-toggle="tab"
                                data-bs-target="#profile-info" type="button" role="tab">
                            <i class="ri-user-line me-1"></i>Informations personnelles
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-photo-tab" data-bs-toggle="tab"
                                data-bs-target="#profile-photo" type="button" role="tab">
                            <i class="ri-image-line me-1"></i>Photo de profil
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-password-tab" data-bs-toggle="tab"
                                data-bs-target="#profile-password" type="button" role="tab">
                            <i class="ri-lock-line me-1"></i>Mot de passe
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Contenu des onglets -->
            <div class="tab-content" id="profileTabsContent">
                <!-- Onglet Informations personnelles -->
                <div class="tab-pane fade show active" id="profile-info" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informations personnelles</h5>
                        </div>
                        <div class="card-body">
                            <!-- FORMULAIRE 1: Informations personnelles -->
                            <form action="<?php echo e(route('profile.update')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
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
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
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
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="phone" name="phone" value="<?php echo e(old('phone', $user->phone ?? '')); ?>">
                                        <?php $__errorArgs = ['phone'];
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
                                    <div class="col-md-6">
                                        <label for="birth_date" class="form-label">Date de naissance</label>
                                        <input type="date" class="form-control <?php $__errorArgs = ['birth_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="birth_date" name="birth_date" 
                                               value="<?php echo e(old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '')); ?>">
                                        <?php $__errorArgs = ['birth_date'];
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
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Genre</label>
                                        <select class="form-select <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="gender" name="gender">
                                            <option value="">Sélectionnez votre genre</option>
                                            <option value="male" <?php echo e(old('gender', $user->gender ?? '') == 'male' ? 'selected' : ''); ?>>Homme</option>
                                            <option value="female" <?php echo e(old('gender', $user->gender ?? '') == 'female' ? 'selected' : ''); ?>>Femme</option>
                                            <option value="other" <?php echo e(old('gender', $user->gender ?? '') == 'other' ? 'selected' : ''); ?>>Autre</option>
                                        </select>
                                        <?php $__errorArgs = ['gender'];
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
                                        <label for="address" class="form-label">Adresse</label>
                                        <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                  id="address" name="address" rows="2"><?php echo e(old('address', $user->address ?? '')); ?></textarea>
                                        <?php $__errorArgs = ['address'];
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
                                        <label for="bio" class="form-label">Biographie</label>
                                        <textarea class="form-control <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                  id="bio" name="bio" rows="3" 
                                                  placeholder="Parlez-nous un peu de vous..."><?php echo e(old('bio', $user->bio ?? '')); ?></textarea>
                                        <?php $__errorArgs = ['bio'];
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
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="<?php echo e(route('profile.show')); ?>" class="btn btn-secondary">Annuler</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Onglet Photo de profil -->
                <div class="tab-pane fade" id="profile-photo" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Photo de profil</h5>
                        </div>
                        <div class="card-body">
                            <!-- Affichage de l'avatar actuel -->
                            <div class="text-center mb-4">
                                <?php if($user->avatar): ?>
                                    <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Avatar actuel" 
                                         class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                         style="width: 150px; height: 150px;">
                                        <span class="text-white fs-48 fw-bold"><?php echo e(substr($user->name, 0, 2)); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- FORMULAIRE 2: Upload d'avatar -->
                            <form action="<?php echo e(route('profile.update-avatar')); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Nouvelle photo de profil</label>
                                    <input type="file" class="form-control <?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="avatar" name="avatar" accept="image/*">
                                    <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB</div>
                                    <?php $__errorArgs = ['avatar'];
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
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-upload-line me-1"></i>Mettre à jour la photo
                                    </button>
                                </div>
                            </form>

                            <!-- SECTION SÉPARÉE pour supprimer l'avatar -->
                            <?php if($user->avatar): ?>
                            <hr class="my-4">
                            <div class="text-center">
                                <p class="text-muted mb-3">Ou supprimer la photo actuelle :</p>
                                <form action="<?php echo e(route('profile.delete-avatar')); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')">
                                        <i class="ri-delete-bin-line me-1"></i>Supprimer la photo actuelle
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Onglet Mot de passe -->
                <div class="tab-pane fade" id="profile-password" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Changer le mot de passe</h5>
                        </div>
                        <div class="card-body">
                            <!-- FORMULAIRE 3: Mot de passe -->
                            <form action="<?php echo e(route('profile.update-password')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="current_password" name="current_password" required>
                                        <?php $__errorArgs = ['current_password'];
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
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="new_password" name="new_password" required>
                                        <?php $__errorArgs = ['new_password'];
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
                                    <div class="col-md-6">
                                        <label for="new_password_confirmation" class="form-label">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" 
                                               id="new_password_confirmation" name="new_password_confirmation" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="<?php echo e(route('profile.show')); ?>" class="btn btn-secondary">Annuler</a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="ri-lock-line me-1"></i>Changer le mot de passe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'alerte -->
    <?php if(session('success')): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-check-line me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus sur l'onglet avec erreur
    <?php if($errors->any()): ?>
        // Déterminer quel onglet a des erreurs
        <?php if($errors->has(['name', 'email', 'phone', 'address', 'birth_date', 'gender', 'bio'])): ?>
            // Erreurs dans l'onglet informations personnelles
            document.getElementById('profile-info-tab').click();
        <?php elseif($errors->has('avatar')): ?>
            // Erreurs dans l'onglet photo
            document.getElementById('profile-photo-tab').click();
        <?php elseif($errors->has(['current_password', 'new_password'])): ?>
            // Erreurs dans l'onglet mot de passe
            document.getElementById('profile-password-tab').click();
        <?php endif; ?>
    <?php endif; ?>
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/pages-profile-edit.blade.php ENDPATH**/ ?>
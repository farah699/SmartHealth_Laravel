<?php $__env->startSection('title', 'Recommandations IA | SmartHealth'); ?>
<?php $__env->startSection('title-sub', 'Activités Sportives'); ?>
<?php $__env->startSection('pagetitle', 'Recommandations IA'); ?>

<?php $__env->startSection('css'); ?>
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Animate.css pour les animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
.recommendation-card {
    transition: transform 0.2s;
    border: 1px solid #e3e6f0;
    border-radius: 15px;
    overflow: hidden;
}

.recommendation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.breadcrumb-item a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: white;
}

.avatar-lg {
    width: 60px;
    height: 60px;
}

.profile-card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.generation-card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-animation {
    animation: fadeInUp 0.6s ease-out;
}

.btn-ai {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-ai:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- En-tête avec breadcrumb -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 header-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="fas fa-robot text-white fs-3"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 text-white">🤖 Recommandations IA Personnalisées</h4>
                                    <p class="text-white-50 mb-0">Notre intelligence artificielle analyse votre profil pour vous proposer les meilleurs exercices</p>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0 mt-2">
                                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Tableau de bord</a></li>
                                            <li class="breadcrumb-item"><a href="<?php echo e(route('activities.index')); ?>">Activités</a></li>
                                            <li class="breadcrumb-item active">Recommandations IA</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('activities.index')); ?>" class="btn btn-light">
                                    <i class="fas fa-arrow-left me-1"></i>Retour aux activités
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profil utilisateur -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card profile-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-circle me-2"></i>👤 Votre Profil d'Entraînement
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-calendar text-primary me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">Âge</small>
                                        <div class="fw-semibold"><?php echo e($user->age ?? 28); ?> ans</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-weight text-success me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">IMC</small>
                                        <?php
                                            $imc = $user->imc ?? 26.1;
                                            $imcStatus = ($imc >= 18.5 && $imc <= 24.9) ? 'Normal' : (($imc >= 25 && $imc <= 29.9) ? 'Surpoids' : 'Obésité');
                                            $imcClass = ($imc >= 18.5 && $imc <= 24.9) ? 'text-success' : (($imc >= 25 && $imc <= 29.9) ? 'text-warning' : 'text-danger');
                                        ?>
                                        <div class="fw-semibold <?php echo e($imcClass); ?>"><?php echo e(number_format($imc, 1)); ?> (<?php echo e($imcStatus); ?>)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-trophy text-warning me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">Niveau</small>
                                        <?php
                                            $activityCount = $user->activities()->count();
                                            $level = $activityCount >= 30 ? 'Avancé' : ($activityCount >= 10 ? 'Intermédiaire' : 'Débutant');
                                        ?>
                                        <div class="fw-semibold"><?php echo e($level); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-chart-line text-info me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">Activités</small>
                                        <div class="fw-semibold"><?php echo e($user->activities()->count()); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton de génération -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card generation-card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-brain fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">🎯 Génération de Recommandations Personnalisées</h5>
                        <p class="card-text text-muted mb-4">
                            Notre IA analyse votre profil, votre niveau et vos préférences pour vous proposer les exercices les plus adaptés à vos objectifs
                        </p>
                        <button id="generateBtn" class="btn btn-ai btn-lg">
                            <i class="fas fa-robot me-2"></i>
                            <span id="btnText">Générer mes recommandations IA</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zone des recommandations -->
        <div id="recommendationsContainer" class="row" style="display: none;">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trophy me-2"></i>🏆 Vos Recommandations Personnalisées
                        </h5>
                        <span id="recommendationsCount" class="badge bg-light text-dark"></span>
                    </div>
                    <div class="card-body">
                        <div id="recommendationsList" class="row">
                            <!-- Les recommandations seront insérées ici -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommandations existantes -->
        <?php if(!empty($recommendations)): ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>🏆 Dernières Recommandations (<?php echo e(count($recommendations)); ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php $__currentLoopData = $recommendations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 recommendation-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title">
                                                <?php
                                                    $emoji = match($rec['exercise_type'] ?? 'other') {
                                                        'cardio' => '❤️',
                                                        'strength' => '💪',
                                                        'flexibility' => '🧘',
                                                        'balance' => '⚖️',
                                                        default => '🏃'
                                                    };
                                                ?>
                                                <?php echo e($emoji); ?> <?php echo e($rec['exercise_name'] ?? 'Exercice'); ?>

                                            </h6>
                                            <span class="badge bg-<?php echo e(($rec['predicted_score'] ?? 0) >= 80 ? 'success' : (($rec['predicted_score'] ?? 0) >= 60 ? 'warning' : 'secondary')); ?>">
                                                <?php echo e(number_format($rec['predicted_score'] ?? 0, 1)); ?>%
                                            </span>
                                        </div>
                                        
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Durée</small>
                                                <div class="fw-semibold"><?php echo e($rec['recommended_duration'] ?? 0); ?> min</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Difficulté</small>
                                                <div class="fw-semibold"><?php echo e($rec['difficulty_level'] ?? 0); ?>/10</div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">Type:</small>
                                            <span class="badge bg-light text-dark"><?php echo e(ucfirst($rec['exercise_type'] ?? 'autre')); ?></span>
                                        </div>
                                        
                                        <p class="card-text small text-muted">
                                            <?php echo e($rec['recommendation_reason'] ?? 'Recommandé par l\'IA'); ?>

                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                🔥 <?php echo e($rec['calories_per_minute'] ?? 0); ?> cal/min
                                            </small>
                                            <button class="btn btn-sm btn-outline-primary start-training-btn"
                                                data-exercise-id="<?php echo e($rec['exercise_id'] ?? $index); ?>"
                                                data-exercise-name="<?php echo e($rec['exercise_name'] ?? 'Exercice'); ?>"
                                                data-exercise-type="<?php echo e($rec['exercise_type'] ?? 'other'); ?>"
                                                data-duration="<?php echo e($rec['recommended_duration'] ?? 30); ?>"
                                                data-difficulty="<?php echo e($rec['difficulty_level'] ?? 5); ?>"
                                                data-calories="<?php echo e($rec['calories_per_minute'] ?? 5); ?>">
                                                <i class="fas fa-play me-1"></i>Commencer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generateBtn');
    const btnText = document.getElementById('btnText');
    const recommendationsContainer = document.getElementById('recommendationsContainer');
    const recommendationsList = document.getElementById('recommendationsList');
    const recommendationsCount = document.getElementById('recommendationsCount');
    
    // Bouton générer recommandations
    generateBtn.addEventListener('click', function() {
        generateRecommendations();
    });
    
    // Gérer les clics sur les boutons "Commencer"
    document.addEventListener('click', function(e) {
        if (e.target.closest('.start-training-btn')) {
            e.preventDefault();
            
            const button = e.target.closest('.start-training-btn');
            const exerciseData = {
                exercise_id: button.dataset.exerciseId,
                exercise_name: button.dataset.exerciseName,
                exercise_type: button.dataset.exerciseType,
                recommended_duration: button.dataset.duration,
                difficulty_level: button.dataset.difficulty,
                calories_per_minute: button.dataset.calories
            };
            
            console.log('Données exercice:', exerciseData);
            
            // Créer un formulaire pour envoyer les données
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route("training.start")); ?>';
            form.style.display = 'none';
            
            // Ajouter le token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Ajouter les données de l'exercice
            Object.keys(exerciseData).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = exerciseData[key];
                form.appendChild(input);
            });
            
            // Ajouter le formulaire au DOM et le soumettre
            document.body.appendChild(form);
            form.submit();
        }
    });
    
    function generateRecommendations() {
        generateBtn.disabled = true;
        btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>IA en cours d\'analyse...';
        
        recommendationsContainer.style.display = 'none';
        
        // Animation de chargement
        Swal.fire({
            title: '🤖 IA en cours...',
            text: 'Analyse de votre profil pour générer des recommandations personnalisées',
            icon: 'info',
            showConfirmButton: false,
            timer: 3000
        });
        
        fetch('<?php echo e(route("exercises.recommendations.generate")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRecommendations(data.recommendations);
                Swal.fire({
                    title: 'Succès !',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: data.error || 'Erreur lors de la génération',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                title: 'Erreur',
                text: 'Erreur de communication avec le serveur',
                icon: 'error'
            });
        })
        .finally(() => {
            generateBtn.disabled = false;
            btnText.innerHTML = '<i class="fas fa-robot me-2"></i>Générer de nouvelles recommandations';
        });
    }
    
    function displayRecommendations(recommendations) {
        recommendationsList.innerHTML = '';
        recommendationsCount.textContent = recommendations.length + ' nouvelles recommandations';
        
        recommendations.forEach((rec, index) => {
            const emoji = getExerciseEmoji(rec.exercise_type);
            const badgeClass = rec.predicted_score >= 80 ? 'success' : (rec.predicted_score >= 60 ? 'warning' : 'secondary');
            
            const cardHtml = `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 recommendation-card fade-in-animation" style="animation-delay: ${index * 0.1}s;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="card-title">${emoji} ${rec.exercise_name}</h6>
                                <span class="badge bg-${badgeClass}">${rec.predicted_score.toFixed(1)}%</span>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Durée</small>
                                    <div class="fw-semibold">${rec.recommended_duration} min</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Difficulté</small>
                                    <div class="fw-semibold">${rec.difficulty_level}/10</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">Type:</small>
                                <span class="badge bg-light text-dark">${rec.exercise_type.charAt(0).toUpperCase() + rec.exercise_type.slice(1)}</span>
                            </div>
                            
                            <p class="card-text small text-muted">
                                ${rec.recommendation_reason || 'Recommandé par l\'IA'}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">🔥 ${rec.calories_per_minute || 0} cal/min</small>
                                <button class="btn btn-sm btn-outline-primary start-training-btn" 
                                    data-exercise-id="${rec.exercise_id || 0}"
                                    data-exercise-name="${rec.exercise_name}"
                                    data-exercise-type="${rec.exercise_type}"
                                    data-duration="${rec.recommended_duration}"
                                    data-difficulty="${rec.difficulty_level}"
                                    data-calories="${rec.calories_per_minute || 0}">
                                    <i class="fas fa-play me-1"></i>Commencer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            recommendationsList.innerHTML += cardHtml;
        });
        
        recommendationsContainer.style.display = 'block';
        recommendationsContainer.scrollIntoView({ behavior: 'smooth' });
    }
    
    function getExerciseEmoji(type) {
        const emojis = {
            'cardio': '❤️',
            'strength': '💪',
            'flexibility': '🧘',
            'balance': '⚖️'
        };
        return emojis[type] || '🏃';
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/exercises/recommendations.blade.php ENDPATH**/ ?>
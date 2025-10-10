<?php $__env->startSection('title', 'Mes Activités | SmartHealth'); ?>
<?php $__env->startSection('title-sub', 'Activités Sportives'); ?>
<?php $__env->startSection('pagetitle', 'Mes Activités'); ?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/libs/flatpickr/flatpickr.min.css')); ?>">
<style>
    .activity-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .activity-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .activity-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
    }

    .stats-card {
        border-left: 4px solid;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div id="layout-wrapper">
    <div class="row">
        <div class="col-12">
            
            <div class="card border-0 bg-primary-subtle mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-primary">
                                <i class="bi bi-heart-pulse me-2"></i>Mes Activités Sportives
                            </h4>
                            <p class="text-muted mb-0">Suivez vos progrès et restez motivé ! 💪</p>
                            <?php if(isset($stats) && $stats['total_activities'] > 0): ?>
                                <small class="text-primary">
                                    <i class="bi bi-trophy me-1"></i><?php echo e($stats['total_activities']); ?> activités enregistrées
                                    | Cette semaine : <?php echo e($stats['this_week']); ?>

                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('activities.create')); ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-1"></i>Nouvelle Activité
                            </a>
                            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="bi bi-funnel me-1"></i>Filtrer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <?php if(isset($stats) && $stats['total_activities'] > 0): ?>
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0" style="border-left-color: #28a745 !important;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-success-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-trophy text-success fs-3"></i>
                                </div>
                                <div>
                                    <h3 class="text-success mb-1"><?php echo e($stats['total_activities']); ?></h3>
                                    <p class="text-muted mb-0 small">Activités totales</p>
                                    <small class="text-success">
                                        <i class="bi bi-calendar-week me-1"></i><?php echo e($stats['this_week']); ?> cette semaine
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0" style="border-left-color: #ffc107 !important;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-stopwatch text-warning fs-3"></i>
                                </div>
                                <div>
                                    <?php
                                        $totalHours = floor($stats['total_duration'] / 60);
                                        $totalMinutes = $stats['total_duration'] % 60;
                                    ?>
                                    <h3 class="text-warning mb-1"><?php echo e($totalHours); ?>h<?php echo e($totalMinutes > 0 ? sprintf('%02d', $totalMinutes) : ''); ?></h3>
                                    <p class="text-muted mb-0 small">Temps total d'activité</p>
                                    <small class="text-warning">
                                        <i class="bi bi-clock me-1"></i><?php echo e(round($stats['total_duration'] / max($stats['total_activities'], 1))); ?>min en moyenne
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0" style="border-left-color: #17a2b8 !important;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-info-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-geo text-info fs-3"></i>
                                </div>
                                <div>
                                    <h3 class="text-info mb-1"><?php echo e(number_format($stats['total_distance'], 1)); ?></h3>
                                    <p class="text-muted mb-0 small">Kilomètres parcourus</p>
                                    <?php if($stats['total_distance'] > 0): ?>
                                        <small class="text-info">
                                            <i class="bi bi-speedometer me-1"></i><?php echo e(number_format($stats['total_distance'] / max($stats['total_activities'], 1), 1)); ?>km en moyenne
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0" style="border-left-color: #dc3545 !important;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-fire text-danger fs-3"></i>
                                </div>
                                <div>
                                    <h3 class="text-danger mb-1"><?php echo e(number_format($stats['total_calories'])); ?></h3>
                                    <p class="text-muted mb-0 small">Calories brûlées</p>
                                    <small class="text-danger">
                                        <i class="bi bi-lightning me-1"></i><?php echo e(round($stats['total_calories'] / max($stats['total_activities'], 1))); ?>cal par séance
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if(request()->filled('type') || request()->filled('date_from') || request()->filled('date_to')): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="bi bi-funnel me-2"></i>
                    <strong>Filtre actif :</strong>
                    <?php if(request()->filled('type')): ?>
                        Type: <span class="badge bg-primary"><?php echo e(ucfirst(request('type'))); ?></span>
                    <?php endif; ?>
                    <?php if(request()->filled('date_from')): ?>
                        Du: <span class="badge bg-secondary"><?php echo e(request('date_from')); ?></span>
                    <?php endif; ?>
                    <?php if(request()->filled('date_to')): ?>
                        Au: <span class="badge bg-secondary"><?php echo e(request('date_to')); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo e(route('activities.index')); ?>" class="btn btn-sm btn-outline-info ms-2">
                        <i class="bi bi-x-circle me-1"></i>Effacer les filtres
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <div class="row">
                <?php if(isset($activities) && $activities->count() > 0): ?>
                    <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $activityTypes = App\Models\Activity::getActivityTypes();
                            $type = $activityTypes[$activity->type] ?? null;
                            $intensityLevels = App\Models\Activity::getIntensityLevels();
                            $intensity = $intensityLevels[$activity->intensity] ?? null;
                        ?>
                        
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card activity-card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <?php if($type): ?>
                                                <div class="avatar-sm bg-<?php echo e($type['color']); ?>-subtle rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="<?php echo e($type['icon']); ?> text-<?php echo e($type['color']); ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?php echo e($activity->name); ?></h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i><?php echo e($activity->activity_date->format('d/m/Y')); ?>

                                                    <?php if($activity->start_time): ?>
                                                        à <?php echo e($activity->start_time->format('H:i')); ?>

                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="<?php echo e(route('activities.show', $activity)); ?>">
                                                    <i class="bi bi-eye me-2"></i>Voir détails
                                                </a></li>
                                                <li><a class="dropdown-item" href="<?php echo e(route('activities.edit', $activity)); ?>">
                                                    <i class="bi bi-pencil me-2"></i>Modifier
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="<?php echo e(route('activities.destroy', $activity)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Supprimer cette activité ?')">
                                                            <i class="bi bi-trash me-2"></i>Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    
                                    <div class="d-flex gap-1 mb-3 flex-wrap">
                                        <?php if($type): ?>
                                            <span class="badge bg-<?php echo e($type['color']); ?>-subtle text-<?php echo e($type['color']); ?> activity-type-badge">
                                                <i class="<?php echo e($type['icon']); ?> me-1"></i><?php echo e($type['name']); ?>

                                            </span>
                                        <?php endif; ?>
                                        <?php if($intensity): ?>
                                            <span class="badge bg-<?php echo e($intensity['color']); ?>-subtle text-<?php echo e($intensity['color']); ?> activity-type-badge">
                                                <i class="bi bi-speedometer me-1"></i><?php echo e($intensity['name']); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <i class="bi bi-stopwatch text-primary fs-5"></i>
                                                <div class="fw-bold text-primary"><?php echo e($activity->formatted_duration); ?></div>
                                                <small class="text-muted">Durée</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <i class="bi bi-fire text-danger fs-5"></i>
                                                <div class="fw-bold text-danger"><?php echo e($activity->calories ?? 0); ?></div>
                                                <small class="text-muted">Calories</small>
                                            </div>
                                        </div>
                                        <?php if($activity->distance): ?>
                                            <div class="col-6">
                                                <div class="text-center p-2 bg-light rounded">
                                                    <i class="bi bi-geo text-info fs-5"></i>
                                                    <div class="fw-bold text-info"><?php echo e($activity->distance); ?> km</div>
                                                    <small class="text-muted">Distance</small>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($activity->average_speed): ?>
                                            <div class="col-6">
                                                <div class="text-center p-2 bg-light rounded">
                                                    <i class="bi bi-speedometer2 text-success fs-5"></i>
                                                    <div class="fw-bold text-success"><?php echo e($activity->average_speed); ?> km/h</div>
                                                    <small class="text-muted">Vitesse moy.</small>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <?php if($activity->additional_data): ?>
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">Informations supplémentaires :</small>
                                            <?php if(isset($activity->additional_data['heart_rate'])): ?>
                                                <span class="badge bg-danger-subtle text-danger me-1">
                                                    <i class="bi bi-heart me-1"></i><?php echo e($activity->additional_data['heart_rate']); ?> bpm
                                                </span>
                                            <?php endif; ?>
                                            <?php if(isset($activity->additional_data['weather'])): ?>
                                                <span class="badge bg-info-subtle text-info">
                                                    <i class="bi bi-cloud me-1"></i><?php echo e(ucfirst($activity->additional_data['weather'])); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    
                                    <?php if($activity->description): ?>
                                        <div class="border-top pt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-chat-left-text me-1"></i>
                                                <?php echo e(Str::limit($activity->description, 100)); ?>

                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-footer bg-transparent border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Ajouté <?php echo e($activity->created_at->diffForHumans()); ?>

                                        </small>
                                        <a href="<?php echo e(route('activities.show', $activity)); ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>Détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php if(method_exists($activities, 'links')): ?>
                        <div class="col-12">
                            <div class="d-flex justify-content-center mt-4">
                                <?php echo e($activities->appends(request()->query())->links()); ?>

                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-heart-pulse text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                                </div>
                                <h4 class="text-muted mb-3">Aucune activité trouvée</h4>
                                <p class="text-muted mb-4">
                                    <?php if(request()->hasAny(['type', 'date_from', 'date_to'])): ?>
                                        Aucune activité ne correspond à vos critères de recherche.
                                        <br>Essayez de modifier vos filtres ou
                                    <?php else: ?>
                                        Vous n'avez pas encore enregistré d'activité sportive.
                                        <br>Commencez dès maintenant et
                                    <?php endif; ?>
                                    suivez vos progrès !
                                </p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="<?php echo e(route('activities.create')); ?>" class="btn btn-primary btn-lg">
                                        <i class="bi bi-plus-circle me-2"></i>Ajouter ma première activité
                                    </a>
                                    <?php if(request()->hasAny(['type', 'date_from', 'date_to'])): ?>
                                        <a href="<?php echo e(route('activities.index')); ?>" class="btn btn-outline-secondary btn-lg">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Voir toutes les activités
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-funnel me-2"></i>Filtrer les activités
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('activities.index')); ?>" method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type d'activité</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tous les types</option>
                                <?php $__currentLoopData = App\Models\Activity::getActivityTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>>
                                        <?php echo e($type['name']); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_from" class="form-label">Date de début</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo e(request('date_from')); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_to" class="form-label">Date de fin</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo e(request('date_to')); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <a href="<?php echo e(route('activities.index')); ?>" class="btn btn-outline-danger">Effacer</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('assets/libs/flatpickr/flatpickr.min.js')); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-info)');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('show')) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 300);
            }
        });
    }, 5000);
    
    // Animation pour les cartes d'activité
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observer toutes les cartes d'activité
    document.querySelectorAll('.activity-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/activities/index.blade.php ENDPATH**/ ?>
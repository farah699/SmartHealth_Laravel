@extends('partials.layouts.master')

@section('title', 'Détails de l\'activité | SmartHealth')
@section('title-sub', 'Activités Sportives')
@section('pagetitle', 'Détails de l\'activité')

@section('css')
<style>
    .stat-card {
        transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .session-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .session-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .progress-ring {
        width: 60px;
        height: 60px;
    }
</style>
@endsection

@section('content')
<div id="layout-wrapper">
    <div class="row">
        <div class="col-12">
            {{-- En-tête de l'activité --}}
            <div class="card border-0 bg-primary-subtle mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @php
                                $activityTypes = App\Models\Activity::getActivityTypes();
                                $type = $activityTypes[$activity->type] ?? null;
                            @endphp
                            @if($type)
                                <div class="avatar-lg bg-{{ $type['color'] }} rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="{{ $type['icon'] }} text-white fs-3"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="mb-1 text-primary">{{ $activity->name }}</h4>
                                <p class="text-muted mb-0">
                                    @if($activity->is_recurring)
                                        <i class="bi bi-arrow-repeat me-1"></i>Activité récurrente
                                        @if($activity->target_sessions_per_week)
                                            - Objectif: {{ $activity->target_sessions_per_week }} sessions/semaine
                                        @endif
                                    @else
                                        <i class="bi bi-calendar3 me-1"></i>{{ $activity->activity_date->format('d/m/Y') }}
                                        @if($activity->start_time)
                                            à {{ $activity->start_time->format('H:i') }}
                                        @endif
                                    @endif
                                </p>
                                @if($activity->activity_description)
                                    <small class="text-primary">{{ $activity->activity_description }}</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            @if($activity->is_recurring)
                                <a href="{{ route('activity-sessions.create', $activity) }}" class="btn btn-success btn-lg">
                                    <i class="bi bi-plus-circle me-1"></i>Ajouter une session
                                </a>
                            @else
                                {{-- Bouton pour convertir en activité récurrente --}}
                                <button type="button" class="btn btn-info btn-lg" data-bs-toggle="modal" data-bs-target="#convertModal">
                                    <i class="bi bi-arrow-repeat me-1"></i>Rendre récurrente
                                </button>
                            @endif
                            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-warning btn-lg">
                                <i class="bi bi-pencil-square me-1"></i>Modifier
                            </a>
                            <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($activity->is_recurring)
                {{-- Statistiques générales pour activité récurrente --}}
                @php $stats = $activity->stats; @endphp
                @if($stats['total_sessions'] > 0)
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card border-0 bg-success-subtle h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-trophy text-success fs-2 mb-2"></i>
                                <h3 class="text-success mb-1">{{ $stats['total_sessions'] }}</h3>
                                <p class="text-muted mb-0 small">Sessions totales</p>
                                <small class="text-success">
                                    Cette semaine: {{ $stats['this_week_sessions'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-0 bg-primary-subtle h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-stopwatch text-primary fs-2 mb-2"></i>
                                @php
                                    $totalHours = floor($stats['total_duration'] / 60);
                                    $totalMinutes = $stats['total_duration'] % 60;
                                @endphp
                                <h3 class="text-primary mb-1">{{ $totalHours }}h{{ $totalMinutes > 0 ? sprintf('%02d', $totalMinutes) : '' }}</h3>
                                <p class="text-muted mb-0 small">Temps total</p>
                                <small class="text-primary">
                                    Moyenne: {{ round($stats['average_duration']) }}min
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-0 bg-info-subtle h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-geo text-info fs-2 mb-2"></i>
                                <h3 class="text-info mb-1">{{ number_format($stats['total_distance'], 1) }}</h3>
                                <p class="text-muted mb-0 small">Km parcourus</p>
                                @if($stats['average_distance'] > 0)
                                    <small class="text-info">
                                        Moyenne: {{ number_format($stats['average_distance'], 1) }}km
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-0 bg-danger-subtle h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-fire text-danger fs-2 mb-2"></i>
                                <h3 class="text-danger mb-1">{{ number_format($stats['total_calories']) }}</h3>
                                <p class="text-muted mb-0 small">Calories brûlées</p>
                                <small class="text-danger">
                                    Moyenne: {{ round($stats['total_calories'] / max($stats['total_sessions'], 1)) }}cal
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Progression vers l'objectif --}}
                <div class="card mb-4 border-0 bg-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2">
                                    <i class="bi bi-target text-success me-2"></i>Progression cette semaine
                                </h5>
                                @php
                                    $weeklyProgress = ($stats['this_week_sessions'] / max($activity->target_sessions_per_week, 1)) * 100;
                                    $weeklyProgress = min($weeklyProgress, 100);
                                @endphp
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                         style="width: {{ $weeklyProgress }}%">
                                        {{ round($weeklyProgress) }}%
                                    </div>
                                </div>
                                <p class="mb-0 text-muted">
                                    {{ $stats['this_week_sessions'] }} sessions sur {{ $activity->target_sessions_per_week }} objectif
                                    @if($weeklyProgress >= 100)
                                        <span class="badge bg-success ms-2">🎯 Objectif atteint !</span>
                                    @elseif($weeklyProgress >= 75)
                                        <span class="badge bg-warning ms-2">Presque !</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                @if($stats['last_session_date'])
                                    <small class="text-muted">Dernière session</small>
                                    <h6 class="text-success mb-0">{{ \Carbon\Carbon::parse($stats['last_session_date'])->format('d/m/Y') }}</h6>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($stats['last_session_date'])->diffForHumans() }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Liste des sessions --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-list-ul me-2"></i>Historique des sessions
                            </h5>
                            @if($activity->sessions->count() > 0)
                                <span class="badge bg-primary-subtle text-primary fs-6">
                                    {{ $activity->sessions->count() }} sessions
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($activity->sessions->count() > 0)
                            <div class="row">
                                @foreach($activity->sessions as $session)
                                <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                                    <div class="card session-card border h-100">
                                        <div class="card-header bg-light border-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $session->session_date->format('d/m/Y') }}</h6>
                                                    @if($session->start_time)
                                                        <small class="text-muted">{{ $session->start_time->format('H:i') }}</small>
                                                    @endif
                                                </div>
                                                @if($session->rating)
                                                    <div class="text-warning">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $session->rating)⭐@endif
                                                        @endfor
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2 text-center mb-3">
                                                <div class="col-6">
                                                    <div class="p-2 bg-primary-subtle rounded">
                                                        <i class="bi bi-stopwatch text-primary"></i>
                                                        <div class="fw-bold text-primary">{{ $session->formatted_duration }}</div>
                                                        <small class="text-muted">Durée</small>
                                                    </div>
                                                </div>
                                                @if($session->distance)
                                                <div class="col-6">
                                                    <div class="p-2 bg-info-subtle rounded">
                                                        <i class="bi bi-geo text-info"></i>
                                                        <div class="fw-bold text-info">{{ $session->distance }} km</div>
                                                        <small class="text-muted">Distance</small>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($session->calories)
                                                <div class="col-6">
                                                    <div class="p-2 bg-danger-subtle rounded">
                                                        <i class="bi bi-fire text-danger"></i>
                                                        <div class="fw-bold text-danger">{{ $session->calories }}</div>
                                                        <small class="text-muted">Calories</small>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($session->average_speed)
                                                <div class="col-6">
                                                    <div class="p-2 bg-success-subtle rounded">
                                                        <i class="bi bi-speedometer2 text-success"></i>
                                                        <div class="fw-bold text-success">{{ $session->average_speed }} km/h</div>
                                                        <small class="text-muted">Vitesse</small>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>

                                            {{-- Badges d'intensité et difficulté --}}
                                            <div class="d-flex gap-1 mb-2 justify-content-center flex-wrap">
                                                @php
                                                    $intensityLevels = App\Models\Activity::getIntensityLevels();
                                                    $intensity = $intensityLevels[$session->intensity] ?? null;
                                                @endphp
                                                @if($intensity)
                                                    <span class="badge bg-{{ $intensity['color'] }}-subtle text-{{ $intensity['color'] }}">
                                                        {{ $intensity['name'] }}
                                                    </span>
                                                @endif
                                                
                                                @if($session->difficulty)
                                                    @php
                                                        $difficultyLevels = App\Models\ActivitySession::getDifficultyLevels();
                                                        $difficulty = $difficultyLevels[$session->difficulty] ?? null;
                                                    @endphp
                                                    @if($difficulty)
                                                        <span class="badge bg-{{ $difficulty['color'] }}-subtle text-{{ $difficulty['color'] }}">
                                                            <i class="{{ $difficulty['icon'] }} me-1"></i>{{ $difficulty['name'] }}
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>

                                            @if($session->session_notes)
                                                <div class="border-top pt-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-chat-left-text me-1"></i>
                                                        {{ Str::limit($session->session_notes, 80) }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent border-0 text-center">
                                            <small class="text-muted">
                                                {{ $session->session_date->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                <h5 class="text-muted mt-3">Aucune session enregistrée</h5>
                                <p class="text-muted mb-4">Commencez par ajouter votre première session pour cette activité récurrente !</p>
                                <a href="{{ route('activity-sessions.create', $activity) }}" class="btn btn-success btn-lg">
                                    <i class="bi bi-plus-circle me-1"></i>Ajouter la première session
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Activité simple - afficher les détails normaux --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Détails de la session
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                <div class="p-3 bg-primary-subtle rounded">
                                    <i class="bi bi-stopwatch text-primary fs-2 mb-2"></i>
                                    <h4 class="text-primary mb-1">{{ $activity->formatted_duration }}</h4>
                                    <p class="text-muted mb-0">Durée</p>
                                </div>
                            </div>
                            @if($activity->distance)
                            <div class="col-md-3 text-center mb-3">
                                <div class="p-3 bg-info-subtle rounded">
                                    <i class="bi bi-geo text-info fs-2 mb-2"></i>
                                    <h4 class="text-info mb-1">{{ $activity->distance }} km</h4>
                                    <p class="text-muted mb-0">Distance</p>
                                </div>
                            </div>
                            @endif
                            @if($activity->calories)
                            <div class="col-md-3 text-center mb-3">
                                <div class="p-3 bg-danger-subtle rounded">
                                    <i class="bi bi-fire text-danger fs-2 mb-2"></i>
                                    <h4 class="text-danger mb-1">{{ $activity->calories }}</h4>
                                    <p class="text-muted mb-0">Calories</p>
                                </div>
                            </div>
                            @endif
                            @if($activity->average_speed)
                            <div class="col-md-3 text-center mb-3">
                                <div class="p-3 bg-success-subtle rounded">
                                    <i class="bi bi-speedometer2 text-success fs-2 mb-2"></i>
                                    <h4 class="text-success mb-1">{{ $activity->average_speed }} km/h</h4>
                                    <p class="text-muted mb-0">Vitesse moyenne</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        @if($activity->additional_data)
                            <div class="border-top pt-3 mt-3">
                                <h6 class="mb-2">Données supplémentaires :</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if(isset($activity->additional_data['heart_rate']))
                                        <span class="badge bg-danger-subtle text-danger">
                                            <i class="bi bi-heart me-1"></i>{{ $activity->additional_data['heart_rate'] }} bpm
                                        </span>
                                    @endif
                                    @if(isset($activity->additional_data['weather']))
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="bi bi-cloud me-1"></i>{{ ucfirst($activity->additional_data['weather']) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($activity->description)
                            <div class="border-top pt-3 mt-3">
                                <h6 class="mb-2">Description :</h6>
                                <p class="text-muted">{{ $activity->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Suggestion pour rendre récurrente --}}
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-lightbulb fs-4 me-3 text-info"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Conseil :</h6>
                            <p class="mb-0">Si vous pratiquez régulièrement cette activité, vous pouvez la convertir en activité récurrente pour suivre vos sessions quotidiennes.</p>
                        </div>
                        <button type="button" class="btn btn-info ms-3" data-bs-toggle="modal" data-bs-target="#convertModal">
                            <i class="bi bi-arrow-repeat me-1"></i>Rendre récurrente
                        </button>
                    </div>
                </div>
            @endif

            {{-- Informations générales --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>Créé le {{ $activity->created_at->format('d/m/Y à H:i') }}
                                @if($activity->created_at != $activity->updated_at)
                                    <br><i class="bi bi-pencil me-1"></i>Modifié {{ $activity->updated_at->diffForHumans() }}
                                @endif
                            </small>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square me-1"></i>Modifier
                            </a>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash me-1"></i>Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal pour convertir en activité récurrente --}}
    @if(!$activity->is_recurring)
    <div class="modal fade" id="convertModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-info">
                        <i class="bi bi-arrow-repeat me-2"></i>Convertir en activité récurrente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('activities.convert-recurring', $activity) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            En convertissant cette activité en récurrente, vous pourrez :
                            <ul class="mb-0 mt-2">
                                <li>Ajouter des sessions quotidiennes</li>
                                <li>Suivre vos statistiques globales</li>
                                <li>Définir des objectifs hebdomadaires</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <label for="target_sessions_per_week" class="form-label">Objectif sessions par semaine *</label>
                            <select class="form-select" id="target_sessions_per_week" name="target_sessions_per_week" required>
                                <option value="">Choisir...</option>
                                <option value="1">1 session/semaine</option>
                                <option value="2">2 sessions/semaine</option>
                                <option value="3">3 sessions/semaine</option>
                                <option value="4">4 sessions/semaine</option>
                                <option value="5">5 sessions/semaine</option>
                                <option value="6">6 sessions/semaine</option>
                                <option value="7">Tous les jours</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="activity_description" class="form-label">Description générale</label>
                            <input type="text" class="form-control" id="activity_description" name="activity_description" 
                                   placeholder="Ex: Course matinale quotidienne" value="{{ $activity->name }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-arrow-repeat me-1"></i>Convertir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal de suppression --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirmer la suppression
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                    <p class="mt-3">
                        Êtes-vous sûr de vouloir supprimer définitivement<br>
                        <strong>"{{ $activity->name }}"</strong>
                        @if($activity->is_recurring && $activity->sessions->count() > 0)
                            <br>et toutes ses <strong>{{ $activity->sessions->count() }} sessions</strong> ?
                        @endif
                    </p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Attention :</strong> Cette action est irréversible !
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="{{ route('activities.destroy', $activity) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'apparition des cartes
    const cards = document.querySelectorAll('.stat-card, .session-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Clic sur les cartes de session pour plus de détails
    document.querySelectorAll('.session-card').forEach(card => {
        card.addEventListener('click', function() {
            // Animation de clic
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'translateY(-3px)';
            }, 100);
        });
    });
});
</script>
@endsection
@extends('partials.layouts.master')

@section('title', 'Mes Activités | SmartHealth')
@section('title-sub', 'Activités Sportives')
@section('pagetitle', 'Mes Activités')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
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
@endsection

@section('content')
<div id="layout-wrapper">
    <div class="row">
        <div class="col-12">
            {{-- En-tête avec bouton d'ajout --}}
            <div class="card border-0 bg-primary-subtle mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-primary">
                                <i class="bi bi-heart-pulse me-2"></i>Mes Activités Sportives
                            </h4>
                            <p class="text-muted mb-0">Suivez vos progrès et restez motivé ! 💪</p>
                            @if(isset($stats) && $stats['total_activities'] > 0)
                                <small class="text-primary">
                                    <i class="bi bi-trophy me-1"></i>{{ $stats['total_activities'] }} activités enregistrées
                                    | Cette semaine : {{ $stats['this_week'] }}
                                </small>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('activities.create') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-1"></i>Nouvelle Activité
                            </a>
                            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="bi bi-funnel me-1"></i>Filtrer
                            </button>
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

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Statistiques rapides --}}
            @if(isset($stats) && $stats['total_activities'] > 0)
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stats-card border-0" style="border-left-color: #28a745 !important;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-success-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="bi bi-trophy text-success fs-3"></i>
                                </div>
                                <div>
                                    <h3 class="text-success mb-1">{{ $stats['total_activities'] }}</h3>
                                    <p class="text-muted mb-0 small">Activités totales</p>
                                    <small class="text-success">
                                        <i class="bi bi-calendar-week me-1"></i>{{ $stats['this_week'] }} cette semaine
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
                                    @php
                                        $totalHours = floor($stats['total_duration'] / 60);
                                        $totalMinutes = $stats['total_duration'] % 60;
                                    @endphp
                                    <h3 class="text-warning mb-1">{{ $totalHours }}h{{ $totalMinutes > 0 ? sprintf('%02d', $totalMinutes) : '' }}</h3>
                                    <p class="text-muted mb-0 small">Temps total d'activité</p>
                                    <small class="text-warning">
                                        <i class="bi bi-clock me-1"></i>{{ round($stats['total_duration'] / max($stats['total_activities'], 1)) }}min en moyenne
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
                                    <h3 class="text-info mb-1">{{ number_format($stats['total_distance'], 1) }}</h3>
                                    <p class="text-muted mb-0 small">Kilomètres parcourus</p>
                                    @if($stats['total_distance'] > 0)
                                        <small class="text-info">
                                            <i class="bi bi-speedometer me-1"></i>{{ number_format($stats['total_distance'] / max($stats['total_activities'], 1), 1) }}km en moyenne
                                        </small>
                                    @endif
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
                                    <h3 class="text-danger mb-1">{{ number_format($stats['total_calories']) }}</h3>
                                    <p class="text-muted mb-0 small">Calories brûlées</p>
                                    <small class="text-danger">
                                        <i class="bi bi-lightning me-1"></i>{{ round($stats['total_calories'] / max($stats['total_activities'], 1)) }}cal par séance
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Filtre actuel --}}
            @if(request()->filled('type') || request()->filled('date_from') || request()->filled('date_to'))
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="bi bi-funnel me-2"></i>
                    <strong>Filtre actif :</strong>
                    @if(request()->filled('type'))
                        Type: <span class="badge bg-primary">{{ ucfirst(request('type')) }}</span>
                    @endif
                    @if(request()->filled('date_from'))
                        Du: <span class="badge bg-secondary">{{ request('date_from') }}</span>
                    @endif
                    @if(request()->filled('date_to'))
                        Au: <span class="badge bg-secondary">{{ request('date_to') }}</span>
                    @endif
                    <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-info ms-2">
                        <i class="bi bi-x-circle me-1"></i>Effacer les filtres
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Liste des activités --}}
            <div class="row">
                @if(isset($activities) && $activities->count() > 0)
                    @foreach($activities as $activity)
                        @php
                            $activityTypes = App\Models\Activity::getActivityTypes();
                            $type = $activityTypes[$activity->type] ?? null;
                            $intensityLevels = App\Models\Activity::getIntensityLevels();
                            $intensity = $intensityLevels[$activity->intensity] ?? null;
                        @endphp
                        
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card activity-card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @if($type)
                                                <div class="avatar-sm bg-{{ $type['color'] }}-subtle rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="{{ $type['icon'] }} text-{{ $type['color'] }}"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $activity->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>{{ $activity->activity_date->format('d/m/Y') }}
                                                    @if($activity->start_time)
                                                        à {{ $activity->start_time->format('H:i') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('activities.show', $activity) }}">
                                                    <i class="bi bi-eye me-2"></i>Voir détails
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('activities.edit', $activity) }}">
                                                    <i class="bi bi-pencil me-2"></i>Modifier
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
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
                                    {{-- Badges --}}
                                    <div class="d-flex gap-1 mb-3 flex-wrap">
                                        @if($type)
                                            <span class="badge bg-{{ $type['color'] }}-subtle text-{{ $type['color'] }} activity-type-badge">
                                                <i class="{{ $type['icon'] }} me-1"></i>{{ $type['name'] }}
                                            </span>
                                        @endif
                                        @if($intensity)
                                            <span class="badge bg-{{ $intensity['color'] }}-subtle text-{{ $intensity['color'] }} activity-type-badge">
                                                <i class="bi bi-speedometer me-1"></i>{{ $intensity['name'] }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Métriques principales --}}
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <i class="bi bi-stopwatch text-primary fs-5"></i>
                                                <div class="fw-bold text-primary">{{ $activity->formatted_duration }}</div>
                                                <small class="text-muted">Durée</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <i class="bi bi-fire text-danger fs-5"></i>
                                                <div class="fw-bold text-danger">{{ $activity->calories ?? 0 }}</div>
                                                <small class="text-muted">Calories</small>
                                            </div>
                                        </div>
                                        @if($activity->distance)
                                            <div class="col-6">
                                                <div class="text-center p-2 bg-light rounded">
                                                    <i class="bi bi-geo text-info fs-5"></i>
                                                    <div class="fw-bold text-info">{{ $activity->distance }} km</div>
                                                    <small class="text-muted">Distance</small>
                                                </div>
                                            </div>
                                        @endif
                                        @if($activity->average_speed)
                                            <div class="col-6">
                                                <div class="text-center p-2 bg-light rounded">
                                                    <i class="bi bi-speedometer2 text-success fs-5"></i>
                                                    <div class="fw-bold text-success">{{ $activity->average_speed }} km/h</div>
                                                    <small class="text-muted">Vitesse moy.</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Données supplémentaires --}}
                                    @if($activity->additional_data)
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">Informations supplémentaires :</small>
                                            @if(isset($activity->additional_data['heart_rate']))
                                                <span class="badge bg-danger-subtle text-danger me-1">
                                                    <i class="bi bi-heart me-1"></i>{{ $activity->additional_data['heart_rate'] }} bpm
                                                </span>
                                            @endif
                                            @if(isset($activity->additional_data['weather']))
                                                <span class="badge bg-info-subtle text-info">
                                                    <i class="bi bi-cloud me-1"></i>{{ ucfirst($activity->additional_data['weather']) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Description --}}
                                    @if($activity->description)
                                        <div class="border-top pt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-chat-left-text me-1"></i>
                                                {{ Str::limit($activity->description, 100) }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-footer bg-transparent border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Ajouté {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>Détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Pagination --}}
                    @if(method_exists($activities, 'links'))
                        <div class="col-12">
                            <div class="d-flex justify-content-center mt-4">
                                {{ $activities->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif

                @else
                    {{-- Aucune activité --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-heart-pulse text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                                </div>
                                <h4 class="text-muted mb-3">Aucune activité trouvée</h4>
                                <p class="text-muted mb-4">
                                    @if(request()->hasAny(['type', 'date_from', 'date_to']))
                                        Aucune activité ne correspond à vos critères de recherche.
                                        <br>Essayez de modifier vos filtres ou
                                    @else
                                        Vous n'avez pas encore enregistré d'activité sportive.
                                        <br>Commencez dès maintenant et
                                    @endif
                                    suivez vos progrès !
                                </p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('activities.create') }}" class="btn btn-primary btn-lg">
                                        <i class="bi bi-plus-circle me-2"></i>Ajouter ma première activité
                                    </a>
                                    @if(request()->hasAny(['type', 'date_from', 'date_to']))
                                        <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-lg">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Voir toutes les activités
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de filtre --}}
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-funnel me-2"></i>Filtrer les activités
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('activities.index') }}" method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type d'activité</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tous les types</option>
                                @foreach(App\Models\Activity::getActivityTypes() as $key => $type)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                        {{ $type['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_from" class="form-label">Date de début</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_to" class="form-label">Date de fin</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <a href="{{ route('activities.index') }}" class="btn btn-outline-danger">Effacer</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>

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
@endsection
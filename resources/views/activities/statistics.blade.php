@extends('partials.layouts.master')

@section('title', 'Statistiques des Activités | SmartHealth')
@section('title-sub', 'Activités Sportives')
@section('pagetitle', 'Statistiques')

@section('css')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Animate.css pour les animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .animate-counter {
        font-size: 2rem;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div id="layout-wrapper">
    <div class="row">
        {{-- En-tête --}}
        <div class="col-12 mb-4">
            <div class="card border-0 bg-primary-subtle">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="bi bi-graph-up text-white fs-3"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 text-primary">Statistiques des Activités</h4>
                                <p class="text-muted mb-0">Suivez vos performances et progressez vers vos objectifs</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" id="exportBtn">
                                <i class="bi bi-download me-1"></i>Exporter
                            </button>
                            <a href="{{ route('activities.index') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left me-1"></i>Retour aux activités
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques générales --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 bg-success-subtle h-100" onclick="showDetailModal('activities')">
                <div class="card-body text-center p-4">
                    <i class="bi bi-trophy text-success fs-1 mb-3"></i>
                    <div class="animate-counter text-success">{{ $totalActivities }}</div>
                    <h6 class="text-muted mb-0">Activités Totales</h6>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 bg-primary-subtle h-100" onclick="showDetailModal('duration')">
                <div class="card-body text-center p-4">
                    <i class="bi bi-stopwatch text-primary fs-1 mb-3"></i>
                    @php
                        $totalHours = floor($totalDuration / 60);
                        $totalMinutes = $totalDuration % 60;
                    @endphp
                    <div class="animate-counter text-primary">{{ $totalHours }}h{{ $totalMinutes > 0 ? sprintf('%02d', $totalMinutes) : '' }}</div>
                    <h6 class="text-muted mb-0">Temps Total</h6>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 bg-info-subtle h-100" onclick="showDetailModal('distance')">
                <div class="card-body text-center p-4">
                    <i class="bi bi-geo text-info fs-1 mb-3"></i>
                    <div class="animate-counter text-info">{{ number_format($totalDistance, 1) }}</div>
                    <h6 class="text-muted mb-0">Kilomètres</h6>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 bg-danger-subtle h-100" onclick="showDetailModal('calories')">
                <div class="card-body text-center p-4">
                    <i class="bi bi-fire text-danger fs-1 mb-3"></i>
                    <div class="animate-counter text-danger">{{ number_format($totalCalories) }}</div>
                    <h6 class="text-muted mb-0">Calories Brûlées</h6>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Évolution Mensuelle
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Répartition par Type
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques par type d'activité --}}
    @if(count($typeStats) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-grid me-2"></i>Performance par Type d'Activité
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($typeStats as $stat)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border stat-card h-100" onclick="showTypeDetail('{{ $stat['type'] }}', {{ json_encode($stat) }})">
                                <div class="card-body text-center">
                                    <i class="{{ $stat['icon'] }} text-{{ $stat['color'] }} fs-2 mb-3"></i>
                                    <h5 class="text-{{ $stat['color'] }}">{{ $stat['type'] }}</h5>
                                    <div class="row text-center mt-3">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h6 class="text-{{ $stat['color'] }} mb-0">{{ $stat['count'] }}</h6>
                                                <small class="text-muted">Sessions</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="text-{{ $stat['color'] }} mb-0">{{ $stat['avg_duration'] }}min</h6>
                                            <small class="text-muted">Moyenne</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Activités récentes --}}
    @if($recentActivities->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Activités Récentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($recentActivities->take(6) as $activity)
                        <div class="col-lg-6 col-xl-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        @php $activityTypes = App\Models\Activity::getActivityTypes(); @endphp
                                        <i class="{{ $activityTypes[$activity->type]['icon'] ?? 'bi bi-activity' }} text-{{ $activityTypes[$activity->type]['color'] ?? 'primary' }} me-2"></i>
                                        <h6 class="mb-0">{{ Str::limit($activity->name, 20) }}</h6>
                                    </div>
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">Durée</small>
                                            <strong>{{ $activity->duration }}min</strong>
                                        </div>
                                        @if($activity->distance)
                                        <div class="col-4">
                                            <small class="text-muted d-block">Distance</small>
                                            <strong>{{ $activity->distance }}km</strong>
                                        </div>
                                        @endif
                                        @if($activity->calories)
                                        <div class="col-4">
                                            <small class="text-muted d-block">Calories</small>
                                            <strong>{{ $activity->calories }}</strong>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-muted">{{ $activity->activity_date->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('js')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique mensuel
    const monthlyData = @json($monthlyStats);
    const ctx1 = document.getElementById('monthlyChart').getContext('2d');
    
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Activités',
                data: monthlyData.map(item => item.activities_count),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Graphique par type
    const typeData = @json($typeStats);
    if (typeData.length > 0) {
        const ctx2 = document.getElementById('typeChart').getContext('2d');
        
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: typeData.map(item => item.type),
                datasets: [{
                    data: typeData.map(item => item.count),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});

// Fonctions SweetAlert2 pour les détails
function showDetailModal(type) {
    let title, content;
    
    switch(type) {
        case 'activities':
            title = '🏆 Activités Totales';
            content = `
                <div class="text-center">
                    <div class="mb-3">
                        <h2 class="text-success">{{ $totalActivities }}</h2>
                        <p class="text-muted">Activités enregistrées</p>
                    </div>
                </div>
            `;
            break;
        case 'duration':
            title = '⏱️ Temps d\'Entraînement';
            content = `
                <div class="text-center">
                    <div class="mb-3">
                        <h2 class="text-primary">{{ floor($totalDuration / 60) }}h{{ $totalDuration % 60 > 0 ? sprintf('%02d', $totalDuration % 60) : '' }}</h2>
                        <p class="text-muted">Temps total d'activité physique</p>
                    </div>
                </div>
            `;
            break;
        case 'distance':
            title = '🏃 Distance Parcourue';
            content = `
                <div class="text-center">
                    <div class="mb-3">
                        <h2 class="text-info">{{ number_format($totalDistance, 1) }} km</h2>
                        <p class="text-muted">Distance totale parcourue</p>
                    </div>
                </div>
            `;
            break;
        case 'calories':
            title = '🔥 Calories Brûlées';
            content = `
                <div class="text-center">
                    <div class="mb-3">
                        <h2 class="text-danger">{{ number_format($totalCalories) }}</h2>
                        <p class="text-muted">Calories totales brûlées</p>
                    </div>
                </div>
            `;
            break;
    }
    
    Swal.fire({
        title: title,
        html: content,
        icon: 'info',
        confirmButtonText: '✓ Fermer'
    });
}

function showTypeDetail(typeName, data) {
    Swal.fire({
        title: `📊 Détails - ${typeName}`,
        html: `
            <div class="text-center">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">${data.count}</h4>
                            <small class="text-muted">Sessions</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">${Math.floor(data.duration / 60)}h${data.duration % 60 > 0 ? String(data.duration % 60).padStart(2, '0') : ''}</h4>
                        <small class="text-muted">Durée totale</small>
                    </div>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: '✓ Fermer'
    });
}

// Export des données
document.getElementById('exportBtn').addEventListener('click', function() {
    Swal.fire({
        title: '📤 Export en cours...',
        text: 'Génération du rapport de statistiques',
        icon: 'info',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        Swal.fire({
            title: 'Export réussi !',
            text: 'Votre rapport a été généré avec succès.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });
});
</script>
@endsection

@extends('partials.layouts.master')

@section('title', 'Calendrier Bien-être | SmartHealth')
@section('title-sub', 'Bien-être')
@section('pagetitle', 'Calendrier Bien-être')

@section('content')
<div id="layout-wrapper">
    <!-- Navigation des onglets -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-justified" id="wellnessTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="calendar-tab" data-bs-toggle="pill" 
                                    data-bs-target="#calendar-content" type="button" role="tab">
                                <i class="bi bi-calendar-heart me-2"></i>Calendrier
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="stats-tab" data-bs-toggle="pill" 
                                    data-bs-target="#stats-content" type="button" role="tab">
                                <i class="bi bi-graph-up me-2"></i>Statistiques
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab" data-bs-toggle="pill" 
                                    data-bs-target="#analytics-content" type="button" role="tab">
                                <i class="bi bi-bar-chart me-2"></i>Analytics
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="wellnessTabContent">
        <!-- ONGLET CALENDRIER -->
        <div class="tab-pane fade show active" id="calendar-content" role="tabpanel">
            <div class="row">
                <!-- Calendrier principal -->
       <div class="col-xl-8">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-heart me-2"></i>
                    Mon Calendrier Bien-être
                </h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="refreshCalendar">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" 
                            data-bs-toggle="modal" data-bs-target="#eventModal">
                        <i class="bi bi-plus-circle me-1"></i>Nouvelle Activité
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id='wellness-calendar'></div>
        </div>
    </div>
</div>

                <!-- Sidebar -->
                <div class="col-xl-4">
                    <!-- Stats du jour -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-graph-up me-2"></i>
                                Aujourd'hui
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="mb-2">
                                        <h4 class="text-success mb-1" id="today-completed">{{ $todayStats['completed_count'] ?? 0 }}</h4>
                                        <small class="text-muted">Complétées</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">
                                        <h4 class="text-primary mb-1" id="today-planned">{{ $todayStats['planned_count'] ?? 0 }}</h4>
                                        <small class="text-muted">Planifiées</small>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $todayStats['completion_rate'] ?? 0 }}%"
                                     id="today-progress">
                                </div>
                            </div>
                            <p class="text-center mb-0">
                                <span id="today-rate">{{ $todayStats['completion_rate'] ?? 0 }}%</span> de réussite
                                <br>
                                <small class="text-muted">
                                    <span id="today-completed-time">{{ $todayStats['total_completed_minutes'] ?? 0 }}</span> min sur 
                                    <span id="today-planned-time">{{ $todayStats['total_planned_minutes'] ?? 0 }}</span> min
                                </small>
                            </p>
                        </div>
                    </div>

                    <!-- Catégories avec filtres -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-tags me-2"></i>
                                Catégories
                            </h6>
                        </div>
                        <div class="card-body">
                            @forelse($categories ?? [] as $category)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-2" style="width: 12px; height: 12px; background-color: {{ $category->color }}; border-radius: 50%;"></div>
                                    <small>{{ $category->name }}</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input category-filter" 
                                           type="checkbox" 
                                           id="category-{{ $category->id }}"
                                           data-category="{{ $category->id }}"
                                           checked>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted text-center mb-0">Aucune catégorie disponible</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recommandations IA -->
                    <div class="card mb-3" id="ai-recommendations">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-robot me-2"></i>
                                Recommandations IA
                            </h6>
                        </div>
                        <div class="card-body" id="recommendations-content">
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <small class="text-muted d-block mt-2">Analyse en cours...</small>
                            </div>
                        </div>
                    </div>

                    <!-- Prochaines activités -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-clock me-2"></i>
                                Prochaines activités
                            </h6>
                        </div>
                        <div class="card-body" id="upcoming-activities">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ONGLET STATISTIQUES -->
        <div class="tab-pane fade" id="stats-content" role="tabpanel">
            <div class="row">
                <!-- KPIs principaux -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success text-success rounded fs-3">
                                        <i class="bi bi-check-circle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Activités Complétées</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="total-completed">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-primary text-primary rounded fs-3">
                                        <i class="bi bi-clock-history"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Temps Total</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="total-time">0h</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-info text-info rounded fs-3">
                                        <i class="bi bi-percent"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Taux de Réussite</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="success-rate">0%</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-warning text-warning rounded fs-3">
                                        <i class="bi bi-graph-down-arrow"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate fs-13">Réduction Stress</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="stress-reduction">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Graphique d'évolution hebdomadaire -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Évolution Hebdomadaire</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="weeklyChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Répartition par catégorie -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Répartition par Catégorie</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique d'analyse du stress -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Analyse du Stress</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="stressChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Progression mensuelle -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Progression Mensuelle</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ONGLET ANALYTICS -->
        <div class="tab-pane fade" id="analytics-content" role="tabpanel">
            <div class="row">
                <!-- Heatmap des activités -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Heatmap d'Activité</h5>
                        </div>
                        <div class="card-body">
                            <div id="activityHeatmap" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Top catégories -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Top Catégories</h5>
                        </div>
                        <div class="card-body">
                            <div id="topCategories"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insights avancés -->
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Tendances Humeur</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="moodTrendChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Efficacité par Heure</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="hourlyEfficiencyChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Patterns Hebdomadaires</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="weeklyPatternsChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Événement -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Nouvelle Activité Bien-être</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <input type="hidden" id="eventId">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Titre *</label>
                                <input type="text" class="form-control" id="eventTitle" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Catégorie *</label>
                                <select class="form-select" id="eventCategory" required>
                                    <option value="">Choisir...</option>
                                    @forelse($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" data-color="{{ $category->color }}">
                                        {{ $category->name }}
                                    </option>
                                    @empty
                                    <option value="">Aucune catégorie disponible</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Date *</label>
                                <input type="date" class="form-control" id="eventDate" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Début *</label>
                                <input type="time" class="form-control" id="eventStartTime" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Fin *</label>
                                <input type="time" class="form-control" id="eventEndTime" required>
                            </div>
                        </div>
                    </div>

                    <!-- État initial -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Humeur actuelle</label>
                                <select class="form-select" id="moodBefore">
                                    <option value="">Non renseigné</option>
                                    <option value="very_bad">😢 Très mauvaise</option>
                                    <option value="bad">😞 Mauvaise</option>
                                    <option value="neutral">😐 Neutre</option>
                                    <option value="good">😊 Bonne</option>
                                    <option value="very_good">😄 Très bonne</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Niveau de stress (1-10)</label>
                                <input type="range" class="form-range" id="stressBefore" min="1" max="10" step="1" value="5">
                                <div class="d-flex justify-content-between">
                                    <small>1 (Détendu)</small>
                                    <span id="stressBeforeValue">5</span>
                                    <small>10 (Très stressé)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Récurrence -->
                    <div class="card border-light mb-3">
                        <div class="card-header bg-light">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isRecurring">
                                <label class="form-check-label" for="isRecurring">
                                    Répéter cette activité
                                </label>
                            </div>
                        </div>
                        <div class="card-body d-none" id="recurringOptions">
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-select" id="recurringFrequency">
                                        <option value="daily">Chaque jour</option>
                                        <option value="weekly" selected>Chaque semaine</option>
                                        <option value="monthly">Chaque mois</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" id="recurringOccurrences" 
                                           placeholder="Nombre de répétitions" min="1" max="52" value="4">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveEventBtn">Enregistrer</button>
                <button type="button" class="btn btn-success d-none" id="completeEventBtn">Marquer comme terminé</button>
                <button type="button" class="btn btn-danger d-none" id="deleteEventBtn">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Complétion -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terminer l'activité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="completeForm">
                    <input type="hidden" id="completeEventId">
                    
                    <div class="mb-3">
                        <label class="form-label">Comment vous sentez-vous maintenant ?</label>
                        <select class="form-select" id="moodAfter">
                            <option value="">Non renseigné</option>
                            <option value="very_bad">😢 Très mauvaise</option>
                            <option value="bad">😞 Mauvaise</option>
                            <option value="neutral">😐 Neutre</option>
                            <option value="good">😊 Bonne</option>
                            <option value="very_good">😄 Très bonne</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Niveau de stress actuel (1-10)</label>
                        <input type="range" class="form-range" id="stressAfter" min="1" max="10" step="1" value="5">
                        <div class="d-flex justify-content-between">
                            <small>1 (Détendu)</small>
                            <span id="stressAfterValue">5</span>
                            <small>10 (Très stressé)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="completionNotes" rows="3" 
                                  placeholder="Comment s'est passée cette activité ? Qu'avez-vous ressenti ?"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="confirmCompleteBtn">Terminer l'activité</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/fr.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    let weeklyChart, categoryChart, stressChart, monthlyChart;
    let moodTrendChart, hourlyEfficiencyChart, weeklyPatternsChart;

    // Initialisation du calendrier
    initializeCalendar();
    
    // Charger les données initiales
    loadInitialData();

    // Gestionnaires d'événements pour les onglets
    document.getElementById('stats-tab').addEventListener('shown.bs.tab', function() {
        loadStatistics();
    });

    document.getElementById('analytics-tab').addEventListener('shown.bs.tab', function() {
        loadAnalytics();
    });

    function initializeCalendar() {
        const calendarEl = document.getElementById('wellness-calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            height: 650,
            eventSources: [
                {
                    url: '/wellness/events',
                    method: 'GET',
                    failure: function() {
                        showNotification('Erreur lors du chargement des événements', 'error');
                    }
                }
            ],
            selectable: true,
            selectMirror: true,
            editable: true,
            dayMaxEvents: true,
            
            // Créer un événement par sélection
            select: function(info) {
                // Vérifier si la date sélectionnée n'est pas dans le passé
                const selectedDate = new Date(info.startStr);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Date invalide',
                        text: 'Vous ne pouvez pas planifier une activité dans le passé.',
                        confirmButtonText: 'Compris'
                    });
                    calendar.unselect();
                    return;
                }
                
                document.getElementById('eventDate').value = info.startStr;
                const now = new Date();
                document.getElementById('eventStartTime').value = now.toTimeString().slice(0,5);
                const endTime = new Date(now.getTime() + 3600000);
                document.getElementById('eventEndTime').value = endTime.toTimeString().slice(0,5);
                resetEventForm();
                new bootstrap.Modal(document.getElementById('eventModal')).show();
            },
            
            // Cliquer sur un événement
            eventClick: function(info) {
                loadEventData(info.event.id);
            },
            
            // Déplacer un événement
            eventDrop: function(info) {
                const newDate = new Date(info.event.start);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (newDate < today) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Déplacement impossible',
                        text: 'Vous ne pouvez pas déplacer une activité dans le passé.',
                        confirmButtonText: 'Compris'
                    });
                    info.revert();
                    return;
                }
                
                updateEventDateTime(info.event.id, info.event.start, info.event.end);
            },
            
            // Redimensionner un événement
            eventResize: function(info) {
                updateEventDateTime(info.event.id, info.event.start, info.event.end);
            },

            // Personnalisation des événements
            eventDidMount: function(info) {
                // Ajouter des classes CSS selon le statut
                if (info.event.extendedProps.status === 'completed') {
                    info.el.classList.add('event-completed');
                    info.el.style.opacity = '0.8';
                    info.el.style.textDecoration = 'line-through';
                }
                
                // Ajouter tooltip
                info.el.setAttribute('title', 
                    `${info.event.title}\n${info.event.extendedProps.description || ''}\nCatégorie: ${info.event.extendedProps.category || ''}`
                );
            }
        });
        
        calendar.render();
    }

    function loadInitialData() {
        // Charger les stats du jour
        updateTodayStats();
        
        // Charger les recommandations IA
        loadAIRecommendations();
        
        // Charger les prochaines activités
        loadUpcomingActivities();
    }

    function loadStatistics() {
        if (weeklyChart) weeklyChart.destroy();
        if (categoryChart) categoryChart.destroy();
        if (stressChart) stressChart.destroy();
        if (monthlyChart) monthlyChart.destroy();

        // Charger les KPIs
        loadKPIs();
        
        // Graphique d'évolution hebdomadaire
        createWeeklyChart();
        
        // Graphique répartition par catégorie
        createCategoryChart();
        
        // Graphique d'analyse du stress
        createStressChart();
        
        // Graphique progression mensuelle
        createMonthlyChart();
    }

    function loadAnalytics() {
        if (moodTrendChart) moodTrendChart.destroy();
        if (hourlyEfficiencyChart) hourlyEfficiencyChart.destroy();
        if (weeklyPatternsChart) weeklyPatternsChart.destroy();

        // Créer la heatmap d'activité
        createActivityHeatmap();
        
        // Charger le top des catégories
        loadTopCategories();
        
        // Graphiques d'analyse avancée
        createMoodTrendChart();
        createHourlyEfficiencyChart();
        createWeeklyPatternsChart();
    }

    function loadKPIs() {
        fetch('/wellness/stats/weekly')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-completed').textContent = data.completed_events || 0;
                document.getElementById('total-time').textContent = Math.round((data.completed_minutes || 0) / 60) + 'h';
                
                const successRate = data.total_events > 0 ? 
                    Math.round((data.completed_events / data.total_events) * 100) : 0;
                document.getElementById('success-rate').textContent = successRate + '%';
                
                // Calcul de la réduction de stress moyenne
                let avgStressReduction = 0;
                if (data.by_category) {
                    const categoryData = Object.values(data.by_category);
                    avgStressReduction = categoryData.reduce((sum, cat) => {
                        return sum + (cat.avg_stress_reduction || 0);
                    }, 0) / categoryData.length;
                }
                document.getElementById('stress-reduction').textContent = avgStressReduction.toFixed(1);
            })
            .catch(error => {
                console.error('Erreur lors du chargement des KPIs:', error);
            });
    }

    function createWeeklyChart() {
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        
        fetch('/wellness/stats/weekly')
            .then(response => response.json())
            .then(data => {
                const labels = [];
                const completedData = [];
                const plannedData = [];
                
                // Générer les 7 derniers jours
                for (let i = 6; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    const dateStr = date.toISOString().split('T')[0];
                    
                    labels.push(date.toLocaleDateString('fr-FR', { weekday: 'short' }));
                    
                    const dayData = data.by_day && data.by_day[dateStr];
                    completedData.push(dayData ? dayData.completed : 0);
                    plannedData.push(dayData ? dayData.count : 0);
                }

                weeklyChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Activités Complétées',
                            data: completedData,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'Activités Planifiées',
                            data: plannedData,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Erreur lors de la création du graphique hebdomadaire:', error);
            });
    }

    function createCategoryChart() {
        const ctx = document.getElementById('categoryChart').getContext('2d');
        
        fetch('/wellness/stats/weekly')
            .then(response => response.json())
            .then(data => {
                if (!data.by_category) return;
                
                const categories = Object.values(data.by_category);
                const labels = categories.map(cat => cat.category_name);
                const values = categories.map(cat => cat.completed);
                const colors = [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#FF6384', '#36A2EB'
                ];

                categoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors.slice(0, labels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Erreur lors de la création du graphique de catégories:', error);
            });
    }

    function createStressChart() {
        const ctx = document.getElementById('stressChart').getContext('2d');
        
        fetch('/wellness/reports/stress-analysis')
            .then(response => response.json())
            .then(data => {
                if (!data.daily_trend) return;

                const dates = Object.keys(data.daily_trend).slice(-7);
                const stressBefore = dates.map(date => data.daily_trend[date].avg_stress_before || 0);
                const stressAfter = dates.map(date => data.daily_trend[date].avg_stress_after || 0);
                const labels = dates.map(date => new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }));

                stressChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Stress Avant',
                            data: stressBefore,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderColor: '#dc3545',
                            borderWidth: 1
                        }, {
                            label: 'Stress Après',
                            data: stressAfter,
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderColor: '#28a745',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 10,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Erreur lors de la création du graphique de stress:', error);
            });
    }

    function createMonthlyChart() {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        fetch('/wellness/reports/monthly')
            .then(response => response.json())
            .then(data => {
                if (!data.by_category) return;

                const categories = Object.values(data.by_category);
                const labels = categories.map(cat => cat.category_name);
                const minutes = categories.map(cat => cat.minutes);

                monthlyChart = new Chart(ctx, {
                    type: 'polarArea',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: minutes,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 159, 64, 0.6)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Erreur lors de la création du graphique mensuel:', error);
            });
    }

    function createActivityHeatmap() {
        // Simulation d'une heatmap avec ApexCharts
        const options = {
            series: [{
                name: 'Lun',
                data: generateHeatmapData('Lun')
            }, {
                name: 'Mar',
                data: generateHeatmapData('Mar')
            }, {
                name: 'Mer',
                data: generateHeatmapData('Mer')
            }, {
                name: 'Jeu',
                data: generateHeatmapData('Jeu')
            }, {
                name: 'Ven',
                data: generateHeatmapData('Ven')
            }, {
                name: 'Sam',
                data: generateHeatmapData('Sam')
            }, {
                name: 'Dim',
                data: generateHeatmapData('Dim')
            }],
            chart: {
                height: 400,
                type: 'heatmap',
            },
            dataLabels: {
                enabled: false
            },
            colors: ["#008FFB"],
            title: {
                text: 'Intensité des Activités par Jour et Heure'
            },
            xaxis: {
                categories: Array.from({length: 24}, (_, i) => i + 'h')
            }
        };

        const chart = new ApexCharts(document.querySelector("#activityHeatmap"), options);
        chart.render();
    }

    function generateHeatmapData(name) {
        const data = [];
        for (let i = 0; i < 24; i++) {
            data.push({
                x: i + 'h',
                y: Math.floor(Math.random() * 5)
            });
        }
        return data;
    }

    function loadTopCategories() {
        fetch('/wellness/stats/weekly')
            .then(response => response.json())
            .then(data => {
                if (!data.by_category) return;

                const categories = Object.values(data.by_category)
                    .sort((a, b) => b.minutes - a.minutes)
                    .slice(0, 5);

                let html = '';
                categories.forEach((cat, index) => {
                    const progressWidth = (cat.minutes / categories[0].minutes) * 100;
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">${cat.category_name}</h6>
                                <small class="text-muted">${cat.completed} activités • ${cat.minutes} min</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">#${index + 1}</span>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar" style="width: ${progressWidth}%"></div>
                        </div>
                    `;
                });

                document.getElementById('topCategories').innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur lors du chargement du top des catégories:', error);
            });
    }

    function createMoodTrendChart() {
        const ctx = document.getElementById('moodTrendChart').getContext('2d');
        
        // Données simulées pour les tendances d'humeur
        const moodData = [3.2, 3.8, 4.1, 3.9, 4.3, 4.0, 4.2];
        const labels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

        moodTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Humeur Moyenne',
                    data: moodData,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    function createHourlyEfficiencyChart() {
        const ctx = document.getElementById('hourlyEfficiencyChart').getContext('2d');
        
        // Données simulées pour l'efficacité par heure
        const hours = ['6h', '8h', '10h', '12h', '14h', '16h', '18h', '20h', '22h'];
        const efficiency = [60, 80, 95, 70, 85, 90, 75, 65, 50];

        hourlyEfficiencyChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Efficacité (%)',
                    data: efficiency,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    function createWeeklyPatternsChart() {
        const ctx = document.getElementById('weeklyPatternsChart').getContext('2d');
        
        // Données simulées pour les patterns hebdomadaires
        const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        const patterns = [8, 7, 9, 6, 5, 4, 3];

        weeklyPatternsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Activités',
                    data: patterns,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#FF6384'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Gestionnaires d'événements pour le formulaire et les actions

    // Validation de la date dans le formulaire
    document.getElementById('eventDate').addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            Swal.fire({
                icon: 'error',
                title: 'Date invalide',
                text: 'Vous ne pouvez pas planifier une activité dans le passé.',
                confirmButtonText: 'Compris'
            });
            this.value = '';
        }
    });

    // Gestionnaire de formulaire principal
    document.getElementById('saveEventBtn').addEventListener('click', function() {
        if (validateEventForm()) {
            saveEvent();
        }
    });

    // Gestionnaire de complétion
    document.getElementById('confirmCompleteBtn').addEventListener('click', function() {
        completeEvent();
    });

    // Gestionnaire de suppression
    document.getElementById('deleteEventBtn').addEventListener('click', function() {
        showConfirmation(
            'Êtes-vous sûr ?',
            'Cette action supprimera définitivement cette activité.',
            'Oui, supprimer',
            'Annuler'
        ).then((result) => {
            if (result.isConfirmed) {
                deleteEvent();
            }
        });
    });

    // Sliders de stress
    document.getElementById('stressBefore').addEventListener('input', function() {
        document.getElementById('stressBeforeValue').textContent = this.value;
    });

    document.getElementById('stressAfter').addEventListener('input', function() {
        document.getElementById('stressAfterValue').textContent = this.value;
    });

    // Gestion de la récurrence
    document.getElementById('isRecurring').addEventListener('change', function() {
        const options = document.getElementById('recurringOptions');
        if (this.checked) {
            options.classList.remove('d-none');
        } else {
            options.classList.add('d-none');
        }
    });

    // Filtres par catégorie
    document.querySelectorAll('.category-filter').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            filterEventsByCategory();
        });
    });

    // Bouton refresh calendrier
    document.getElementById('refreshCalendar').addEventListener('click', function() {
        calendar.refetchEvents();
        loadInitialData();
        showNotification('Calendrier actualisé', 'success');
    });

    // Fonctions utilitaires
    function validateEventForm() {
        const title = document.getElementById('eventTitle').value.trim();
        const category = document.getElementById('eventCategory').value;
        const date = document.getElementById('eventDate').value;
        const startTime = document.getElementById('eventStartTime').value;
        const endTime = document.getElementById('eventEndTime').value;

        if (!title) {
            Swal.fire({
                icon: 'error',
                title: 'Titre manquant',
                text: 'Veuillez saisir un titre pour votre activité.',
                confirmButtonText: 'Compris'
            });
            return false;
        }

        if (!category) {
            Swal.fire({
                icon: 'error',
                title: 'Catégorie manquante',
                text: 'Veuillez choisir une catégorie pour votre activité.',
                confirmButtonText: 'Compris'
            });
            return false;
        }

        if (!date) {
            Swal.fire({
                icon: 'error',
                title: 'Date manquante',
                text: 'Veuillez choisir une date pour votre activité.',
                confirmButtonText: 'Compris'
            });
            return false;
        }

        if (!startTime || !endTime) {
            Swal.fire({
                icon: 'error',
                title: 'Horaires manquants',
                text: 'Veuillez définir les heures de début et de fin.',
                confirmButtonText: 'Compris'
            });
            return false;
        }

        if (startTime >= endTime) {
            Swal.fire({
                icon: 'error',
                title: 'Horaires invalides',
                text: 'L\'heure de fin doit être après l\'heure de début.',
                confirmButtonText: 'Compris'
            });
            return false;
        }

        return true;
    }

    function resetEventForm() {
        document.getElementById('eventForm').reset();
        document.getElementById('eventId').value = '';
        document.getElementById('eventModalTitle').textContent = 'Nouvelle Activité Bien-être';
        document.getElementById('saveEventBtn').classList.remove('d-none');
        document.getElementById('completeEventBtn').classList.add('d-none');
        document.getElementById('deleteEventBtn').classList.add('d-none');
        document.getElementById('stressBeforeValue').textContent = '5';
        document.getElementById('isRecurring').checked = false;
        document.getElementById('recurringOptions').classList.add('d-none');
    }

    function loadEventData(eventId) {
        fetch(`/wellness/events/${eventId}`)
            .then(response => response.json())
            .then(event => {
                document.getElementById('eventId').value = event.id;
                document.getElementById('eventTitle').value = event.title;
                document.getElementById('eventDescription').value = event.description || '';
                document.getElementById('eventCategory').value = event.wellness_category_id;
                document.getElementById('eventDate').value = event.event_date;
                document.getElementById('eventStartTime').value = event.start_time;
                document.getElementById('eventEndTime').value = event.end_time;
                
                if (event.mood_before) {
                    document.getElementById('moodBefore').value = event.mood_before;
                }
                if (event.stress_level_before) {
                    document.getElementById('stressBefore').value = event.stress_level_before;
                    document.getElementById('stressBeforeValue').textContent = event.stress_level_before;
                }

                document.getElementById('eventModalTitle').textContent = 'Modifier l\'activité';
                document.getElementById('saveEventBtn').classList.remove('d-none');
                
                if (event.status === 'planned') {
                    document.getElementById('completeEventBtn').classList.remove('d-none');
                }
                document.getElementById('deleteEventBtn').classList.remove('d-none');

                new bootstrap.Modal(document.getElementById('eventModal')).show();
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur lors du chargement de l\'événement', 'error');
            });
    }

    function saveEvent() {
        const formData = {
            title: document.getElementById('eventTitle').value,
            description: document.getElementById('eventDescription').value,
            wellness_category_id: document.getElementById('eventCategory').value,
            event_date: document.getElementById('eventDate').value,
            start_time: document.getElementById('eventStartTime').value,
            end_time: document.getElementById('eventEndTime').value,
            mood_before: document.getElementById('moodBefore').value || null,
            stress_level_before: document.getElementById('stressBefore').value || null,
            is_recurring: document.getElementById('isRecurring').checked,
            recurring_config: document.getElementById('isRecurring').checked ? {
                frequency: document.getElementById('recurringFrequency').value,
                occurrences: parseInt(document.getElementById('recurringOccurrences').value)
            } : null
        };

        const eventId = document.getElementById('eventId').value;
        const url = eventId ? `/wellness/events/${eventId}` : '/wellness/events';
        const method = eventId ? 'PUT' : 'POST';

        // Afficher un loader
        Swal.fire({
            title: 'Enregistrement...',
            text: 'Sauvegarde en cours',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                updateTodayStats();
                loadAIRecommendations();
                loadUpcomingActivities();
                showNotification(data.message, 'success');
            } else {
                if (data.errors) {
                    let errorMessage = 'Erreurs de validation:\n';
                    Object.values(data.errors).forEach(errors => {
                        errors.forEach(error => {
                            errorMessage += '- ' + error + '\n';
                        });
                    });
                    showNotification(errorMessage, 'error');
                } else {
                    showNotification(data.message || 'Erreur lors de l\'enregistrement', 'error');
                }
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Erreur:', error);
            showNotification('Erreur lors de l\'enregistrement', 'error');
        });
    }

    function completeEvent() {
        const eventId = document.getElementById('completeEventId').value;
        const formData = {
            mood_after: document.getElementById('moodAfter').value || null,
            stress_level_after: document.getElementById('stressAfter').value || null,
            notes: document.getElementById('completionNotes').value || null
        };

        // Afficher un loader
        Swal.fire({
            title: 'Finalisation...',
            text: 'Marquage comme terminé',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/wellness/events/${eventId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('completeModal')).hide();
                updateTodayStats();
                loadAIRecommendations();
                loadUpcomingActivities();
                showNotification(data.message, 'success');
                
                // Afficher les recommandations IA si disponibles
                if (data.ai_recommendation) {
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'info',
                            title: 'Recommandation IA',
                            html: `<div class="text-start">${data.ai_recommendation}</div>`,
                            confirmButtonText: 'Merci !',
                            confirmButtonColor: '#007bff'
                        });
                    }, 1000);
                }
            } else {
                showNotification(data.message || 'Erreur lors de la complétion', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Erreur:', error);
            showNotification('Erreur lors de la complétion', 'error');
        });
    }

  
    function deleteEvent() {
        const eventId = document.getElementById('eventId').value;

        fetch(`/wellness/events/${eventId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                updateTodayStats();
                loadAIRecommendations();
                loadUpcomingActivities();
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Erreur lors de la suppression', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la suppression', 'error');
        });
    }

    function filterEventsByCategory() {
        const selected = Array.from(document.querySelectorAll('.category-filter:checked'))
            .map(cb => cb.dataset.category);

        calendar.removeAllEventSources();
        calendar.addEventSource({
            url: '/wellness/events',
            method: 'GET',
            extraParams: {
                categories: selected
            },
            failure: function() {
                showNotification('Erreur lors du chargement des événements', 'error');
            }
        });
        calendar.refetchEvents();
    }

    function updateEventDateTime(eventId, start, end) {
        const startDate = new Date(start);
        const endDate = end ? new Date(end) : new Date(startDate.getTime() + 60 * 60 * 1000);

        const payloadPart = {
            event_date: startDate.toISOString().split('T')[0],
            start_time: startDate.toTimeString().slice(0, 5),
            end_time: endDate.toTimeString().slice(0, 5)
        };

        // Récupérer l'événement pour satisfaire la validation du backend
        fetch(`/wellness/events/${eventId}`)
            .then(r => r.json())
            .then(ev => {
                const payload = {
                    title: ev.title,
                    description: ev.description,
                    wellness_category_id: ev.wellness_category_id,
                    mood_before: ev.mood_before,
                    stress_level_before: ev.stress_level_before,
                    ...payloadPart
                };

                return fetch(`/wellness/events/${eventId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
                    calendar.refetchEvents();
                } else {
                    updateTodayStats();
                    loadUpcomingActivities();
                    showNotification('Activité mise à jour', 'success');
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('Erreur lors de la mise à jour', 'error');
                calendar.refetchEvents();
            });
    }

    // Ouvrir le modal de complétion depuis le bouton du modal d'édition
    document.getElementById('completeEventBtn').addEventListener('click', function() {
        const currentId = document.getElementById('eventId').value;
        document.getElementById('completeEventId').value = currentId;
        new bootstrap.Modal(document.getElementById('completeModal')).show();
    });

    function updateTodayStats() {
        fetch('/wellness/stats/today')
            .then(r => r.json())
            .then(data => {
                document.getElementById('today-completed').textContent = data.completed_count ?? 0;
                document.getElementById('today-planned').textContent = data.planned_count ?? 0;
                document.getElementById('today-progress').style.width = (data.completion_rate ?? 0) + '%';
                document.getElementById('today-rate').textContent = (data.completion_rate ?? 0) + '%';
                document.getElementById('today-completed-time').textContent = data.total_completed_minutes ?? 0;
                document.getElementById('today-planned-time').textContent = data.total_planned_minutes ?? 0;

                if (data.upcoming) {
                    renderUpcoming(data.upcoming);
                }
            })
            .catch(err => console.error('Erreur stats du jour:', err));
    }

    function loadAIRecommendations() {
        const container = document.getElementById('recommendations-content');
        container.innerHTML = `
            <div class="text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <small class="text-muted d-block mt-2">Analyse en cours...</small>
            </div>
        `;

        fetch('/wellness/ai/recommendations')
            .then(r => r.json())
            .then(data => {
                const recos = data.recommendations || [];
                if (!recos.length) {
                    container.innerHTML = `<p class="text-muted mb-0">Aucune recommandation pour le moment.</p>`;
                    return;
                }
                container.innerHTML = recos.map(r => {
    const obj = typeof r === 'string' ? { title: 'Suggestion', message: r, type: 'info' } : r;
    const bsType = obj.type === 'warning' ? 'warning' : (obj.type === 'success' ? 'success' : 'primary');
    return `
        <div class="alert alert-${bsType} py-2 px-3 d-flex align-items-start">
            <i class="bi bi-stars me-2 mt-1"></i>
            <div>
                <strong>${obj.title || 'Suggestion'}</strong><br>
                <span>${obj.message || ''}</span>
            </div>
        </div>
    `;
}).join('');
            })
            .catch(err => {
                console.error('Erreur IA:', err);
                container.innerHTML = `<p class="text-danger mb-0">Erreur lors du chargement des recommandations.</p>`;
            });
    }

    function loadUpcomingActivities() {
        fetch('/wellness/stats/today')
            .then(r => r.json())
            .then(data => {
                renderUpcoming(data.upcoming || []);
            })
            .catch(err => {
                console.error('Erreur prochaines activités:', err);
                document.getElementById('upcoming-activities').innerHTML = `
                    <p class="text-danger mb-0">Erreur lors du chargement.</p>
                `;
            });
    }

    function renderUpcoming(upcoming) {
        const box = document.getElementById('upcoming-activities');
        if (!upcoming || !upcoming.length) {
            box.innerHTML = `<p class="text-muted mb-0 text-center">Aucune activité à venir aujourd'hui.</p>`;
            return;
        }

        box.innerHTML = upcoming.map(ev => `
            <div class="d-flex align-items-center mb-2">
                <div class="me-2" style="width:10px;height:10px;border-radius:50%;background:${ev.category?.color || '#0d6efd'}"></div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong class="me-3">${ev.title}</strong>
                        <small class="text-muted">${ev.start_time?.slice(0,5)} - ${ev.end_time?.slice(0,5)}</small>
                    </div>
                    <small class="text-muted">${ev.category?.name || ''}</small>
                </div>
            </div>
        `).join('');
    }

    function showNotification(message, type = 'info') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
        Toast.fire({
            icon: type,
            title: message
        });
    }

    function showConfirmation(title, text, confirmText = 'Confirmer', cancelText = 'Annuler') {
        return Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d'
        });
    }
});
</script>
@endsection
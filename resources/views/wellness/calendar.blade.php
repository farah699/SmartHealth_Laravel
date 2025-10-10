{{-- filepath: resources/views/wellness/calendar.blade.php --}}
@extends('partials.layouts.master')

@section('title', 'Calendrier Bien-être | SmartHealth')
@section('title-sub', 'Bien-être')
@section('pagetitle', 'Calendrier Bien-être')

@section('content')
<div id="layout-wrapper">
    <div class="row">
        <!-- Calendrier principal -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-heart me-2"></i>
                        Mon Calendrier Bien-être
                    </h5>
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
                                <h4 class="text-primary mb-1" id="today-completed">{{ $todayStats['completed_count'] }}</h4>
                                <small class="text-muted">Complétées</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <h4 class="text-info mb-1" id="today-planned">{{ $todayStats['planned_count'] }}</h4>
                                <small class="text-muted">Planifiées</small>
                            </div>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $todayStats['completion_rate'] }}%"
                             id="today-progress">
                        </div>
                    </div>
                    <p class="text-center mb-0">
                        <span id="today-rate">{{ $todayStats['completion_rate'] }}%</span> de réussite
                        <br>
                        <small class="text-muted">
                            {{ $todayStats['total_completed_minutes'] }} min sur {{ $todayStats['total_planned_minutes'] }} min
                        </small>
                    </p>
                </div>
            </div>

            <!-- Bouton Créer -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <button type="button" class="btn btn-primary w-100" 
                            data-bs-toggle="modal" data-bs-target="#eventModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouvelle Activité
                    </button>
                </div>
            </div>

            <!-- Catégories -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-tags me-2"></i>
                        Catégories
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($categories as $category)
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
                    @endforeach
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
                    @forelse($todayStats['upcoming'] as $event)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="mb-0">{{ $event->title }}</h6>
                            <small class="text-muted">
                                {{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}
                            </small>
                        </div>
                        <button class="btn btn-sm btn-outline-success complete-btn" 
                                data-event-id="{{ $event->id }}">
                            <i class="bi bi-check"></i>
                        </button>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Aucune activité prévue</p>
                    @endforelse
                </div>
            </div>

            <!-- Recommandations -->
            @if(!empty($recommendations))
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        Recommandations
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($recommendations as $recommendation)
                    <div class="alert alert-{{ $recommendation['type'] === 'warning' ? 'warning' : ($recommendation['type'] === 'info' ? 'info' : 'success') }} alert-dismissible p-2 mb-2">
                        <strong>{{ $recommendation['title'] }}</strong><br>
                        <small>{{ $recommendation['message'] }}</small>
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
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
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-color="{{ $category->color }}">
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du calendrier
    const calendarEl = document.getElementById('wellness-calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 600,
        events: '/wellness/events',
        selectable: true,
        selectMirror: true,
        editable: true,
        
        // Créer un événement par sélection
        select: function(info) {
            document.getElementById('eventDate').value = info.startStr;
            document.getElementById('eventStartTime').value = new Date().toTimeString().slice(0,5);
            document.getElementById('eventEndTime').value = new Date(Date.now() + 3600000).toTimeString().slice(0,5);
            resetEventForm();
            new bootstrap.Modal(document.getElementById('eventModal')).show();
        },
        
        // Cliquer sur un événement
        eventClick: function(info) {
            loadEventData(info.event.id);
        },
        
        // Déplacer un événement
        eventDrop: function(info) {
            updateEventDateTime(info.event.id, info.event.start, info.event.end);
        },
        
        // Redimensionner un événement
        eventResize: function(info) {
            updateEventDateTime(info.event.id, info.event.start, info.event.end);
        }
    });
    
    calendar.render();

    // Gestionnaire de formulaire principal
    document.getElementById('saveEventBtn').addEventListener('click', function() {
        saveEvent();
    });

    // Gestionnaire de complétion
    document.getElementById('confirmCompleteBtn').addEventListener('click', function() {
        completeEvent();
    });

    // Gestionnaire de suppression
    document.getElementById('deleteEventBtn').addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) {
            deleteEvent();
        }
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

    // Boutons de complétion rapide
    document.querySelectorAll('.complete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            document.getElementById('completeEventId').value = eventId;
            new bootstrap.Modal(document.getElementById('completeModal')).show();
        });
    });

    // Fonctions utilitaires
    function resetEventForm() {
        document.getElementById('eventForm').reset();
        document.getElementById('eventId').value = '';
        document.getElementById('eventModalTitle').textContent = 'Nouvelle Activité Bien-être';
        document.getElementById('saveEventBtn').classList.remove('d-none');
        document.getElementById('completeEventBtn').classList.add('d-none');
        document.getElementById('deleteEventBtn').classList.add('d-none');
        document.getElementById('stressBeforeValue').textContent = '5';
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
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                updateTodayStats();
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Erreur lors de l\'enregistrement', 'error');
            }
        })
        .catch(error => {
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
            if (data.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('completeModal')).hide();
                updateTodayStats();
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Erreur lors de la complétion', 'error');
            }
        })
        .catch(error => {
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

    function updateEventDateTime(eventId, start, end) {
        const formData = {
            event_date: start.toISOString().split('T')[0],
            start_time: start.toTimeString().slice(0, 5),
            end_time: end ? end.toTimeString().slice(0, 5) : null
        };

        fetch(`/wellness/events/${eventId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .catch(error => {
            console.error('Erreur lors du déplacement:', error);
        });
    }

    function updateTodayStats() {
        fetch('/wellness/stats/today')
            .then(response => response.json())
            .then(stats => {
                document.getElementById('today-completed').textContent = stats.completed_count;
                document.getElementById('today-planned').textContent = stats.planned_count;
                document.getElementById('today-rate').textContent = stats.completion_rate + '%';
                document.getElementById('today-progress').style.width = stats.completion_rate + '%';
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour des stats:', error);
            });
    }

    function filterEventsByCategory() {
        // Récupérer les catégories sélectionnées
        const selectedCategories = [];
        document.querySelectorAll('.category-filter:checked').forEach(checkbox => {
            selectedCategories.push(checkbox.getAttribute('data-category'));
        });
        
        // Rafraîchir le calendrier avec les filtres
        calendar.refetchEvents();
    }

    function showNotification(message, type) {
        // Créer une notification toast
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible position-fixed`;
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }
});
</script>

<style>
/* Styles personnalisés pour le calendrier */
.fc-event {
    border-radius: 6px;
    border: none !important;
    font-size: 12px;
    padding: 2px 4px;
    cursor: pointer;
}

.fc-event:hover {
    opacity: 0.8;
}

.status-completed {
    opacity: 0.8;
    text-decoration: line-through;
}

.status-cancelled {
    opacity: 0.5;
    background-color: #6c757d !important;
}

.status-missed {
    opacity: 0.6;
    background-color: #dc3545 !important;
}

.form-range {
    margin: 10px 0;
}

.card-header.bg-light {
    background-color: #f8f9fa !important;
}

/* Animation pour les notifications */
.alert {
    animation: slideIn 0.3s ease-in-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .col-xl-8, .col-xl-4 {
        margin-bottom: 1rem;
    }
    
    .fc-header-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }
}

/* Amélioration des couleurs de progression */
.progress-bar {
    background: linear-gradient(45deg, #007bff, #0056b3);
    transition: width 0.3s ease;
}

/* Style pour les boutons de catégorie */
.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

/* Animation pour les cartes */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Style pour les boutons de completion */
.complete-btn {
    transition: all 0.2s ease;
}

.complete-btn:hover {
    transform: scale(1.1);
}
</style>
@endsection
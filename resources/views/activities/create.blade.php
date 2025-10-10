<?php
// filepath: c:\Users\ferie\OneDrive\Bureau\projetLaravel\SmartHealth_Laravel\resources\views\activities\create.blade.php
?>
@extends('partials.layouts.master')

@section('title', 'Ajouter une activité | SmartHealth')
@section('title-sub', 'Activités Sportives')
@section('pagetitle', 'Ajouter une activité')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/tom-select/tom-select.min.css') }}">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Animate.css pour les animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endsection

@section('content')
<div id="layout-wrapper">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            {{-- En-tête --}}
            <div class="card border-0 bg-primary-subtle mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-plus-circle text-white fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 text-primary">Ajouter une nouvelle activité</h4>
                            <p class="text-muted mb-0">Enregistrez votre séance sportive pour suivre vos progrès</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages d'erreur --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Erreurs de validation :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Formulaire principal --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-heart-pulse me-2 text-danger"></i>
                            Détails de l'activité
                        </h5>
                        <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('activities.store') }}" method="POST" id="activityForm">
                        @csrf
                        
                        <div class="row">
                            {{-- Nom de l'activité --}}
                            <div class="col-md-12 mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bi bi-tag me-1"></i>Nom de l'activité *
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       placeholder="Ex: Footing matinal, Session fitness..."
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Type d'activité --}}
                            <div class="col-md-6 mb-4">
                                <label for="type" class="form-label fw-semibold">
                                    <i class="bi bi-grid me-1"></i>Type d'activité *
                                </label>
                                <div class="row g-2">
                                    @foreach($activityTypes as $key => $type)
                                    <div class="col-6">
                                        <input type="radio" 
                                               class="btn-check" 
                                               name="type" 
                                               id="type_{{ $key }}" 
                                               value="{{ $key }}"
                                               {{ old('type') == $key ? 'checked' : '' }}>
                                        <label class="btn btn-outline-{{ $type['color'] }} w-100 p-3" for="type_{{ $key }}">
                                            <i class="{{ $type['icon'] }} fs-4 d-block mb-1"></i>
                                            <small>{{ $type['name'] }}</small>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @error('type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Intensité --}}
                            <div class="col-md-6 mb-4">
                                <label for="intensity" class="form-label fw-semibold">
                                    <i class="bi bi-speedometer me-1"></i>Intensité *
                                </label>
                                <select class="form-select form-select-lg @error('intensity') is-invalid @enderror" 
                                        id="intensity" 
                                        name="intensity" 
                                        required>
                                    <option value="">Choisir l'intensité</option>
                                    @foreach($intensityLevels as $key => $intensity)
                                        <option value="{{ $key }}" 
                                                {{ old('intensity') == $key ? 'selected' : '' }}
                                                data-description="{{ $intensity['description'] }}">
                                            {{ $intensity['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted" id="intensityDescription"></small>
                                @error('intensity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Option activité récurrente --}}
                            <div class="col-12 mb-4">
                                <div class="card bg-info-subtle">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-arrow-repeat me-1"></i>Type d'activité
                                        </h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_recurring">
                                                <strong>Activité récurrente</strong> - Je pratique cette activité régulièrement et je veux pouvoir ajouter des sessions quotidiennes
                                            </label>
                                        </div>
                                        <div id="recurring-options" class="mt-3" style="display: {{ old('is_recurring') ? 'block' : 'none' }};">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle me-2"></i>
                                                <small>En activant cette option, vous créez une activité "modèle" à laquelle vous pourrez ajouter des sessions individuelles avec leurs propres données (durée, distance, notes, etc.)</small>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="target_sessions_per_week" class="form-label">Objectif sessions/semaine</label>
                                                    <select class="form-select @error('target_sessions_per_week') is-invalid @enderror" 
                                                            id="target_sessions_per_week" 
                                                            name="target_sessions_per_week">
                                                        <option value="">Choisir...</option>
                                                        <option value="1" {{ old('target_sessions_per_week') == '1' ? 'selected' : '' }}>1 session/semaine</option>
                                                        <option value="2" {{ old('target_sessions_per_week') == '2' ? 'selected' : '' }}>2 sessions/semaine</option>
                                                        <option value="3" {{ old('target_sessions_per_week', '3') == '3' ? 'selected' : '' }}>3 sessions/semaine</option>
                                                        <option value="4" {{ old('target_sessions_per_week') == '4' ? 'selected' : '' }}>4 sessions/semaine</option>
                                                        <option value="5" {{ old('target_sessions_per_week') == '5' ? 'selected' : '' }}>5 sessions/semaine</option>
                                                        <option value="6" {{ old('target_sessions_per_week') == '6' ? 'selected' : '' }}>6 sessions/semaine</option>
                                                        <option value="7" {{ old('target_sessions_per_week') == '7' ? 'selected' : '' }}>Tous les jours</option>
                                                    </select>
                                                    @error('target_sessions_per_week')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="activity_description" class="form-label">Description générale</label>
                                                    <input type="text" class="form-control @error('activity_description') is-invalid @enderror" 
                                                           id="activity_description" 
                                                           name="activity_description" 
                                                           value="{{ old('activity_description') }}" 
                                                           placeholder="Ex: Course matinale quotidienne, Séances gym">
                                                    @error('activity_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div id="single-session-info" class="mt-2" style="display: {{ old('is_recurring') ? 'none' : 'block' }};">
                                            <div class="alert alert-success">
                                                <i class="bi bi-check-circle me-2"></i>
                                                <small><strong>Activité simple</strong> - Les informations ci-dessous seront enregistrées pour cette session unique.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Date et heure (masqués si récurrent) --}}
                            <div id="session-details">
                                <div class="col-md-6 mb-4">
                                    <label for="activity_date" class="form-label fw-semibold">
                                        <i class="bi bi-calendar-date me-1"></i><span id="date-label">Date de l'activité</span> *
                                    </label>
                                    <input type="date" 
                                           class="form-control form-control-lg @error('activity_date') is-invalid @enderror" 
                                           id="activity_date" 
                                           name="activity_date" 
                                           value="{{ old('activity_date', date('Y-m-d')) }}"
                                           max="{{ date('Y-m-d') }}"
                                           required>
                                    @error('activity_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="start_time" class="form-label fw-semibold">
                                        <i class="bi bi-clock me-1"></i>Heure de début
                                    </label>
                                    <input type="time" 
                                           class="form-control form-control-lg @error('start_time') is-invalid @enderror" 
                                           id="start_time" 
                                           name="start_time" 
                                           value="{{ old('start_time') }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Durée --}}
                                <div class="col-md-4 mb-4">
                                    <label for="duration" class="form-label fw-semibold">
                                        <i class="bi bi-stopwatch me-1"></i>Durée (minutes) <span id="duration-required">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg @error('duration') is-invalid @enderror" 
                                           id="duration" 
                                           name="duration" 
                                           value="{{ old('duration') }}"
                                           min="1" 
                                           max="1440"
                                           placeholder="Ex: 30">
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Distance --}}
                                <div class="col-md-4 mb-4">
                                    <label for="distance" class="form-label fw-semibold">
                                        <i class="bi bi-geo me-1"></i>Distance (km)
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg @error('distance') is-invalid @enderror" 
                                           id="distance" 
                                           name="distance" 
                                           value="{{ old('distance') }}"
                                           min="0" 
                                           step="0.01"
                                           placeholder="Ex: 5.2">
                                    @error('distance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Calories --}}
                                <div class="col-md-4 mb-4">
                                    <label for="calories" class="form-label fw-semibold">
                                        <i class="bi bi-fire me-1"></i>Calories brûlées
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg @error('calories') is-invalid @enderror" 
                                           id="calories" 
                                           name="calories" 
                                           value="{{ old('calories') }}"
                                           min="0"
                                           placeholder="Auto calculé si vide">
                                    <small class="form-text text-muted">Laissez vide pour calcul automatique</small>
                                    @error('calories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Données supplémentaires --}}
                                <div class="col-12 mb-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="bi bi-plus-square me-1"></i>Données supplémentaires (optionnel)
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="heart_rate" class="form-label">
                                                        <i class="bi bi-heart me-1"></i>Fréquence cardiaque moyenne (bpm)
                                                    </label>
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="heart_rate" 
                                                           name="heart_rate" 
                                                           value="{{ old('heart_rate') }}"
                                                           min="40" 
                                                           max="220"
                                                           placeholder="Ex: 140">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="weather" class="form-label">
                                                        <i class="bi bi-cloud-sun me-1"></i>Conditions météo
                                                    </label>
                                                    <select class="form-select" id="weather" name="weather">
                                                        <option value="">Choisir...</option>
                                                        <option value="ensoleille" {{ old('weather') == 'ensoleille' ? 'selected' : '' }}>☀️ Ensoleillé</option>
                                                        <option value="nuageux" {{ old('weather') == 'nuageux' ? 'selected' : '' }}>☁️ Nuageux</option>
                                                        <option value="pluvieux" {{ old('weather') == 'pluvieux' ? 'selected' : '' }}>🌧️ Pluvieux</option>
                                                        <option value="venteux" {{ old('weather') == 'venteux' ? 'selected' : '' }}>💨 Venteux</option>
                                                        <option value="froid" {{ old('weather') == 'froid' ? 'selected' : '' }}>❄️ Froid</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="col-12 mb-4">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="bi bi-chat-left-text me-1"></i><span id="description-label">Description / Notes</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4"
                                              placeholder="Décrivez votre séance, vos sensations, le parcours...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Boutons d'action --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="{{ route('activities.index') }}" class="btn btn-light btn-lg">
                                        <i class="bi bi-x-circle me-1"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5" id="submit-btn">
                                        <i class="bi bi-check-circle me-1"></i>Enregistrer l'activité
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/libs/tom-select/tom-select.min.js') }}"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const intensitySelect = document.getElementById('intensity');
    const intensityDescription = document.getElementById('intensityDescription');
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const durationInput = document.getElementById('duration');
    const caloriesInput = document.getElementById('calories');
    const isRecurringCheckbox = document.getElementById('is_recurring');
    const recurringOptions = document.getElementById('recurring-options');
    const singleSessionInfo = document.getElementById('single-session-info');
    const sessionDetails = document.getElementById('session-details');
    const dateLabel = document.getElementById('date-label');
    const descriptionLabel = document.getElementById('description-label');
    const durationRequired = document.getElementById('duration-required');
    const submitBtn = document.getElementById('submit-btn');
    const activityDateInput = document.getElementById('activity_date');

    // Descriptions des intensités
    const intensityDescriptions = {
        'faible': 'Effort léger, respiration normale',
        'modere': 'Effort moyen, respiration légèrement accélérée', 
        'intense': 'Effort soutenu, respiration rapide'
    };
    
    // Gestion de l'affichage de la description d'intensité
    function updateIntensityDescription() {
        const selected = intensitySelect.value;
        if (selected && intensityDescriptions[selected]) {
            intensityDescription.textContent = intensityDescriptions[selected];
            intensityDescription.className = 'form-text text-info';
        } else {
            intensityDescription.textContent = '';
            intensityDescription.className = 'form-text text-muted';
        }
    }
    
    intensitySelect.addEventListener('change', updateIntensityDescription);
    
    // Initialiser avec la valeur sélectionnée
    if (intensitySelect.value) {
        updateIntensityDescription();
    }
    
    // Calcul automatique des calories
    function calculateCalories() {
        const selectedType = document.querySelector('input[name="type"]:checked');
        const duration = parseInt(durationInput.value);
        const intensity = intensitySelect.value;
        
        if (selectedType && duration && intensity && !caloriesInput.value) {
            const caloriesPerMinute = {
                'course': {'faible': 8, 'modere': 12, 'intense': 16},
                'marche': {'faible': 4, 'modere': 6, 'intense': 8},
                'velo': {'faible': 6, 'modere': 10, 'intense': 14},
                'fitness': {'faible': 5, 'modere': 8, 'intense': 12}
            };
            
            const rate = caloriesPerMinute[selectedType.value][intensity] || 8;
            const estimatedCalories = duration * rate;
            caloriesInput.placeholder = `≈ ${estimatedCalories} cal (auto-calculé)`;
            
            // Animation pour attirer l'attention
            caloriesInput.style.borderColor = '#28a745';
            setTimeout(() => {
                caloriesInput.style.borderColor = '';
            }, 2000);
        }
    }
    
    // Écouter les changements pour le calcul automatique
    typeInputs.forEach(input => input.addEventListener('change', calculateCalories));
    durationInput.addEventListener('input', calculateCalories);
    intensitySelect.addEventListener('change', calculateCalories);
    
    // Gestion des activités récurrentes
    function toggleRecurringMode() {
        if (isRecurringCheckbox.checked) {
            // Mode récurrent
            recurringOptions.style.display = 'block';
            singleSessionInfo.style.display = 'none';
            
            // Modifier les labels et comportements
            dateLabel.textContent = 'Date de la première session';
            descriptionLabel.textContent = 'Notes sur cette première session';
            durationRequired.textContent = ''; // Pas obligatoire pour activité récurrente
            submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Créer l\'activité récurrente';
            
            // Rendre la durée non obligatoire
            durationInput.removeAttribute('required');
            
        } else {
            // Mode simple
            recurringOptions.style.display = 'none';
            singleSessionInfo.style.display = 'block';
            
            // Restaurer les labels originaux
            dateLabel.textContent = 'Date de l\'activité';
            descriptionLabel.textContent = 'Description / Notes';
            durationRequired.textContent = '*';
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Enregistrer l\'activité';
            
            // Rendre la durée obligatoire
            durationInput.setAttribute('required', 'required');
        }
    }
    
    isRecurringCheckbox.addEventListener('change', toggleRecurringMode);
    
    // Initialiser l'affichage au chargement
    toggleRecurringMode();
    
    // Validation du formulaire avec SweetAlert2
    const form = document.getElementById('activityForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Empêcher la soumission par défaut
        
        const name = document.getElementById('name').value.trim();
        const type = document.querySelector('input[name="type"]:checked');
        const intensity = document.getElementById('intensity').value;
        const activityDate = document.getElementById('activity_date').value;
        
        // Validations communes
        if (!name || !type || !intensity || !activityDate) {
            Swal.fire({
                icon: 'error',
                title: '❌ Champs obligatoires manquants',
                html: '<p class="mb-2">Veuillez remplir tous les champs obligatoires (<span class="text-danger fw-bold">*</span>)</p><small class="text-muted">Assurez-vous que tous les champs requis sont complétés</small>',
                confirmButtonColor: '#dc3545',
                confirmButtonText: '✓ Compris',
                showClass: {
                    popup: 'animate__animated animate__shakeX'
                },
                customClass: {
                    popup: 'swal-wide'
                }
            });
            
            // Focus sur le premier champ manquant
            if (!name) document.getElementById('name').focus();
            else if (!type) document.querySelector('input[name="type"]').focus();
            else if (!intensity) document.getElementById('intensity').focus();
            else if (!activityDate) document.getElementById('activity_date').focus();
            
            return false;
        }
        
        // Validations spécifiques au mode récurrent
        if (isRecurringCheckbox.checked) {
            const targetSessions = document.getElementById('target_sessions_per_week').value;
            if (!targetSessions) {
                Swal.fire({
                    icon: 'warning',
                    title: '⚠️ Objectif manquant',
                    html: '<p class="mb-2">Veuillez définir un objectif de sessions par semaine pour l\'activité récurrente.</p><small class="text-muted">Cet objectif vous aidera à suivre vos progrès</small>',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: '✓ Compris',
                    showClass: {
                        popup: 'animate__animated animate__bounceIn'
                    }
                });
                document.getElementById('target_sessions_per_week').focus();
                return false;
            }
        } else {
            // Mode simple - valider la durée
            const duration = document.getElementById('duration').value;
            if (!duration || duration < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: '⏱️ Durée manquante',
                    html: '<p class="mb-2">Veuillez indiquer la durée de votre activité.</p><small class="text-muted">La durée est nécessaire pour calculer les calories brûlées</small>',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: '✓ Compris',
                    showClass: {
                        popup: 'animate__animated animate__bounceIn'
                    }
                });
                document.getElementById('duration').focus();
                return false;
            }
        }
        
        // Confirmation différente selon le mode avec SweetAlert2
        const isRecurring = isRecurringCheckbox.checked;
        const confirmTitle = isRecurring 
            ? '🔄 Créer l\'activité récurrente ?'
            : '✅ Confirmer l\'enregistrement ?';
            
        const confirmHtml = isRecurring 
            ? `<div class="text-center">
                <p class="mb-2">Créer l'activité récurrente <strong class="text-primary">"${name}"</strong> ?</p>
                <small class="text-muted">💡 Vous pourrez ensuite y ajouter des sessions individuelles avec leurs propres données.</small>
               </div>`
            : `<div class="text-center">
                <p class="mb-2">Confirmer l'enregistrement de <strong class="text-success">"${name}"</strong> ?</p>
                <small class="text-muted">📊 Cette session sera enregistrée dans votre historique sportif.</small>
               </div>`;
            
        const confirmIcon = isRecurring ? 'info' : 'question';
        const confirmColor = isRecurring ? '#0d6efd' : '#28a745';
        
        Swal.fire({
            title: confirmTitle,
            html: confirmHtml,
            icon: confirmIcon,
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: isRecurring ? 
                '<i class="bi bi-plus-circle me-1"></i>✓ Créer l\'activité' : 
                '<i class="bi bi-check-circle me-1"></i>✓ Enregistrer',
            cancelButtonText: '<i class="bi bi-x-circle me-1"></i>✕ Annuler',
            reverseButtons: true,
            buttonsStyling: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            focusConfirm: true,
            showClass: {
                popup: 'animate__animated animate__zoomIn animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__zoomOut animate__faster'
            },
            customClass: {
                popup: 'swal-wide'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Afficher un loader pendant l'enregistrement
                Swal.fire({
                    title: '💾 Enregistrement en cours...',
                    html: `
                        <div class="text-center">
                            <p class="mb-3">Veuillez patienter pendant que nous sauvegardons votre activité</p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Animation du bouton de soumission
                        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Enregistrement...';
                        submitBtn.disabled = true;
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeIn animate__faster'
                    }
                });
                
                // Soumettre le formulaire après un petit délai pour l'animation
                setTimeout(() => {
                    // Restaurer le formulaire à son état normal pour la soumission
                    form.removeEventListener('submit', arguments.callee);
                    form.submit();
                }, 1000);
            }
        });
    });
    
    // Animation d'apparition de la carte
    const card = document.querySelector('.card.shadow-sm');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0.0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200);
    }

    // Animation des éléments du formulaire
    const formElements = document.querySelectorAll('.form-control, .btn-check + label, .form-select');
    formElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        setTimeout(() => {
            element.style.transition = 'all 0.4s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 300 + (index * 50));
    });

    // Fonction pour gérer les clics sur les cartes d'activité (pour index.blade.php)
    window.handleActivityClick = function(activityId, isRecurring) {
        if (isRecurring) {
            // Si c'est une activité récurrente, rediriger vers l'ajout de session
            window.location.href = `/activities/${activityId}/sessions/create`;
        } else {
            // Si c'est une activité simple, rediriger vers les détails
            window.location.href = `/activities/${activityId}`;
        }
    };

    // Amélioration de l'expérience utilisateur
    // Auto-focus sur le champ nom au chargement
    setTimeout(() => {
        document.getElementById('name').focus();
    }, 800);

    // Validation en temps réel
    const nameInput = document.getElementById('name');
    nameInput.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
    });

    // Feedback visuel pour les types d'activité
    typeInputs.forEach(input => {
        input.addEventListener('change', function() {
            typeInputs.forEach(otherInput => {
                const label = otherInput.nextElementSibling;
                if (otherInput === this) {
                    label.style.transform = 'scale(1.05)';
                    label.style.boxShadow = '0 4px 15px rgba(0,123,255,0.3)';
                } else {
                    label.style.transform = 'scale(1)';
                    label.style.boxShadow = 'none';
                }
            });
        });
    });
});

// Style CSS personnalisé pour SweetAlert2
const style = document.createElement('style');
style.textContent = `
    .swal-wide {
        width: 32rem !important;
    }
    
    .swal2-popup {
        border-radius: 1rem !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2) !important;
    }
    
    .swal2-title {
        font-size: 1.5rem !important;
        font-weight: 600 !important;
    }
    
    .swal2-html-container {
        font-size: 1rem !important;
        line-height: 1.5 !important;
    }
    
    .swal2-confirm {
        font-weight: 600 !important;
        padding: 0.5rem 2rem !important;
        border-radius: 0.5rem !important;
    }
    
    .swal2-cancel {
        font-weight: 500 !important;
        padding: 0.5rem 2rem !important;
        border-radius: 0.5rem !important;
    }
`;
document.head.appendChild(style);
</script>
@endsection
<?php
?>
@extends('partials.layouts.master')

@section('title', 'Modifier l\'activité | SmartHealth')
@section('title-sub', 'Activités Sportives')
@section('pagetitle', 'Modifier une activité')

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
            <div class="card border-0 bg-warning-subtle mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-pencil-square text-white fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 text-warning">Modifier l'activité</h4>
                            <p class="text-muted mb-0">
                                Modifiez les détails de votre activité "{{ $activity->name }}"
                            </p>
                            <small class="text-warning">
                                <i class="bi bi-calendar3 me-1"></i>
                                Activité du {{ $activity->activity_date->format('d/m/Y') }}
                                @if($activity->start_time)
                                    à {{ $activity->start_time->format('H:i') }}
                                @endif
                            </small>
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

            {{-- Formulaire de modification --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-heart-pulse me-2 text-danger"></i>
                            Modifier les détails
                        </h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-eye"></i> Voir détails
                            </a>
                            <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('activities.update', $activity) }}" method="POST" id="activityEditForm">
                        @csrf
                        @method('PUT')
                        
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
                                       value="{{ old('name', $activity->name) }}"
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
                                               {{ (old('type', $activity->type) == $key) ? 'checked' : '' }}>
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
                                                {{ old('intensity', $activity->intensity) == $key ? 'selected' : '' }}
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

                            {{-- Date et heure --}}
                            <div class="col-md-6 mb-4">
                                <label for="activity_date" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-date me-1"></i>Date de l'activité *
                                </label>
                                <input type="date" 
                                       class="form-control form-control-lg @error('activity_date') is-invalid @enderror" 
                                       id="activity_date" 
                                       name="activity_date" 
                                       value="{{ old('activity_date', $activity->activity_date->format('Y-m-d')) }}"
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
                                       value="{{ old('start_time', $activity->start_time ? $activity->start_time->format('H:i') : '') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Durée --}}
                            <div class="col-md-4 mb-4">
                                <label for="duration" class="form-label fw-semibold">
                                    <i class="bi bi-stopwatch me-1"></i>Durée (minutes) *
                                </label>
                                <input type="number" 
                                       class="form-control form-control-lg @error('duration') is-invalid @enderror" 
                                       id="duration" 
                                       name="duration" 
                                       value="{{ old('duration', $activity->duration) }}"
                                       min="1" 
                                       max="1440"
                                       placeholder="Ex: 30"
                                       required>
                                <small class="form-text text-muted">
                                    Actuellement: {{ $activity->formatted_duration }}
                                </small>
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
                                       value="{{ old('distance', $activity->distance) }}"
                                       min="0" 
                                       step="0.01"
                                       placeholder="Ex: 5.2">
                                @if($activity->distance)
                                    <small class="form-text text-muted">
                                        Actuellement: {{ $activity->distance }} km
                                    </small>
                                @endif
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
                                       value="{{ old('calories', $activity->calories) }}"
                                       min="0"
                                       placeholder="Auto calculé si vide">
                                <small class="form-text text-muted">
                                    Actuellement: {{ $activity->calories ?? 0 }} cal
                                </small>
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
                                                       value="{{ old('heart_rate', $activity->additional_data['heart_rate'] ?? '') }}"
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
                                                    <option value="ensoleille" {{ old('weather', $activity->additional_data['weather'] ?? '') == 'ensoleille' ? 'selected' : '' }}>☀️ Ensoleillé</option>
                                                    <option value="nuageux" {{ old('weather', $activity->additional_data['weather'] ?? '') == 'nuageux' ? 'selected' : '' }}>☁️ Nuageux</option>
                                                    <option value="pluvieux" {{ old('weather', $activity->additional_data['weather'] ?? '') == 'pluvieux' ? 'selected' : '' }}>🌧️ Pluvieux</option>
                                                    <option value="venteux" {{ old('weather', $activity->additional_data['weather'] ?? '') == 'venteux' ? 'selected' : '' }}>💨 Venteux</option>
                                                    <option value="froid" {{ old('weather', $activity->additional_data['weather'] ?? '') == 'froid' ? 'selected' : '' }}>❄️ Froid</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="col-12 mb-4">
                                <label for="description" class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-text me-1"></i>Description / Notes
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Décrivez votre séance, vos sensations, le parcours...">{{ old('description', $activity->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Informations sur les modifications --}}
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Dernière modification :</strong> {{ $activity->updated_at->diffForHumans() }}
                            <br>
                            <small class="text-muted">
                                Créée le {{ $activity->created_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('activities.index') }}" class="btn btn-light btn-lg">
                                            <i class="bi bi-x-circle me-1"></i>Annuler
                                        </a>
                                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-info btn-lg">
                                            <i class="bi bi-eye me-1"></i>Voir détails
                                        </a>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-danger btn-lg" id="deleteBtn">
                                            <i class="bi bi-trash me-1"></i>Supprimer
                                        </button>
                                        <button type="submit" class="btn btn-warning btn-lg px-5" id="updateBtn">
                                            <i class="bi bi-check-circle me-1"></i>Enregistrer les modifications
                                        </button>
                                    </div>
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
    // Gestion de l'affichage de la description d'intensité
    const intensitySelect = document.getElementById('intensity');
    const intensityDescription = document.getElementById('intensityDescription');
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const durationInput = document.getElementById('duration');
    const caloriesInput = document.getElementById('calories');
    const updateBtn = document.getElementById('updateBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    
    const intensityDescriptions = {
        'faible': 'Effort léger, respiration normale',
        'modere': 'Effort moyen, respiration légèrement accélérée', 
        'intense': 'Effort soutenu, respiration rapide'
    };
    
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
    updateIntensityDescription();
    
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
            caloriesInput.style.borderColor = '#ffc107';
            setTimeout(() => {
                caloriesInput.style.borderColor = '';
            }, 2000);
        }
    }
    
    // Écouter les changements pour le calcul automatique
    typeInputs.forEach(input => {
        input.addEventListener('change', calculateCalories);
    });
    durationInput.addEventListener('input', calculateCalories);
    intensitySelect.addEventListener('change', calculateCalories);
    
    // Validation du formulaire avant soumission avec SweetAlert2
    const form = document.getElementById('activityEditForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Empêcher la soumission par défaut
        
        const name = document.getElementById('name').value.trim();
        const type = document.querySelector('input[name="type"]:checked');
        const duration = document.getElementById('duration').value;
        const intensity = document.getElementById('intensity').value;
        const activityDate = document.getElementById('activity_date').value;
        
        // Validation des champs obligatoires
        if (!name || !type || !duration || !intensity || !activityDate) {
            Swal.fire({
                icon: 'error',
                title: '❌ Champs obligatoires manquants',
                html: '<p class="mb-2">Veuillez remplir tous les champs obligatoires (<span class="text-danger fw-bold">*</span>)</p><small class="text-muted">Vérifiez que tous les champs requis sont complétés</small>',
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
            else if (!duration) document.getElementById('duration').focus();
            else if (!intensity) document.getElementById('intensity').focus();
            else if (!activityDate) document.getElementById('activity_date').focus();
            
            return false;
        }
        
        // Confirmation de modification avec SweetAlert2
        Swal.fire({
            title: '✏️ Confirmer les modifications ?',
            html: `
                <div class="text-center">
                    <p class="mb-2">Enregistrer les modifications de <strong class="text-warning">"${name}"</strong> ?</p>
                    <small class="text-muted">📝 Les nouvelles données remplaceront les informations actuelles.</small>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-check-circle me-1"></i>✓ Enregistrer',
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
                // Afficher un loader pendant la modification
                Swal.fire({
                    title: '💾 Modification en cours...',
                    html: `
                        <div class="text-center">
                            <p class="mb-3">Veuillez patienter pendant que nous sauvegardons vos modifications</p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Animation du bouton
                        updateBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Modification...';
                        updateBtn.disabled = true;
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeIn animate__faster'
                    }
                });
                
                // Soumettre le formulaire après un petit délai
                setTimeout(() => {
                    form.removeEventListener('submit', arguments.callee);
                    form.submit();
                }, 1000);
            }
        });
    });
    
    // Gestion de la suppression avec SweetAlert2
    deleteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const activityName = '{{ $activity->name }}';
        const activityDate = '{{ $activity->activity_date->format("d/m/Y") }}';
        
        Swal.fire({
            title: '🗑️ Confirmer la suppression',
            html: `
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="mb-2">Êtes-vous sûr de vouloir supprimer définitivement l'activité</p>
                    <p class="mb-3"><strong class="text-danger">"${activityName}"</strong><br>
                    <small class="text-muted">du ${activityDate}</small></p>
                    <div class="alert alert-warning border-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Attention :</strong> Cette action est <strong>irréversible</strong> !
                        <br><small>Toutes les données de cette activité seront perdues définitivement.</small>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-1"></i>🗑️ Supprimer définitivement',
            cancelButtonText: '<i class="bi bi-x-circle me-1"></i>✕ Annuler',
            reverseButtons: true,
            buttonsStyling: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            focusCancel: true,
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
                // Afficher un loader pendant la suppression
                Swal.fire({
                    title: '🗑️ Suppression en cours...',
                    html: `
                        <div class="text-center">
                            <p class="mb-3">Suppression de l'activité en cours...</p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Animation du bouton
                        deleteBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Suppression...';
                        deleteBtn.disabled = true;
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeIn animate__faster'
                    }
                });
                
                // Créer et soumettre le formulaire de suppression
                setTimeout(() => {
                    const deleteForm = document.createElement('form');
                    deleteForm.method = 'POST';
                    deleteForm.action = '{{ route("activities.destroy", $activity) }}';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    deleteForm.appendChild(csrfToken);
                    deleteForm.appendChild(methodField);
                    document.body.appendChild(deleteForm);
                    
                    deleteForm.submit();
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
        }, 100);
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
        }, 200 + (index * 30));
    });

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
                    label.style.boxShadow = '0 4px 15px rgba(255,193,7,0.3)';
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
    
    .progress {
        background-color: rgba(0,0,0,0.1) !important;
        border-radius: 10px !important;
    }
    
    .progress-bar {
        border-radius: 10px !important;
    }
`;
document.head.appendChild(style);
</script>
@endsection
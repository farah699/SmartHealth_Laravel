<?php
?>
@extends('partials.layouts.master')

@section('title', 'Ajouter une session | SmartHealth')
@section('title-sub', 'Activités Sportives')
@section('pagetitle', 'Ajouter une session')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
<div id="layout-wrapper">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            {{-- En-tête --}}
            <div class="card border-0 bg-success-subtle mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-plus-circle text-white fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 text-success">Nouvelle session</h4>
                            <p class="text-muted mb-0">
                                Ajouter une session pour "{{ $activity->name }}"
                            </p>
                            @if($activity->target_sessions_per_week)
                                <small class="text-success">
                                    <i class="bi bi-target me-1"></i>Objectif: {{ $activity->target_sessions_per_week }} sessions/semaine
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

      
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

            {{-- Formulaire --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-plus me-2 text-success"></i>
                            Détails de la session
                        </h5>
                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Retour à l'activité
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('activity-sessions.store', $activity) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            {{-- Date et heure --}}
                            <div class="col-md-6 mb-4">
                                <label for="session_date" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-date me-1"></i>Date de la session *
                                </label>
                                <input type="date" 
                                       class="form-control form-control-lg @error('session_date') is-invalid @enderror" 
                                       id="session_date" 
                                       name="session_date" 
                                       value="{{ old('session_date', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('session_date')
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
                                    <i class="bi bi-stopwatch me-1"></i>Durée (minutes) *
                                </label>
                                <input type="number" 
                                       class="form-control form-control-lg @error('duration') is-invalid @enderror" 
                                       id="duration" 
                                       name="duration" 
                                       value="{{ old('duration', $lastSession ? $lastSession->duration : '') }}"
                                       min="1" 
                                       max="1440"
                                       placeholder="Ex: 30"
                                       required>
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
                                       value="{{ old('distance', $lastSession ? $lastSession->distance : '') }}"
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
                                    @foreach($intensityLevels as $key => $level)
                                        <option value="{{ $key }}" 
                                                {{ old('intensity', $lastSession ? $lastSession->intensity : '') == $key ? 'selected' : '' }}>
                                            {{ $level['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('intensity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Difficulté --}}
                            <div class="col-md-6 mb-4">
                                <label for="difficulty" class="form-label fw-semibold">
                                    <i class="bi bi-emoji-neutral me-1"></i>Difficulté ressentie
                                </label>
                                <select class="form-select form-select-lg @error('difficulty') is-invalid @enderror" 
                                        id="difficulty" 
                                        name="difficulty">
                                    <option value="">Choisir la difficulté</option>
                                    @foreach($difficultyLevels as $key => $level)
                                        <option value="{{ $key }}" {{ old('difficulty') == $key ? 'selected' : '' }}>
                                            {{ $level['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('difficulty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Note de satisfaction --}}
                            <div class="col-md-6 mb-4">
                                <label for="rating" class="form-label fw-semibold">
                                    <i class="bi bi-star me-1"></i>Note de satisfaction (1-5)
                                </label>
                                <select class="form-select form-select-lg @error('rating') is-invalid @enderror" 
                                        id="rating" 
                                        name="rating">
                                    <option value="">Pas de note</option>
                                    <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>⭐ 1 - Très mauvais</option>
                                    <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>⭐⭐ 2 - Mauvais</option>
                                    <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ 3 - Moyen</option>
                                    <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4 - Bon</option>
                                    <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 - Excellent</option>
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Données supplémentaires --}}
                            <div class="col-md-6 mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="bi bi-plus-square me-1"></i>Données supplémentaires
                                        </h6>
                                        <div class="mb-3">
                                            <label for="heart_rate" class="form-label small">
                                                <i class="bi bi-heart me-1"></i>Fréquence cardiaque (bpm)
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
                                        <div class="mb-3">
                                            <label for="weather" class="form-label small">
                                                <i class="bi bi-cloud-sun me-1"></i>Météo
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
                                        <div>
                                            <label for="mood" class="form-label small">
                                                <i class="bi bi-emoji-smile me-1"></i>Humeur
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="mood" 
                                                   name="mood" 
                                                   value="{{ old('mood') }}"
                                                   placeholder="Ex: Motivé, Fatigué..."
                                                   maxlength="50">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="col-12 mb-4">
                                <label for="session_notes" class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-text me-1"></i>Notes de session
                                </label>
                                <textarea class="form-control @error('session_notes') is-invalid @enderror" 
                                          id="session_notes" 
                                          name="session_notes" 
                                          rows="4"
                                          placeholder="Comment s'est passée cette session ? Vos sensations, observations...">{{ old('session_notes') }}</textarea>
                                @error('session_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Boutons d'action --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-light btn-lg">
                                        <i class="bi bi-x-circle me-1"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="bi bi-check-circle me-1"></i>Enregistrer la session
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcul automatique des calories
    const durationInput = document.getElementById('duration');
    const caloriesInput = document.getElementById('calories');
    const intensitySelect = document.getElementById('intensity');
    
    function calculateCalories() {
        const duration = parseInt(durationInput.value);
        const intensity = intensitySelect.value;
        
        if (duration && intensity && !caloriesInput.value) {
            const caloriesPerMinute = {
                'course': {'faible': 8, 'modere': 12, 'intense': 16},
                'marche': {'faible': 4, 'modere': 6, 'intense': 8},
                'velo': {'faible': 6, 'modere': 10, 'intense': 14},
                'fitness': {'faible': 5, 'modere': 8, 'intense': 12}
            };
            
            const activityType = '{{ $activity->type }}';
            const rate = caloriesPerMinute[activityType][intensity] || 8;
            const estimatedCalories = duration * rate;
            caloriesInput.placeholder = `≈ ${estimatedCalories} cal (auto-calculé)`;
        }
    }
    
    durationInput.addEventListener('input', calculateCalories);
    intensitySelect.addEventListener('change', calculateCalories);
    
    // Animation d'apparition
    const card = document.querySelector('.card.shadow-sm');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    }
});
</script>
@endsection
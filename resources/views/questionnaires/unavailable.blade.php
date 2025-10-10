
@extends('partials.layouts.master')

@section('title', 'Questionnaires non disponibles | SmartHealth')
@section('title-sub', 'Évaluation')
@section('pagetitle', 'Non disponible')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <div class="card shadow-lg border-0">
            <div class="card-body text-center py-5">
                <i class="ri-calendar-close-line text-warning mb-4" style="font-size: 4rem;"></i>
                <h3 class="text-warning mb-3">Questionnaires non disponibles</h3>
                <p class="lead text-muted mb-4">
                    Les questionnaires psychologiques sont disponibles uniquement le lundi et vendredi.
                </p>
                
                <div class="alert alert-info border-0">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="ri-information-line me-3 fs-20"></i>
                        <div>
                            <strong>Prochaines disponibilités :</strong>
                            <br>
                            <small>Lundi et Vendredi de chaque semaine</small>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="/" class="btn btn-primary me-3">
                        <i class="ri-home-line me-2"></i>Retour à l'accueil
                    </a>
                    <a href="{{ route('questionnaires.history') }}" class="btn btn-outline-primary">
                        <i class="ri-history-line me-2"></i>Voir l'historique
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
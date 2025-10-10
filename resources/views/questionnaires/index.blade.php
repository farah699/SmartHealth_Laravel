
@extends('partials.layouts.master')

@section('title', 'Questionnaires Psychologiques | SmartHealth')
@section('title-sub', 'Évaluation')
@section('pagetitle', 'Questionnaires')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <!-- Header Card -->
        <div class="card shadow-lg border-0 mb-4">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-brain text-primary" style="font-size: 4rem;"></i>
                </div>
                <h2 class="card-title text-primary mb-3">Évaluation Psychologique</h2>
                <p class="lead text-muted mb-4">
                    Évaluez votre bien-être mental avec nos questionnaires cliniques validés scientifiquement
                </p>
                <div class="row text-center mb-4">
                    <div class="col-md-6">
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <h5 class="text-primary">PHQ-9</h5>
                            <small class="text-muted">Dépistage de la dépression</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <h5 class="text-success">GAD-7</h5>
                            <small class="text-muted">Évaluation de l'anxiété</small>
                        </div>
                    </div>
                </div>
                <button onclick="startAssessment()" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-play me-2"></i>Commencer l'évaluation
                </button>
            </div>
        </div>

        <!-- Information Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-clock text-info mb-3" style="font-size: 2rem;"></i>
                        <h6>Durée</h6>
                        <p class="text-muted small">5-10 minutes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt text-success mb-3" style="font-size: 2rem;"></i>
                        <h6>Confidentialité</h6>
                        <p class="text-muted small">100% confidentiel</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-certificate text-warning mb-3" style="font-size: 2rem;"></i>
                        <h6>Validé</h6>
                        <p class="text-muted small">Scientifiquement validé</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Info -->
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <i class="fas fa-calendar-alt me-3 text-info"></i>
                <div>
                    <h6 class="mb-1">Disponibilité</h6>
                    <small>Les questionnaires sont disponibles uniquement le lundi et vendredi</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function startAssessment() {
    Swal.fire({
        title: 'Commencer l\'évaluation',
        html: `
            <p>Vous allez répondre à deux questionnaires :</p>
            <div class="text-start">
                <p><strong>1. PHQ-9</strong> - Évaluation de la dépression (9 questions)</p>
                <p><strong>2. GAD-7</strong> - Évaluation de l'anxiété (7 questions)</p>
            </div>
            <p class="text-muted">Cette évaluation prend environ 5-10 minutes.</p>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Commencer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#0d6efd'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('questionnaires.start') }}";
        }
    });
}
</script>
@endsection
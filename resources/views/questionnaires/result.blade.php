
@extends('partials.layouts.master')

@section('title', 'Résultats de l\'évaluation | SmartHealth')
@section('title-sub', 'Évaluation')
@section('pagetitle', 'Résultats')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <!-- Success Animation -->
        <div class="text-center mb-4">
            <div class="success-checkmark">
                <div class="check-icon">
                    <span class="icon-line line-tip"></span>
                    <span class="icon-line line-long"></span>
                    <div class="icon-circle"></div>
                    <div class="icon-fix"></div>
                </div>
            </div>
            <h2 class="text-success mt-3">Évaluation terminée !</h2>
            <p class="text-muted">Voici vos résultats détaillés</p>
        </div>

        <!-- Overall Score Card -->
        <div class="card shadow-lg border-0 mb-4">
            <div class="card-header bg-gradient text-white" 
                 style="background: linear-gradient(135deg, #6f42c1, #007bff);">
                <h5 class="mb-0 text-white">
                    <i class="ri-trophy-line me-2"></i>Scores Obtenus
                </h5>
            </div>
            <div class="card-body text-center py-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="score-circle phq9-score">
                            <span class="score-number">{{ $session->phq9_score }}</span>
                            <small>PHQ-9</small>
                        </div>
                        <h6 class="mt-3 text-primary">{{ $session->phq9_interpretation }}</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="score-circle gad7-score">
                            <span class="score-number">{{ $session->gad7_score }}</span>
                            <small>GAD-7</small>
                        </div>
                        <h6 class="mt-3 text-success">{{ $session->gad7_interpretation }}</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="score-circle total-score">
                            <span class="score-number">{{ $session->getTotalScore() }}</span>
                            <small>Total</small>
                        </div>
                        <h6 class="mt-3 text-warning">{{ $session->getOverallInterpretation() }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Results -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="ri-heart-line me-2"></i>Dépression (PHQ-9)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Score obtenu</span>
                            <span class="badge bg-primary">{{ $session->phq9_score }}/27</span>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ ($session->phq9_score / 27) * 100 }}%"></div>
                        </div>
                        <p class="small text-muted mb-0">{{ $session->phq9_interpretation }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="ri-brain-line me-2"></i>Anxiété (GAD-7)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Score obtenu</span>
                            <span class="badge bg-success">{{ $session->gad7_score }}/21</span>
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ ($session->gad7_score / 21) * 100 }}%"></div>
                        </div>
                        <p class="small text-muted mb-0">{{ $session->gad7_interpretation }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ri-settings-2-line me-2"></i>Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <button onclick="viewHistory()" class="btn btn-outline-primary w-100">
                            <i class="ri-history-line me-2"></i>Voir l'historique
                        </button>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('questionnaires.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="ri-refresh-line me-2"></i>Nouvelle évaluation
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="/" class="btn btn-primary w-100">
                            <i class="ri-home-line me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notice -->
        <div class="alert alert-warning border-0 shadow-sm mt-4">
            <div class="d-flex align-items-start">
                <i class="ri-alert-line text-warning me-3 mt-1 fs-20"></i>
                <div>
                    <h6>Important</h6>
                    <p class="mb-0 small">
                        Ces résultats sont des indicateurs scientifiques de votre état psychologique. 
                        Ils ne constituent pas un diagnostic médical. Si vos scores indiquent des préoccupations, 
                        nous vous recommandons de consulter un professionnel de la santé mentale.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function viewHistory() {
    window.location.href = "{{ route('questionnaires.history') }}";
}

// Animation des scores au chargement
document.addEventListener('DOMContentLoaded', function() {
    const scoreNumbers = document.querySelectorAll('.score-number');
    
    scoreNumbers.forEach((element, index) => {
        const finalScore = parseInt(element.textContent);
        let currentScore = 0;
        element.textContent = '0';
        
        const interval = setInterval(() => {
            if (currentScore < finalScore) {
                currentScore++;
                element.textContent = currentScore;
            } else {
                clearInterval(interval);
            }
        }, 30 + (index * 10));
    });
});
</script>

<style>
.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.success-checkmark .check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50px;
    box-sizing: content-box;
    border: 4px solid #4CAF50;
}

.success-checkmark .check-icon::before {
    top: 3px;
    left: -2px;
    width: 30px;
    transform-origin: 100% 50%;
    border-radius: 100px 0 0 100px;
}

.success-checkmark .check-icon::after {
    top: 0;
    left: 30px;
    width: 60px;
    transform-origin: 0 50%;
    border-radius: 0 100px 100px 0;
    animation: rotate-circle 4.25s ease-in;
}

.success-checkmark .check-icon::before, .success-checkmark .check-icon::after {
    content: '';
    height: 100px;
    position: absolute;
    background-color: #FFFFFF;
    transform: rotate(-45deg);
}

.success-checkmark .check-icon .icon-line {
    height: 5px;
    background-color: #4CAF50;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.success-checkmark .check-icon .icon-line.line-tip {
    top: 46px;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: icon-line-tip 0.75s;
}

.success-checkmark .check-icon .icon-line.line-long {
    top: 38px;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: icon-line-long 0.75s;
}

.score-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: 3px solid #dee2e6;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.phq9-score {
    border-color: #0d6efd;
    background: linear-gradient(135deg, #cfe2ff, #b6d7ff);
}

.gad7-score {
    border-color: #28a745;
    background: linear-gradient(135deg, #d1e7dd, #badbcc);
}

.total-score {
    border-color: #ffc107;
    background: linear-gradient(135deg, #fff3cd, #ffecb5);
}

.score-number {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1;
}

@keyframes icon-line-tip {
    0% { width: 0; left: 1px; top: 19px; }
    54% { width: 0; left: 1px; top: 19px; }
    70% { width: 50px; left: -8px; top: 37px; }
    84% { width: 17px; left: 21px; top: 48px; }
    100% { width: 25px; left: 14px; top: 45px; }
}

@keyframes icon-line-long {
    0% { width: 0; right: 46px; top: 54px; }
    65% { width: 0; right: 46px; top: 54px; }
    84% { width: 55px; right: 0px; top: 35px; }
    100% { width: 47px; right: 8px; top: 38px; }
}
</style>
@endsection
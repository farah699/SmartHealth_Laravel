
@extends('partials.layouts.master')

@section('title', 'Historique des évaluations | SmartHealth')
@section('title-sub', 'Évaluation')
@section('pagetitle', 'Historique')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1">
                            <i class="ri-history-line text-primary me-2"></i>Historique des évaluations
                        </h4>
                        <p class="text-muted mb-0">Suivez l'évolution de votre bien-être mental</p>
                    </div>
                    <a href="{{ route('questionnaires.index') }}" class="btn btn-primary">
                        <i class="ri-add-line me-2"></i>Nouvelle évaluation
                    </a>
                </div>
            </div>
        </div>

        @if($sessions->count() > 0)
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="ri-line-chart-line text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5 class="card-title">{{ $sessions->count() }}</h5>
                            <p class="text-muted small mb-0">Évaluations complètes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="ri-heart-line text-danger mb-2" style="font-size: 2rem;"></i>
                            <h5 class="card-title">{{ number_format($sessions->avg('phq9_score'), 1) }}</h5>
                            <p class="text-muted small mb-0">Score PHQ-9 moyen</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="ri-brain-line text-success mb-2" style="font-size: 2rem;"></i>
                            <h5 class="card-title">{{ number_format($sessions->avg('gad7_score'), 1) }}</h5>
                            <p class="text-muted small mb-0">Score GAD-7 moyen</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="ri-calendar-line text-info mb-2" style="font-size: 2rem;"></i>
                            <h5 class="card-title">{{ $sessions->first()->completed_at->diffForHumans() }}</h5>
                            <p class="text-muted small mb-0">Dernière évaluation</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Timeline -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white" 
                     style="background: linear-gradient(135deg, #6f42c1, #007bff);">
                    <h6 class="mb-0 text-white">
                        <i class="ri-file-list-3-line me-2"></i>Historique détaillé
                    </h6>
                </div>
                <div class="card-body p-0">
                    @foreach($sessions as $session)
                        <div class="timeline-item p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="timeline-date">
                                            <i class="ri-calendar-2-line text-primary mb-2 fs-24"></i>
                                            <div class="fw-bold">{{ $session->completed_at->format('d M') }}</div>
                                            <small class="text-muted">{{ $session->completed_at->format('Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="score-badge phq9">
                                                <div class="score-value">{{ $session->phq9_score }}</div>
                                                <div class="score-label">PHQ-9</div>
                                                <div class="score-interpretation">{{ $session->phq9_interpretation }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="score-badge gad7">
                                                <div class="score-value">{{ $session->gad7_score }}</div>
                                                <div class="score-label">GAD-7</div>
                                                <div class="score-interpretation">{{ $session->gad7_interpretation }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="score-badge total">
                                                <div class="score-value">{{ $session->getTotalScore() }}</div>
                                                <div class="score-label">Total</div>
                                                <div class="score-interpretation">{{ $session->getOverallInterpretation() }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <small class="text-muted d-block">
                                                <i class="ri-time-line me-1"></i>{{ $session->completed_at->format('H:i') }}
                                            </small>
                                            <small class="text-muted d-block">
                                                {{ $session->completed_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-line-chart-line text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h5 class="text-muted">Aucune évaluation effectuée</h5>
                    <p class="text-muted mb-4">Commencez votre première évaluation pour suivre votre bien-être mental</p>
                    <a href="{{ route('questionnaires.index') }}" class="btn btn-primary">
                        <i class="ri-play-line me-2"></i>Commencer maintenant
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes de statistiques
    const statCards = document.querySelectorAll('.card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
.timeline-item {
    transition: background-color 0.2s ease;
}

.timeline-item:hover {
    background-color: #f8f9fa;
}

.score-badge {
    text-align: center;
    padding: 1.2rem;
    border-radius: 12px;
    border: 2px solid;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.score-badge.phq9 {
    border-color: #0d6efd;
    background: linear-gradient(135deg, #cfe2ff, #b6d7ff);
}

.score-badge.gad7 {
    border-color: #28a745;
    background: linear-gradient(135deg, #d1e7dd, #badbcc);
}

.score-badge.total {
    border-color: #ffc107;
    background: linear-gradient(135deg, #fff3cd, #ffecb5);
}

.score-value {
    font-size: 1.8rem;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.score-label {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0.25rem 0;
}

.score-interpretation {
    font-size: 0.75rem;
    color: #6c757d;
    line-height: 1.2;
}

.timeline-date {
    position: relative;
}
</style>
@endsection
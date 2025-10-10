
@extends('partials.layouts.master')

@section('title', 'Questionnaire ' . $type . ' | SmartHealth')
@section('title-sub', 'Évaluation')
@section('pagetitle', 'Questionnaire ' . $type)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <!-- Progress Bar -->
        <div class="card shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Progression</h6>
                    <span class="badge bg-primary">{{ $type === 'PHQ-9' ? '1/2' : '2/2' }}</span>
                </div>
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ $type === 'PHQ-9' ? '50' : '100' }}%"></div>
                </div>
            </div>
        </div>

        <!-- Main Questionnaire Card -->
        <div class="card shadow-lg border-0">
            <div class="card-header border-0 bg-gradient text-white" 
                 style="background: linear-gradient(135deg, {{ $type === 'PHQ-9' ? '#6f42c1, #007bff' : '#28a745, #20c997' }});">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="ri-{{ $type === 'PHQ-9' ? 'heart' : 'brain' }}-line" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-1">Questionnaire {{ $type }}</h4>
                        <p class="mb-0 opacity-75">
                            {{ $type === 'PHQ-9' ? 'Évaluation de la dépression' : 'Évaluation de l\'anxiété' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="alert alert-light border-start border-5 border-info">
                    <div class="d-flex align-items-center">
                        <i class="ri-information-line text-info me-3 fs-20"></i>
                        <div>
                            <strong>Instructions :</strong>
                            <p class="mb-0 small">Au cours des 2 dernières semaines, à quelle fréquence avez-vous été dérangé(e) par les problèmes suivants ?</p>
                        </div>
                    </div>
                </div>

                <form id="questionnaireForm" action="{{ route('questionnaires.store', $type) }}" method="POST">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $session->id }}">
                    
                    @foreach ($questions as $index => $question)
                        <div class="card border-0 shadow-sm mb-4 question-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="badge bg-primary rounded-circle me-3 mt-1 d-flex align-items-center justify-content-center" 
                                         style="width: 35px; height: 35px; font-size: 14px;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-3 fw-semibold">{{ $question['question'] }}</h6>
                                        <div class="options-container">
                                            @foreach ($question['options'] as $option)
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="radio" 
                                                           name="answers[{{ $question['id'] }}]" 
                                                           value="{{ $option['value'] }}" 
                                                           id="q{{ $question['id'] }}_{{ $option['value'] }}" 
                                                           required>
                                                    <label class="form-check-label d-flex align-items-center" 
                                                           for="q{{ $question['id'] }}_{{ $option['value'] }}">
                                                        <span class="badge badge-outline me-2">{{ $option['value'] }}</span>
                                                        <span>{{ $option['label'] }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                            @if($type === 'PHQ-9')
                                <i class="ri-arrow-right-line me-2"></i>Continuer vers GAD-7
                            @else
                                <i class="ri-check-line me-2"></i>Terminer l'évaluation
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('questionnaireForm');
    const submitBtn = document.getElementById('submitBtn');
    const questions = @json($questions);
    
    // Animation des cartes au chargement
    const cards = document.querySelectorAll('.question-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Validation en temps réel
    form.addEventListener('change', function() {
        let allAnswered = true;
        questions.forEach(question => {
            const radios = document.getElementsByName(`answers[${question.id}]`);
            const isAnswered = Array.from(radios).some(radio => radio.checked);
            if (!isAnswered) {
                allAnswered = false;
            }
        });
        
        submitBtn.disabled = !allAnswered;
        if (allAnswered) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Vérifier que toutes les questions sont répondues
        let allAnswered = true;
        questions.forEach(question => {
            const radios = document.getElementsByName(`answers[${question.id}]`);
            const isAnswered = Array.from(radios).some(radio => radio.checked);
            if (!isAnswered) {
                allAnswered = false;
            }
        });
        
        if (!allAnswered) {
            Swal.fire({
                icon: 'warning',
                title: 'Questions manquantes',
                text: 'Veuillez répondre à toutes les questions avant de continuer.',
                confirmButtonColor: '#0d6efd'
            });
            return;
        }

        // Animation de soumission
        Swal.fire({
            title: 'Enregistrement...',
            html: 'Veuillez patienter',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Soumettre le formulaire
        form.submit();
    });
});
</script>

<style>
.badge-outline {
    border: 1px solid #dee2e6;
    background: transparent;
    color: #6c757d;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.form-check-input:checked + .form-check-label .badge-outline {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.question-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 10px;
}

.question-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.form-check-label {
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.form-check-label:hover {
    background-color: #f8f9fa;
}

.form-check-input:checked + .form-check-label {
    background-color: #e3f2fd;
}
</style>
@endsection
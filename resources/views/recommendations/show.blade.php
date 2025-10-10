@extends('partials.layouts.master')

@section('title', 'Détail Recommandation | SmartHealth')
@section('title-sub', 'Recommandations')
@section('pagetitle', 'Détail de la Recommandation')

@section('css')
<style>
    .recommendation-detail-card {
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .recommendation-header {
        background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
        color: white;
        padding: 30px;
        position: relative;
    }
    
    .recommendation-body {
        padding: 30px;
    }
    
    .badge-custom {
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.5em 1em;
        border-radius: 1rem;
        margin-right: 10px;
        margin-bottom: 10px;
        display: inline-block;
    }
    
    .badge-category {
        background-color: #E9D5FF;
        color: #7E22CE;
    }
    
    .badge-type {
        background-color: #BFDBFE;
        color: #2563EB;
    }
    
    .badge-difficulty {
        background-color: #FED7AA;
        color: #EA580C;
    }
    
    .badge-time {
        background-color: #BBF7D0;
        color: #16A34A;
    }
    
    .badge-audience {
        background-color: #FEE2E2;
        color: #DC2626;
    }
    
    .section-heading {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #4B5563;
    }
    
    .recommendation-link {
        display: inline-block;
        padding: 12px 24px;
        background: #6366F1;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 500;
    }
    
    .recommendation-link:hover {
        background: #4F46E5;
        color: white;
        transform: translateY(-2px);
    }
    
    .blog-details {
        background: #F9FAFB;
        border-radius: 12px;
        padding: 20px;
        margin-top: 30px;
    }
    
    .blog-link {
        color: #4F46E5;
        text-decoration: none;
    }
    
    .blog-link:hover {
        text-decoration: underline;
    }
    
    .email-sent-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card recommendation-detail-card">
                <div class="recommendation-header">
                    <h4 class="mb-0">{{ $recommendation->title }}</h4>
                    
                    @if($recommendation->email_sent)
                    <div class="email-sent-badge">
                        <i class="ri-mail-check-line me-1"></i> Email envoyé le {{ $recommendation->email_sent_at->format('d/m/Y') }}
                    </div>
                    @endif
                </div>
                
                <div class="recommendation-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap mb-4">
                                @if($recommendation->category)
                                <span class="badge-custom badge-category">
                                    <i class="ri-folder-line me-1"></i> {{ $recommendation->category }}
                                </span>
                                @endif
                                
                                @if($recommendation->content_type)
                                <span class="badge-custom badge-type">
                                    <i class="ri-file-list-line me-1"></i> {{ $recommendation->content_type }}
                                </span>
                                @endif
                                
                                @if($recommendation->difficulty_level && $recommendation->difficulty_level !== 'N/A')
                                <span class="badge-custom badge-difficulty">
                                    <i class="ri-bar-chart-line me-1"></i> Niveau: {{ $recommendation->difficulty_level }}
                                </span>
                                @endif
                                
                                @if($recommendation->estimated_time && $recommendation->estimated_time !== 'N/A')
                                <span class="badge-custom badge-time">
                                    <i class="ri-time-line me-1"></i> {{ $recommendation->estimated_time }}
                                </span>
                                @endif
                                
                                @if($recommendation->target_audience && $recommendation->target_audience !== 'Tous')
                                <span class="badge-custom badge-audience">
                                    <i class="ri-user-line me-1"></i> {{ $recommendation->target_audience }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="section-heading">Description</h5>
                            <p class="mb-4">{{ $recommendation->description }}</p>
                            
                            @if($recommendation->url)
                            <div class="text-center mt-4 mb-4">
                                <a href="{{ $recommendation->url }}" class="recommendation-link" target="_blank">
                                    <i class="ri-external-link-line me-2"></i> Accéder à la ressource
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="blog-details">
                                <h5 class="section-heading">Recommandé pour l'article</h5>
                                <p class="mb-2">
                                    <a href="{{ route('blogs.show', $recommendation->blog->id) }}" class="blog-link">
                                        <strong>{{ $recommendation->blog->title }}</strong>
                                    </a>
                                </p>
                                <p class="mb-1">
                                    <strong>Auteur:</strong> {{ $recommendation->blog->user->name }}
                                </p>
                                <p class="mb-1">
                                    <strong>Publié le:</strong> {{ $recommendation->blog->created_at->format('d/m/Y') }}
                                </p>
                                <p class="mb-0">
                                    <strong>Catégorie:</strong> {{ $recommendation->blog->category }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('recommendations.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Retour aux recommandations
                                </a>
                                
                              

                                @if($recommendation->blog->user_id === Auth::id() || (Auth::check() && Auth::user()->role === 'Admin'))
<form action="{{ route('recommendations.destroy', $recommendation->id) }}" method="POST" class="d-inline" id="deleteRecommendationForm">
    @csrf
    @method('DELETE')
    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
        <i class="ri-delete-bin-line me-1"></i> Supprimer
    </button>
</form>
@endif
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action supprimera définitivement cette recommandation !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Oui, supprimer',
            cancelButtonText: 'Annuler',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteRecommendationForm').submit();
            }
        });
    }
</script>
@endsection
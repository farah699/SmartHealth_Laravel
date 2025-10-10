@extends('partials.layouts.master')

@section('title', 'Mes Recommandations | SmartHealth')
@section('title-sub', 'Recommandations')
@section('pagetitle', 'Mes Recommandations')

@section('css')
<style>
    .recommendation-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        height: 100%;
    }
    
    .recommendation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .recommendation-header {
        background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
        color: white;
        padding: 20px;
        position: relative;
    }
    
    .recommendation-body {
        padding: 20px;
    }
    
    .recommendation-blog-info {
        border-top: 1px solid #e9ecef;
        padding-top: 15px;
        margin-top: 15px;
    }
    
    .badge-custom {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
        border-radius: 0.5rem;
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
    
    .recommendation-link {
        display: inline-block;
        padding: 8px 16px;
        background: #6366F1;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s;
        margin-top: 10px;
    }
    
    .recommendation-link:hover {
        background: #4F46E5;
        color: white;
    }
    
    .recommendation-title {
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .recommendation-description {
        color: #6B7280;
        margin-bottom: 15px;
    }
    
    .blog-title-link {
        color: #4F46E5;
        text-decoration: none;
        font-weight: 500;
    }
    
    .blog-title-link:hover {
        text-decoration: underline;
    }
    
    .empty-state {
        text-align: center;
        padding: 50px 20px;
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: #D1D5DB;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Mes recommandations</h5>
    
    @if(Auth::user()->role === 'Admin')
    <div>
        <a href="{{ route('recommendations.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="ri-list-check-line me-1"></i> Toutes les recommandations
        </a>
    </div>
    @endif
</div>
                
                <div class="card-body">
                    @if($blogsWithRecommendations->count() > 0)
                    <div class="row g-4">
                        @foreach($blogsWithRecommendations as $blog)
                            <div class="col-12 col-md-6 col-xl-4">
    <div class="card recommendation-card shadow-sm">
        <div class="recommendation-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">{{ $blog->recommendation->title }}</h5>
                
                @if($blog->recommendation->is_new)
                    <form action="{{ route('recommendations.markAsRead', $blog->recommendation->id) }}" method="POST">
                        @csrf
                        <span class="badge bg-success position-absolute top-0 end-0 m-2" title="Nouvelle recommandation">
                            Nouveau
                            <button type="submit" class="btn btn-sm p-0 ms-1 text-white" title="Marquer comme lu">
                                <i class="ri-check-line"></i>
                            </button>
                        </span>
                    </form>
                @endif
            </div>
        </div>
                                    
                                    <div class="recommendation-body">
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @if($blog->recommendation->category)
                                            <span class="badge badge-custom badge-category">
                                                {{ $blog->recommendation->category }}
                                            </span>
                                            @endif
                                            
                                            @if($blog->recommendation->content_type)
                                            <span class="badge badge-custom badge-type">
                                                {{ $blog->recommendation->content_type }}
                                            </span>
                                            @endif
                                            
                                            @if($blog->recommendation->difficulty_level && $blog->recommendation->difficulty_level !== 'N/A')
                                            <span class="badge badge-custom badge-difficulty">
                                                Niveau: {{ $blog->recommendation->difficulty_level }}
                                            </span>
                                            @endif
                                            
                                            @if($blog->recommendation->estimated_time && $blog->recommendation->estimated_time !== 'N/A')
                                            <span class="badge badge-custom badge-time">
                                                {{ $blog->recommendation->estimated_time }}
                                            </span>
                                            @endif
                                        </div>
                                        
                                        <p class="recommendation-description">
                                            {{ Str::limit($blog->recommendation->description, 120) }}
                                        </p>
                                        
                                        @if($blog->recommendation->url)
                                        <a href="{{ $blog->recommendation->url }}" class="recommendation-link" target="_blank">
                                            <i class="ri-external-link-line me-1"></i> Voir la ressource
                                        </a>
                                        @endif
                                        
                                        <div class="recommendation-blog-info">
                                            <p class="mb-1"><strong>Recommandé pour votre article :</strong></p>
                                            <p class="mb-1">
                                                <a href="{{ route('blogs.show', $blog->id) }}" class="blog-title-link">
                                                    {{ Str::limit($blog->title, 50) }}
                                                </a>
                                            </p>
                                            <small class="text-muted">
                                                Publié le {{ $blog->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $blogsWithRecommendations->links() }}
                    </div>
                    
                    @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="ri-bookmark-line"></i>
                        </div>
                        <h4>Vous n'avez pas encore de recommandations</h4>
                        <p class="text-muted">
                            Les recommandations sont générées automatiquement pour vos articles publiés.
                            <br>Publiez un nouvel article pour recevoir des recommandations personnalisées.
                        </p>
                        <a href="{{ route('blogs.create') }}" class="btn btn-primary mt-3">
                            <i class="ri-add-line me-1"></i> Créer un nouvel article
                        </a>
                    </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection
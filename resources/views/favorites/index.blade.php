@extends('partials.layouts.master')

@section('title', 'Mes Favoris | SmartHealth')
@section('title-sub', 'Favoris')
@section('pagetitle', 'Mes Articles Sauvegardés')

@section('css')
<style>
.favorite-tabs .nav-link {
    border-radius: 25px;
    padding: 8px 20px;
    margin-right: 8px;
    border: 1px solid #e3e6f0;
    color: #6c757d;
    transition: all 0.3s ease;
}

.favorite-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
}

.stats-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    transition: transform 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.favorite-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.read-status {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.read-status.unread {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
}

.read-status.read {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}
</style>
@endsection

@section('content')
<div id="layout-wrapper">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec statistiques -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3 class="text-primary mb-1">{{ $stats['favorites_count'] }}</h3>
                        <p class="text-muted mb-0">Favoris</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3 class="text-warning mb-1">{{ $stats['read_later_count'] }}</h3>
                        <p class="text-muted mb-0">À lire plus tard</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3 class="text-info mb-1">{{ $stats['unread_count'] }}</h3>
                        <p class="text-muted mb-0">Non lus</p>
                    </div>
                </div>
            </div>

            <!-- Onglets -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <ul class="nav nav-pills favorite-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'favorites' ? 'active' : '' }}" 
                                   href="{{ route('favorites.index', ['tab' => 'favorites']) }}">
                                    <i class="ri-heart-line me-2"></i>Favoris
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === 'read_later' ? 'active' : '' }}" 
                                   href="{{ route('favorites.index', ['tab' => 'read_later']) }}">
                                    <i class="ri-bookmark-line me-2"></i>Lire plus tard
                                </a>
                            </li>
                        </ul>
                        
                        <a href="{{ route('blogs.index') }}" class="btn btn-outline-primary">
                            <i class="ri-add-line me-1"></i>Découvrir plus
                        </a>
                    </div>

                    <!-- Liste des blogs -->
                    @if($blogs->count() > 0)
                        <div class="row gx-4 gy-4">
                            @foreach($blogs as $blog)
                                <div class="col-lg-4 col-md-6">
                                    <div class="card h-100 position-relative">
                                        <!-- Badge de statut pour "lire plus tard" -->
                                        @if($tab === 'read_later')
                                            <div class="read-status {{ $blog->pivot->read_at ? 'read' : 'unread' }}">
                                                {{ $blog->pivot->read_at ? 'Lu' : 'Non lu' }}
                                            </div>
                                        @endif

                                        <!-- Badge du type de favori -->
                                        <div class="favorite-badge">
                                            @if($tab === 'favorites')
                                                <i class="ri-heart-fill text-danger"></i>
                                            @else
                                                <i class="ri-bookmark-fill text-warning"></i>
                                            @endif
                                        </div>

                                        <!-- Image -->
                                        <div style="height: 160px; overflow: hidden;">
                                            @if($blog->image_url)
                                                <img src="{{ asset('storage/' . $blog->image_url) }}" 
                                                     class="card-img-top w-100 h-100" 
                                                     style="object-fit: cover;" 
                                                     alt="{{ $blog->title }}">
                                            @else
                                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                                    <i class="ri-image-line fs-36 text-muted"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="card-body d-flex flex-column">
                                            <div class="mb-2">
                                                <span class="badge bg-primary-subtle text-primary">{{ $blog->category }}</span>
                                                <small class="text-muted ms-2">{{ $blog->pivot->created_at->format('d M Y') }}</small>
                                            </div>
                                            
                                            <h6 class="card-title">
                                                <a href="{{ route('blogs.show', $blog->id) }}" class="text-decoration-none">
                                                    {{ \Illuminate\Support\Str::limit($blog->title, 50) }}
                                                </a>
                                            </h6>
                                            
                                            <p class="card-text text-muted flex-grow-1">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($blog->content), 80) }}
                                            </p>

                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <small class="text-muted">Par {{ $blog->user->name }}</small>
                                                
                                                <div class="btn-group">
                                                    @if($tab === 'read_later' && !$blog->pivot->read_at)
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="markAsRead({{ $blog->id }})">
                                                            <i class="ri-check-line"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <a href="{{ route('blogs.show', $blog->id) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        Lire
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $blogs->appends(['tab' => $tab])->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            @if($tab === 'favorites')
                                <i class="ri-heart-line fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun favori pour le moment</h5>
                                <p class="text-muted">Explorez nos articles et ajoutez-les à vos favoris</p>
                            @else
                                <i class="ri-bookmark-line fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun article à lire</h5>
                                <p class="text-muted">Sauvegardez des articles pour les lire plus tard</p>
                            @endif
                            
                            <a href="{{ route('blogs.index') }}" class="btn btn-primary">
                                <i class="ri-search-line me-1"></i>Découvrir des articles
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
<script>
// Marquer comme lu
function markAsRead(blogId) {
    fetch(`/blogs/${blogId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger pour mettre à jour le statut
        }
    })
    .catch(error => console.error('Erreur:', error));
}
</script>
@endsection
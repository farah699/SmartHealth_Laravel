@extends('partials.layouts.master')

@section('title', $blog->title . ' | SmartHealth')

@section('title-sub', 'Blog')
@section('pagetitle', 'Détails de l\'Article')

@section('css')
<style>
.comment-item {
    transition: background-color 0.2s ease;
}
.comment-item:hover {
    background-color: #f8f9fa;
}
.like-btn, .dislike-btn {
    border: none;
    background: transparent;
    color: #6c757d;
    transition: all 0.2s ease;
    padding: 4px 8px;
    border-radius: 4px;
}
.like-btn:hover {
    background-color: #e8f5e8;
    color: #28a745;
}
.dislike-btn:hover {
    background-color: #fdeaea;
    color: #dc3545;
}
.like-btn.active {
    background-color: #28a745;
    color: white;
}
.dislike-btn.active {
    background-color: #dc3545;
    color: white;
}
.audio-alert-popup {
    border-radius: 16px !important;
    border: 1px solid #e3e6f0 !important;
}

.audio-confirm-btn {
    border-radius: 8px !important;
    font-weight: 500 !important;
    padding: 8px 16px !important;
}

.audio-cancel-btn {
    border-radius: 8px !important;
    font-weight: 500 !important;
    padding: 8px 16px !important;
}

.generating-alert-popup {
    border-radius: 16px !important;
    border: 1px solid #f6ad55 !important;
    background: linear-gradient(135deg, #fef5e7 0%, #fed7aa 100%) !important;
}
.success-alert-popup {
    border-radius: 16px !important;
    border: 1px solid #68d391 !important;
    background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%) !important;
}

.error-alert-popup {
    border-radius: 16px !important;
    border: 1px solid #f56565 !important;
    background: linear-gradient(135deg, #fed7d7 0%, #fc8181 100%) !important;
}

/* Animation pour le spinner dans SweetAlert */
.swal2-html-container .spinner-border {
    animation: spinner-border 0.75s linear infinite;
}
.audio-player {
    background: #ffffff;
    border: 1px solid #e3e6f0;
    border-radius: 16px;
    padding: 24px;
    margin: 24px 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.audio-player:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.audio-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.audio-title {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #2d3748;
    font-weight: 600;
    font-size: 16px;
    margin: 0;
}

.audio-icon {
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.audio-badges {
    display: flex;
    gap: 8px;
    align-items: center;
}

.duration-badge {
    background: #f7fafc;
    color: #4a5568;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid #e2e8f0;
}

.refresh-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #718096;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.refresh-btn:hover {
    background: #f7fafc;
    color: #4a5568;
    transform: rotate(180deg);
}

.audio-controls {
    display: flex;
    align-items: center;
    gap: 16px;
}



.play-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 20px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.play-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}


.play-btn:active {
    transform: scale(0.95);
}

.audio-info {
    flex-grow: 1;
}

.audio-times {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 12px;
    color: #718096;
    font-weight: 500;
}

.audio-progress {
    width: 100%;
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
}

.audio-progress:hover {
    height: 8px;
    margin: -1px 0;
}

.audio-progress-bar {
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    width: 0%;
    transition: width 0.1s ease;
    border-radius: 3px;
}
.audio-controls-right {
    display: flex;
    gap: 8px;
    align-items: center;
}

.control-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #718096;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}
.control-btn:hover {
    background: #f7fafc;
    color: #4a5568;
    border-color: #cbd5e0;
}

.speed-text {
    font-size: 11px;
    font-weight: 600;
}

/* États spéciaux */
.generating-audio {
    background: linear-gradient(135deg, #fef5e7 0%, #fed7aa 100%);
    border-color: #f6ad55;
    color: #744210;
}
.generating-audio .spinner-border {
    color: #ed8936;
}

.audio-error {
    background: linear-gradient(135deg, #fed7d7 0%, #fc8181 100%);
    border-color: #f56565;
    color: #742a2a;
}

.generate-btn {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #4a5568;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}
.generate-btn:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
    color: #2d3748;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Animation de pulsation pour la génération */
.generating-pulse {
    animation: pulse 2s infinite;
}

@keyframes spinner-border {
    to { transform: rotate(360deg); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}
/* Responsive */
@media (max-width: 768px) {
    .audio-player {
        padding: 20px;
        margin: 20px 0;
    }
    
    .audio-header {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }
    
    .audio-controls {
        gap: 12px;
    }
    
    .play-btn {
        width: 48px;
        height: 48px;
        font-size: 18px;
    }
    
    .control-btn {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
}
/* Animation d'apparition */
.audio-player {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection

@section('content')

    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="position-relative">
                            @if($blog->image_url)
                                <img src="{{ asset('storage/' . $blog->image_url) }}"
                                    class="img-fluid w-full h-320px object-fit-cover overflow-hidden rounded-3" alt="{{ $blog->title }}">
                            @else
                                <div class="img-fluid w-full h-320px bg-light d-flex align-items-center justify-content-center rounded-3">
                                    <i class="ri-image-line fs-48 text-muted"></i>
                                </div>
                            @endif
                            <div class="position-absolute top-0 start-0 mt-4 ms-4 cursor-pointer">
                                <span class="badge bg-primary">{{ $blog->category }}</span>
                            </div>
                            
                            <!-- Boutons d'édition et suppression (uniquement pour le propriétaire) -->
                            @auth
                                @if(Auth::id() === $blog->user_id)
                                    <div class="position-absolute top-0 end-0 mt-4 me-4">
                                        <div class="btn-group">
                                            <a href="{{ route('blogs.edit', $blog->id) }}" class="btn btn-warning btn-sm">
                                                <i class="ri-edit-line me-1"></i>Éditer
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                <i class="ri-delete-bin-line me-1"></i>Supprimer
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endauth
                        </div>
                        <div class="mt-5">
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <div class="d-flex gap-3 align-items-center">
                                    @if($blog->user->avatar)
                                        <img src="{{ asset('storage/' . $blog->user->avatar) }}" alt="Avatar de {{ $blog->user->name }}"
                                            class="avatar-lg rounded-pill shadow" style="object-fit: cover;">
                                    @else
                                        <div class="avatar-lg rounded-pill shadow bg-primary d-flex align-items-center justify-content-center text-white">
                                            {{ $blog->user->initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0 fs-16">{{ $blog->user->name }}</h6>
                                        <p class="text-muted mb-0 fs-12">{{ $blog->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-0 fs-12 gap-4">
                                    <div>
                                        <i class="ri-time-line fs-14"></i>
                                        <span class="ps-1">{{ $blog->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div>
                                        <i class="ri-calendar-line fs-14"></i>
                                        <span class="ps-1">{{ $blog->created_at->format('H:i') }}</span>
                                    </div>
                                    <div>
                                        <i class="ri-chat-1-line fs-14"></i>
                                        <span class="ps-1">{{ $blog->comments_count }} commentaires</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('blogs.index') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="ri-arrow-left-line me-1"></i>Retour
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <h4 class="mb-3">{{ $blog->title }}</h4>
                            
                            <!-- Ajoutez après le titre du blog -->
<div class="d-flex align-items-center gap-2 mb-3">
    @auth
        <!-- Bouton Favori -->
        <button class="btn btn-outline-danger btn-sm favorite-btn" 
                data-blog-id="{{ $blog->id }}" 
                data-type="favorite"
                data-favorited="{{ Auth::user()->hasFavorite($blog->id, 'favorite') ? 'true' : 'false' }}">
            <i class="ri-heart-{{ Auth::user()->hasFavorite($blog->id, 'favorite') ? 'fill' : 'line' }}"></i>
            <span class="favorite-text">
                {{ Auth::user()->hasFavorite($blog->id, 'favorite') ? 'Favori' : 'Ajouter aux favoris' }}
            </span>
            <span class="favorite-count">({{ $blog->favorites_count }})</span>
        </button>

        <!-- Bouton Lire plus tard -->
        <button class="btn btn-outline-warning btn-sm read-later-btn" 
                data-blog-id="{{ $blog->id }}" 
                data-type="read_later"
                data-saved="{{ Auth::user()->hasFavorite($blog->id, 'read_later') ? 'true' : 'false' }}">
            <i class="ri-bookmark-{{ Auth::user()->hasFavorite($blog->id, 'read_later') ? 'fill' : 'line' }}"></i>
            <span class="read-later-text">
                {{ Auth::user()->hasFavorite($blog->id, 'read_later') ? 'Sauvegardé' : 'Lire plus tard' }}
            </span>
               </button>
    @else
        <div class="text-muted">
            <a href="{{ route('login') }}">Connectez-vous</a> pour sauvegarder cet article
        </div>
    @endauth
</div>
                              <!-- 🎵 NOUVEAU : Lecteur Audio -->
                               <div class="audio-player @if($blog->audio_generated && !$blog->has_audio) generating-audio generating-pulse @elseif(!$blog->has_audio && !$blog->audio_generated) audio-error @endif" id="audioPlayer">

                 @if($blog->has_audio)
    <!-- Lecteur audio complet -->
    <div class="audio-header">
        <h6 class="audio-title">
            <div class="audio-icon">
                <i class="ri-music-2-line"></i>
            </div>
            Version Audio
        </h6>
        <div class="audio-badges">
            <span class="duration-badge">{{ $blog->estimated_duration ?? 5 }} min</span>
            @if($blog->user_id === Auth::id())
                <button onclick="regenerateAudio()" class="refresh-btn" title="Régénérer l'audio">
                    <i class="ri-refresh-line"></i>
                </button>
            @endif
        </div>
    </div>
    
    <div class="audio-controls">
        <button class="play-btn" id="playButton" onclick="togglePlay()">
            <i class="ri-play-fill" id="playIcon"></i>
        </button>
        
        <div class="audio-info">
            <div class="audio-times">
                <span id="currentTime">0:00</span>
                <span id="duration">--:--</span>
            </div>
            <div class="audio-progress" onclick="setProgress(event)">
                <div class="audio-progress-bar" id="progressBar"></div>
            </div>
        </div>
        
        <div class="audio-controls-right">
            <button class="control-btn" onclick="changeSpeed()" title="Vitesse de lecture">
                <span class="speed-text" id="speedText">1x</span>
            </button>
            <button class="control-btn" onclick="toggleMute()" title="Couper le son">
                <i class="ri-volume-up-line" id="volumeIcon"></i>
            </button>
        </div>
    </div>
    
    <!-- ✅ CORRECTION : Chemin correct vers le fichier audio -->
    <audio id="audioElement" preload="metadata">
        <source src="{{ $blog->audio_full_url }}" type="audio/mpeg">
        Votre navigateur ne supporte pas l'audio HTML5.
    </audio>

@elseif($blog->audio_generated)
    <!-- Génération en cours -->
    <div class="audio-header">
        <h6 class="audio-title">
            <div class="audio-icon">
                <div class="spinner-border spinner-border-sm"></div>
            </div>
            Génération en cours
        </h6>
    </div>
    <div class="text-center py-2">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        <span class="fw-medium">Création de la version audio...</span>
        <p class="text-muted mb-0 mt-1" style="font-size: 13px;">
            Cela peut prendre quelques instants. 
            <a href="#" onclick="location.reload()" class="text-primary">Actualiser</a>
        </p>
    </div>

@else
    <!-- Pas d'audio -->
    <div class="audio-header">
        <h6 class="audio-title">
            <div class="audio-icon">
                <i class="ri-volume-mute-line"></i>
            </div>
            Aucun audio
        </h6>
    </div>
    <div class="text-center py-2">
        <p class="text-muted mb-3" style="font-size: 14px;">
            Aucune version audio n'a été générée pour cet article
        </p>
        @if($blog->user_id === Auth::id())
            <button onclick="generateAudio()" class="generate-btn">
                <i class="ri-magic-line"></i>
                Générer l'audio
            </button>
        @endif
    </div>
@endif
</div>


                            <div class="blog-content text-muted fs-15">
                                {!! nl2br(e($blog->content)) !!}
                            </div>
                        </div>

                    </div>
                </div>
                


<!-- 📚 Recommendation Section - Add this here -->
@if($blog->hasRecommendation())
<div class="card mt-4">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0"><i class="ri-lightbulb-line me-2"></i>Ressource recommandée</h5>
    </div>
    <div class="card-body">
        <h6 class="fw-bold mb-3">{{ $blog->recommendation->title }}</h6>
        
        <div class="d-flex flex-wrap gap-2 mb-3">
            @if($blog->recommendation->category)
            <span class="badge bg-light text-primary">
                <i class="ri-folder-line me-1"></i> {{ $blog->recommendation->category }}
            </span>
            @endif
            
            @if($blog->recommendation->content_type)
            <span class="badge bg-info">
                <i class="ri-file-list-line me-1"></i> {{ $blog->recommendation->content_type }}
            </span>
            @endif

            @if($blog->recommendation->difficulty_level && $blog->recommendation->difficulty_level !== 'N/A')
            <span class="badge bg-warning text-dark">
                <i class="ri-bar-chart-line me-1"></i> Niveau: {{ $blog->recommendation->difficulty_level }}
            </span>
            @endif
            
            @if($blog->recommendation->estimated_time && $blog->recommendation->estimated_time !== 'N/A')
            <span class="badge bg-success">
                <i class="ri-time-line me-1"></i> {{ $blog->recommendation->estimated_time }}
            </span>
            @endif
        </div>
        
        <p class="card-text mb-4">{{ $blog->recommendation->description }}</p>
        
        <div class="d-flex flex-wrap gap-2">
            @if($blog->recommendation->url)
            <a href="{{ $blog->recommendation->url }}" class="btn btn-primary btn-sm" target="_blank">
                <i class="ri-external-link-line me-1"></i> Consulter la ressource
            </a>
            @endif
            
            <!-- Show "More Details" button only to the blog author -->
            @if(Auth::check() && Auth::id() === $blog->user_id)
            <a href="{{ route('recommendations.show', $blog->recommendation->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="ri-information-line me-1"></i> Plus de détails
            </a>
            @endif
        </div>
    </div>
</div>
@endif
<!-- End of Recommendation Section -->
<!-- End of Recommendation Section -->


                <!-- Section Commentaires -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0 fw-semibold">
                            Commentaires ({{ $blog->comments->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Formulaire pour ajouter un commentaire -->
                        @auth
                        <div class="border-bottom pb-4 mb-4">
                            <form action="{{ route('comments.store', $blog->id) }}" method="POST">
                                @csrf
                                <div class="d-flex gap-3">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                            class="rounded-pill" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-pill bg-primary d-flex align-items-center justify-content-center text-white" 
                                             style="width: 40px; height: 40px; font-size: 14px;">
                                            {{ Auth::user()->initials }}
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <textarea name="content" class="form-control" rows="3" 
                                                placeholder="Écrire un commentaire..." required></textarea>
                                        <div class="d-flex justify-content-end mt-2">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="ri-send-plane-line me-1"></i>Publier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @else
                            <div class="text-center py-3 border-bottom mb-4">
                                <p class="text-muted">
                                    <a href="{{ route('login') }}">Connectez-vous</a> pour laisser un commentaire.
                                </p>
                            </div>
                        @endauth

                        <!-- Liste des commentaires -->
                        <div class="px-2" data-simplebar style="max-height: 500px;">
                            @forelse($blog->comments as $comment)
                                <div class="comment-item p-3 rounded mb-3">
                                    <div class="d-flex gap-3">
                                        @if($comment->user->avatar)
                                            <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="Avatar"
                                                class="rounded-pill" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-pill bg-primary d-flex align-items-center justify-content-center text-white" 
                                                 style="width: 40px; height: 40px; font-size: 14px;">
                                                {{ $comment->user->initials }}
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $comment->user->name }}</h6>
                                                    <p class="fs-12 mb-2 text-muted">{{ $comment->created_at->diffForHumans() }}</p>
                                                </div>
                                                @auth
                                                    @if($comment->user_id === Auth::id())
                                                        <div class="dropdown">
                                                            <button class="btn btn-link btn-sm text-muted" type="button" 
                                                                    data-bs-toggle="dropdown">
                                                                <i class="ri-more-2-line"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <form action="{{ route('comments.destroy', $comment->id) }}" 
                                                                          method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item text-danger"
                                                                                onclick="return confirm('Supprimer ce commentaire ?')">
                                                                            <i class="ri-delete-bin-line me-2"></i>Supprimer
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                @endauth
                                            </div>
                                            <p class="mb-3">{{ $comment->content }}</p>
                                            
                                            <!-- Boutons Like/Dislike -->
                                            @auth
                                                <div class="d-flex gap-2 align-items-center">
                                                    <button class="like-btn {{ $comment->isLikedBy(Auth::id()) ? 'active' : '' }}"
                                                            onclick="toggleLike({{ $comment->id }}, true)">
                                                        <i class="ri-thumb-up-line me-1"></i>
                                                        <span class="likes-count">{{ $comment->likes_count }}</span>
                                                    </button>
                                                    <button class="dislike-btn {{ $comment->isDislikedBy(Auth::id()) ? 'active' : '' }}"
                                                            onclick="toggleLike({{ $comment->id }}, false)">
                                                        <i class="ri-thumb-down-line me-1"></i>
                                                        <span class="dislikes-count">{{ $comment->dislikes_count }}</span>
                                                    </button>
                                                </div>
                                            @else
                                                <div class="d-flex gap-2 align-items-center text-muted">
                                                    <span><i class="ri-thumb-up-line me-1"></i>{{ $comment->likes_count }}</span>
                                                    <span><i class="ri-thumb-down-line me-1"></i>{{ $comment->dislikes_count }}</span>
                                                </div>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="ri-chat-3-line fs-48 text-muted mb-3"></i>
                                    <p class="text-muted">Aucun commentaire pour le moment.</p>
                                    <p class="text-muted">Soyez le premier à donner votre avis !</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-12 col-xl-4">
                <!-- ...existing sidebar code... -->
            </div>
        </div>

    </div><!--End container-fluid-->

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
   
@if(isset($showRecommendationAlert) && $showRecommendationAlert)
<script>
    // Afficher la notification SweetAlert uniquement si une nouvelle recommandation a été générée
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '🎉 Nouvelle recommandation !',
            html: `
                <div class="text-start">
                    <p><strong>Pour votre article :</strong> {{ $recommendationData['blog_title'] ?? $blog->title }}</p>
                    <p>Notre IA a généré une recommandation personnalisée</p>
                    <a href="{{ route('recommendations.show', $recommendationData['recommendation_id'] ?? 0) }}" class="btn btn-primary mt-2">
                        Voir la recommandation
                    </a>
                </div>
            `,
            icon: 'success',
            showConfirmButton: true,
            confirmButtonText: 'D\'accord',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('.card.mt-4')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.querySelector('.card.mt-4')?.classList.add('highlight-section');
            }
        });
    });
</script>

<style>
/* Ajouter cette classe pour l'animation de highlight */
.highlight-section {
    animation: highlight-pulse 2s ease-in-out;
}

@keyframes highlight-pulse {
    0% { box-shadow: 0 0 0 0 rgba(52, 144, 220, 0.5); }
    50% { box-shadow: 0 0 0 15px rgba(52, 144, 220, 0.2); }
    100% { box-shadow: 0 0 0 0 rgba(52, 144, 220, 0); }
}
</style>
@endif

<script>
// ✅ Variables globales pour l'audio - DÉCLARÉES EN PREMIER
let audio = null;
let isPlaying = false;
let currentSpeed = 1;
let speeds = [0.5, 0.75, 1, 1.25, 1.5, 2];
let currentSpeedIndex = 2;

// ✅ Initialisation - APRÈS la déclaration des variables
document.addEventListener('DOMContentLoaded', function() {
    audio = document.getElementById('audioElement');
    
    if (audio) {
        console.log('🎵 Audio element found');
        console.log('📍 Audio source:', audio.src);
        
        audio.addEventListener('loadedmetadata', function() {
            console.log('✅ Metadata loaded');
            updateDuration();
        });
        
        audio.addEventListener('timeupdate', updateProgress);
        audio.addEventListener('ended', audioEnded);
        
        audio.addEventListener('canplay', function() {
            console.log('✅ Audio can play');
        });
        
        audio.addEventListener('error', function(e) {
            console.error('❌ Audio error:', audio.error ? audio.error.code : 'unknown');
            console.error('Error details:', {
                code: audio.error?.code,
                message: audio.error?.message,
                src: audio.src
            });
        });
        
        // Forcer le chargement des métadonnées
        audio.load();
    } else {
        console.warn('⚠️ Audio element not found');
    }
});

// ✅ Fonction togglePlay - DOIT ÊTRE GLOBALE
function togglePlay() {
    if (!audio) {
        console.error('❌ No audio element');
        return;
    }
    
    if (isPlaying) {
        audio.pause();
        document.getElementById('playIcon').className = 'ri-play-fill';
        isPlaying = false;
        console.log('⏸️ Audio paused');
    } else {
        audio.play()
            .then(() => {
                document.getElementById('playIcon').className = 'ri-pause-fill';
                isPlaying = true;
                console.log('▶️ Audio playing');
            })
            .catch(error => {
                console.error('❌ Play failed:', error);
                alert('Erreur de lecture : ' + error.message);
            });
    }
}

// ✅ Mettre à jour la durée
function updateDuration() {
    const duration = audio.duration;
    if (!isNaN(duration)) {
        document.getElementById('duration').textContent = formatTime(duration);
        console.log('✅ Duration updated:', formatTime(duration));
    } else {
        console.warn('⚠️ Duration is NaN');
    }
}

// ✅ Mettre à jour le progrès
function updateProgress() {
    const currentTime = audio.currentTime;
    const duration = audio.duration;
    const progress = (currentTime / duration) * 100;
    
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('currentTime').textContent = formatTime(currentTime);
}

// ✅ Définir la position
function setProgress(event) {
    const progressBar = event.currentTarget;
    const clickX = event.offsetX;
    const width = progressBar.offsetWidth;
    const duration = audio.duration;
    
    audio.currentTime = (clickX / width) * duration;
}

// ✅ Changer la vitesse
function changeSpeed() {
    currentSpeedIndex = (currentSpeedIndex + 1) % speeds.length;
    currentSpeed = speeds[currentSpeedIndex];
    audio.playbackRate = currentSpeed;
    document.getElementById('speedText').textContent = currentSpeed + 'x';
}

// ✅ Couper/remettre le son
function toggleMute() {
    audio.muted = !audio.muted;
    const icon = document.getElementById('volumeIcon');
    icon.className = audio.muted ? 'ri-volume-mute-line' : 'ri-volume-up-line';
}

// ✅ Fin de lecture
function audioEnded() {
    document.getElementById('playIcon').className = 'ri-play-fill';
    isPlaying = false;
    document.getElementById('progressBar').style.width = '0%';
    audio.currentTime = 0;
}

// ✅ Formater le temps
function formatTime(seconds) {
    if (isNaN(seconds)) return '--:--';
    
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
}

// ✅ Régénérer l'audio avec SweetAlert2
function regenerateAudio() {
    Swal.fire({
        title: '🎵 Régénération Audio',
        text: 'Voulez-vous régénérer la version audio ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Régénérer',
        cancelButtonText: 'Annuler',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Génération en cours...',
                html: '<div class="spinner-border text-primary" role="status"></div>',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            fetch('{{ route("blogs.regenerate-audio", $blog->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Succès !',
                        text: 'Audio régénéré avec succès',
                        icon: 'success',
                        confirmButtonText: 'Recharger la page',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.error || 'Erreur inconnue');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Erreur',
                    text: error.message || 'Impossible de régénérer l\'audio',
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });
            });
        }
    });
}

// ✅ Raccourcis clavier
document.addEventListener('keydown', function(event) {
    if (audio && event.target.tagName !== 'INPUT' && event.target.tagName !== 'TEXTAREA') {
        switch(event.code) {
            case 'Space':
                event.preventDefault();
                togglePlay();
                break;
            case 'ArrowLeft':
                event.preventDefault();
                audio.currentTime = Math.max(0, audio.currentTime - 10);
                break;
            case 'ArrowRight':
                event.preventDefault();
                audio.currentTime = Math.min(audio.duration, audio.currentTime + 10);
                break;
            case 'KeyM':
                event.preventDefault();
                toggleMute();
                break;
        }
    }
});

// Gestion des favoris
function toggleFavorite(button) {
    const blogId = button.dataset.blogId;
    const type = button.dataset.type;
    
    fetch(`/blogs/${blogId}/favorite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateFavoriteButton(button, data);
            
            Swal.fire({
                title: data.message,
                icon: 'success',
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                position: 'top-end'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
}

function updateFavoriteButton(button, data) {
    const icon = button.querySelector('i');
    const text = button.querySelector('.favorite-text, .read-later-text');
    
    if (data.type === 'favorite') {
        if (data.is_favorited) {
            button.classList.add('btn-danger');
            button.classList.remove('btn-outline-danger');
            icon.className = 'ri-heart-fill';
            if (text) text.textContent = 'Retirer des favoris';
        } else {
            button.classList.remove('btn-danger');
            button.classList.add('btn-outline-danger');
            icon.className = 'ri-heart-line';
            if (text) text.textContent = 'Ajouter aux favoris';
        }
    } else {
        if (data.is_favorited) {
            button.classList.add('btn-warning');
            button.classList.remove('btn-outline-warning');
            icon.className = 'ri-bookmark-fill';
            if (text) text.textContent = 'Retirer de la liste';
        } else {
            button.classList.remove('btn-warning');
            button.classList.add('btn-outline-warning');
            icon.className = 'ri-bookmark-line';
            if (text) text.textContent = 'Lire plus tard';
        }
    }
    
    // Mettre à jour le compteur
    const count = button.querySelector('.favorite-count, .read-later-count');
    if (count && data.count !== undefined) {
        count.textContent = `(${data.count})`;
    }
}

// Fonction pour gérer les likes/dislikes via AJAX
function toggleLike(commentId, isLike) {
    fetch(`/comments/${commentId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ is_like: isLike })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour les compteurs
            document.getElementById(`like-count-${commentId}`).textContent = data.likes_count;
            document.getElementById(`dislike-count-${commentId}`).textContent = data.dislikes_count;
            
            // Mettre à jour les classes actives
            const likeBtn = document.querySelector(`[onclick="toggleLike(${commentId}, true)"]`);
            const dislikeBtn = document.querySelector(`[onclick="toggleLike(${commentId}, false)"]`);
            
            likeBtn.classList.toggle('active', data.user_reaction === 'like');
            dislikeBtn.classList.toggle('active', data.user_reaction === 'dislike');
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<script src="{{ asset('assets/js/app.js') }}"></script>
@endsection
@extends('partials.layouts.master')

@section('title', 'Liste des Articles | SmartHealth')
@section('title-sub', 'Blog')
@section('pagetitle', 'Liste des Articles')

@section('css')
<style>
    .blogCard .card {
        height: 100%;
        display: flex;
        flex-direction: column;
        max-height: 400px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .blogCard .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .blogCard .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .blogCard .card-body > div:last-child {
        margin-top: auto;
    }
    
    .blog-image-container {
        height: 140px;
        overflow: hidden;
        position: relative;
    }
    
    .blog-image-container img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .blogCard .card:hover .blog-image-container img {
        transform: scale(1.05);
    }
    
    .blog-image-placeholder {
        width: 100%;
        height: 140px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .blog-content {
        flex-grow: 1;
    }
    
    .blog-title {
        height: 2.5rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.25rem;
    }
    
    .blog-excerpt {
        height: 3rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.5rem;
    }

    .blog-author {
        min-height: 50px;
    }
    
    .blog-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .blog-link:hover {
        color: inherit;
        text-decoration: none;
    }

    .blog-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 10;
    }

    .blog-actions .dropdown-toggle::after {
        display: none;
    }

    .blog-actions .btn {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .blog-actions .btn:hover {
        background: rgba(255, 255, 255, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

        .mini-favorite-btn, .mini-read-later-btn {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .mini-favorite-btn:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .mini-read-later-btn:hover {
        background-color: #ffc107;
        border-color: #ffc107;
        color: white;
    }
    
    .mini-favorite-btn.btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .mini-read-later-btn.btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: white;
    }
    .favorite-alert {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.favorite-alert .btn-close {
    font-size: 0.75rem;
}
/* Animation du bouton pendant le chargement */
.mini-favorite-btn:disabled,
.mini-read-later-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.mini-favorite-btn:disabled i,
.mini-read-later-btn:disabled i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
@endsection

@section('content')
    <!-- begin::App -->
    <div id="layout-wrapper">
        <div class="row">
            <div class="col-12 col-xl-8 col-xxl-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Articles de Blog</h4>
                    <div class="d-flex gap-2">
        @auth
            <a href="{{ route('favorites.index') }}" class="btn btn-outline-primary">
                <i class="ri-heart-line me-1"></i>Mes Favoris
                @if(Auth::user()->blogFavorites()->count() > 0)
                    <span class="badge bg-danger ms-1">{{ Auth::user()->blogFavorites()->count() }}</span>
                @endif
            </a>
        @endauth
        <a href="{{ route('blogs.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i>Nouvel Article
        </a>
    </div>
                   
                </div>

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

                <div class="row gx-6 gy-3">
                    @forelse($blogs as $blog)
                    <div class="col-md-6 col-xxl-4">
                        <div class="blogCard">
                            <div class="card overflow-hidden h-100">
                                <div class="blog-image-container">
                                    <!-- Actions du propriétaire -->
                                    @auth
                                        @if(Auth::id() === $blog->user_id)
                                            <div class="blog-actions">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill fs-14"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('blogs.edit', $blog->id) }}">
                                                                <i class="ri-edit-line me-2 text-warning"></i>Éditer
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $blog->id }}, '{{ addslashes($blog->title) }}');">
                                                                <i class="ri-delete-bin-line me-2"></i>Supprimer
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    @endauth

                                    <!-- Image avec lien -->
                                    <a href="{{ route('blogs.show', $blog->id) }}">
                                        @if($blog->image_url)
                                            <img src="{{ asset('storage/' . $blog->image_url) }}" 
                                                 alt="{{ $blog->title }}">
                                        @else
                                            <div class="blog-image-placeholder">
                                                <i class="ri-image-line fs-36 text-muted"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                                
                                <!-- Corps de la carte -->
                                <div class="card-body p-3 d-flex flex-column">
                                    <div class="blog-author d-flex align-items-center mb-2">
                                        @if($blog->user->avatar)
                                            <img src="{{ asset('storage/' . $blog->user->avatar) }}" alt="Avatar de {{ $blog->user->name }}"
                                                 class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" 
                                                 style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ $blog->user->initials }}
                                            </div>
                                        @endif
                                        <div class="ms-2 flex-grow-1">
                                            <h6 class="mb-0 fs-13">{{ $blog->user->name }}</h6>
                                            <small class="text-muted fs-11">{{ $blog->created_at->format('d M Y') }}</small>
                                        </div>
                                        @auth
                                            @if(Auth::id() === $blog->user_id)
                                                <span class="badge bg-success-subtle text-success fs-10">Mes articles</span>
                                            @endif
                                        @endauth
                                    </div>
                                    
                                    <div class="blog-content flex-grow-1">
                                        <h5 class="blog-title mb-2">
                                            <a href="{{ route('blogs.show', $blog->id) }}" class="text-decoration-none text-dark">
                                                {{ $blog->title }}
                                            </a>
                                        </h5>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-primary-subtle text-primary fs-11">{{ $blog->category }}</span>
                                        </div>
                                        
                                        <p class="blog-excerpt mb-2 fs-14 text-muted">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($blog->content), 80) }}
                                        </p>
                                    </div>
                                    
                                    <!-- Actions en bas - SANS lien parent -->
                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                        <div>
                                            <i class="ri-time-line fs-12 text-muted"></i>
                                            <span class="ps-1 text-muted fs-12">{{ $blog->created_at->format('H:i') }}</span>
                                        </div>
                                        <div class="d-flex gap-1 align-items-center">
                                            @auth
                                                <!-- Mini bouton favori -->
                                                <button class="btn btn-sm {{ Auth::user()->hasFavorite($blog->id, 'favorite') ? 'btn-danger' : 'btn-outline-danger' }} mini-favorite-btn" 
                                                        data-blog-id="{{ $blog->id }}" 
                                                        data-type="favorite"
                                                        title="Ajouter aux favoris">
                                                    <i class="ri-heart-{{ Auth::user()->hasFavorite($blog->id, 'favorite') ? 'fill' : 'line' }} fs-12"></i>
                                                </button>
                                                
                                                <!-- Mini bouton lire plus tard -->
                                                <button class="btn btn-sm {{ Auth::user()->hasFavorite($blog->id, 'read_later') ? 'btn-warning' : 'btn-outline-warning' }} mini-read-later-btn" 
                                                        data-blog-id="{{ $blog->id }}" 
                                                        data-type="read_later"
                                                        title="Lire plus tard">
                                                    <i class="ri-bookmark-{{ Auth::user()->hasFavorite($blog->id, 'read_later') ? 'fill' : 'line' }} fs-12"></i>
                                                </button>
                                            @endauth
                                            
                                            <a href="{{ route('blogs.show', $blog->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ri-eye-line fs-12 me-1"></i>Lire
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-4">
                                <i class="ri-article-line fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun article pour le moment</h5>
                                <p class="text-muted">Commencez par créer votre premier article de blog.</p>
                                <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line me-1"></i>Créer un Article
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="col-12 col-xl-4 col-xxl-3">
                <div class="blog-details-list mb-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="position-relative">
                                <div class="form-icon">
                                    <input type="search" class="form-control form-control-icon" id="searchBlog"
                                           placeholder="Rechercher des articles...">
                                    <i class="bi bi-search"></i>
                                </div>
                            </div>
                            
                            <h6 class="mb-4 mt-4">Articles Récents</h6>
                            @foreach($blogs->take(4) as $recentBlog)
                                <div class="border-bottom py-2">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="flex-shrink-0">
                                            @if($recentBlog->image_url)
                                                <img src="{{ asset('storage/' . $recentBlog->image_url) }}"
                                                     class="rounded object-fit-cover" style="width: 50px; height: 50px;" 
                                                     alt="{{ $recentBlog->title }}">
                                            @else
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="ri-image-line fs-16 text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="text-muted d-block small">{{ $recentBlog->created_at->format('d M Y') }}</span>
                                            <a href="{{ route('blogs.show', $recentBlog->id) }}"
                                               class="fw-semibold text-body text-decoration-none d-block fs-13">
                                                {{ \Illuminate\Support\Str::limit($recentBlog->title, 35) }}
                                            </a>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="text-muted fs-11">{{ \Illuminate\Support\Str::limit($recentBlog->user->name, 15) }}</span>
                                                <span class="badge bg-primary-subtle text-primary fs-10">{{ \Illuminate\Support\Str::limit($recentBlog->category, 12) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <h6 class="mb-3 mt-4">Catégories</h6>
                            @php
                                $categories = $blogs->groupBy('category')->map(function($group) {
                                    return $group->count();
                                });
                            @endphp
                            
                            @foreach($categories as $category => $count)
                                <a href="javascript:void(0)" class="d-flex justify-content-between mb-2">
                                    <p class="mb-0 fw-medium text-body fs-13">{{ $category }}</p>
                                    <span class="text-muted fs-11">({{ $count }})</span>
                                </a>
                            @endforeach

                            <!-- Section Mes Articles -->
                            @auth
                                @php
                                    $myBlogs = $blogs->where('user_id', Auth::id());
                                @endphp
                                @if($myBlogs->count() > 0)
                                    <h6 class="mb-3 mt-4">Mes Articles</h6>
                                    @foreach($myBlogs->take(3) as $myBlog)
                                        <div class="border-bottom py-2">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" 
                                                         style="width: 20px; height: 20px;">
                                                        <i class="ri-user-line fs-10 text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('blogs.show', $myBlog->id) }}"
                                                       class="fw-semibold text-body text-decoration-none d-block fs-12">
                                                        {{ \Illuminate\Support\Str::limit($myBlog->title, 30) }}
                                                    </a>
                                                    <div class="d-flex gap-2 mt-1">
                                                        <a href="{{ route('blogs.edit', $myBlog->id) }}" class="text-warning fs-10">Éditer</a>
                                                        <span class="text-muted fs-10">{{ $myBlog->created_at->format('d M') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer l'article :</p>
                    <p class="fw-bold text-primary" id="blogTitleToDelete"></p>
                    <p class="text-muted">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-delete-bin-line me-1"></i>Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fonctionnalité de recherche simple
        document.getElementById('searchBlog').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const blogCards = document.querySelectorAll('.blogCard');
            
            blogCards.forEach(function(card) {
                const title = card.querySelector('h5').textContent.toLowerCase();
                const content = card.querySelector('p').textContent.toLowerCase();
                const author = card.querySelector('.blog-author h6').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || content.includes(searchTerm) || author.includes(searchTerm)) {
                    card.parentElement.style.display = 'block';
                } else {
                    card.parentElement.style.display = 'none';
                }
            });
        });

        // Fonction de confirmation de suppression
        function confirmDelete(blogId, blogTitle) {
            // Empêcher la propagation du clic vers le lien parent
            event.preventDefault();
            event.stopPropagation();
            
            // Remplir le modal avec les informations du blog
            document.getElementById('blogTitleToDelete').textContent = blogTitle;
            document.getElementById('deleteForm').action = `/pages-blog-delete/${blogId}`;
            
            // Afficher le modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // CORRECTION : Empêcher la navigation SEULEMENT pour le bouton dropdown, PAS pour les liens d'édition
        document.addEventListener('click', function(e) {
            // Si c'est le bouton dropdown (3 points), empêcher la navigation
            if (e.target.closest('.dropdown-toggle') || e.target.closest('button[data-bs-toggle="dropdown"]')) {
                e.preventDefault();
                e.stopPropagation();
            }
            // Si c'est un lien d'édition ou de suppression dans le dropdown, laisser passer
            else if (e.target.closest('.dropdown-item')) {
                // Ne rien faire, laisser le lien fonctionner normalement
                return;
            }
            // Si c'est le container des actions, empêcher seulement si ce n'est pas un lien
            else if (e.target.closest('.blog-actions') && !e.target.closest('a')) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        // Réinitialiser la recherche si nécessaire
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchBlog');
            if (searchInput.value === '') {
                document.querySelectorAll('.blogCard').forEach(function(card) {
                    card.parentElement.style.display = 'block';
                });
            }
        });

             
        // Gestion des mini-boutons de favoris avec SweetAlert
        document.addEventListener('click', function(e) {
            if (e.target.closest('.mini-favorite-btn') || e.target.closest('.mini-read-later-btn')) {
                e.preventDefault();
                e.stopPropagation();
                
                const button = e.target.closest('.mini-favorite-btn, .mini-read-later-btn');
                const blogId = button.dataset.blogId;
                const type = button.dataset.type;
                
                // Désactiver le bouton
                button.disabled = true;
                
                fetch(`/blogs/${blogId}/favorite`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ type: type })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let title = '';
                        let icon = '';
                        let color = '';
                        
                        if (data.type === 'favorite') {
                            if (data.is_favorited) {
                                title = 'Ajouté aux favoris !';
                                icon = 'success';
                                color = '#dc3545';
                            } else {
                                title = 'Retiré des favoris';
                                icon = 'info';
                                color = '#6c757d';
                            }
                        } else {
                            if (data.is_favorited) {
                                title = 'Ajouté à "Lire plus tard" !';
                                icon = 'success';
                                color = '#ffc107';
                            } else {
                                title = 'Retiré de "Lire plus tard"';
                                icon = 'info';
                                color = '#6c757d';
                            }
                        }
                        
                        // Afficher SweetAlert
                        Swal.fire({
                            title: title,
                            icon: icon,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            toast: true,
                            position: 'top-end',
                            background: '#fff',
                            color: color
                        }).then(() => {
                            // Recharger la page
                            window.location.reload();
                        });
                        
                    } else {
                        Swal.fire({
                            title: 'Erreur !',
                            text: 'Une erreur est survenue',
                            icon: 'error',
                            timer: 2000
                        });
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire({
                        title: 'Erreur !',
                        text: 'Problème de connexion',
                        icon: 'error',
                        timer: 2000
                    });
                    button.disabled = false;
                });
            }
        });
    </script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
@endsection
@extends('partials.layouts.master')

@section('title', 'Modifier mon profil | SmartHealth')
@section('title-sub', 'Profil')
@section('pagetitle', 'Modifier mon profil')

@section('content')
<div id="layout-wrapper">
    <div class="row">
        <div class="col-12">
            <!-- Debug des erreurs (temporaire) -->
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <h5>Erreurs détectées :</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Onglets -->
            <div class="mb-4">
                <ul class="nav nav-pills" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-info-tab" data-bs-toggle="tab"
                                data-bs-target="#profile-info" type="button" role="tab">
                            <i class="ri-user-line me-1"></i>Informations personnelles
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-photo-tab" data-bs-toggle="tab"
                                data-bs-target="#profile-photo" type="button" role="tab">
                            <i class="ri-image-line me-1"></i>Photo de profil
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-password-tab" data-bs-toggle="tab"
                                data-bs-target="#profile-password" type="button" role="tab">
                            <i class="ri-lock-line me-1"></i>Mot de passe
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Contenu des onglets -->
            <div class="tab-content" id="profileTabsContent">
                <!-- Onglet Informations personnelles -->
                <div class="tab-pane fade show active" id="profile-info" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informations personnelles</h5>
                        </div>
                        <div class="card-body">
                            <!-- FORMULAIRE 1: Informations personnelles -->
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="birth_date" class="form-label">Date de naissance</label>
                                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                               id="birth_date" name="birth_date" 
                                               value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                                        @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Genre</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                            <option value="">Sélectionnez votre genre</option>
                                            <option value="male" {{ old('gender', $user->gender ?? '') == 'male' ? 'selected' : '' }}>Homme</option>
                                            <option value="female" {{ old('gender', $user->gender ?? '') == 'female' ? 'selected' : '' }}>Femme</option>
                                            <option value="other" {{ old('gender', $user->gender ?? '') == 'other' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Adresse</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="2">{{ old('address', $user->address ?? '') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="bio" class="form-label">Biographie</label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                                  id="bio" name="bio" rows="3" 
                                                  placeholder="Parlez-nous un peu de vous...">{{ old('bio', $user->bio ?? '') }}</textarea>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('profile.show') }}" class="btn btn-secondary">Annuler</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Onglet Photo de profil -->
                <div class="tab-pane fade" id="profile-photo" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Photo de profil</h5>
                        </div>
                        <div class="card-body">
                            <!-- Affichage de l'avatar actuel -->
                            <div class="text-center mb-4">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar actuel" 
                                         class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                         style="width: 150px; height: 150px;">
                                        <span class="text-white fs-48 fw-bold">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- FORMULAIRE 2: Upload d'avatar -->
                            <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Nouvelle photo de profil</label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                           id="avatar" name="avatar" accept="image/*">
                                    <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB</div>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-upload-line me-1"></i>Mettre à jour la photo
                                    </button>
                                </div>
                            </form>

                            <!-- SECTION SÉPARÉE pour supprimer l'avatar -->
                            @if($user->avatar)
                            <hr class="my-4">
                            <div class="text-center">
                                <p class="text-muted mb-3">Ou supprimer la photo actuelle :</p>
                                <form action="{{ route('profile.delete-avatar') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')">
                                        <i class="ri-delete-bin-line me-1"></i>Supprimer la photo actuelle
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Onglet Mot de passe -->
                <div class="tab-pane fade" id="profile-password" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Changer le mot de passe</h5>
                        </div>
                        <div class="card-body">
                            <!-- FORMULAIRE 3: Mot de passe -->
                            <form action="{{ route('profile.update-password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                               id="current_password" name="current_password" required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                               id="new_password" name="new_password" required>
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="new_password_confirmation" class="form-label">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" 
                                               id="new_password_confirmation" name="new_password_confirmation" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('profile.show') }}" class="btn btn-secondary">Annuler</a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="ri-lock-line me-1"></i>Changer le mot de passe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if(session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-check-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/app.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus sur l'onglet avec erreur
    @if($errors->any())
        // Déterminer quel onglet a des erreurs
        @if($errors->has(['name', 'email', 'phone', 'address', 'birth_date', 'gender', 'bio']))
            // Erreurs dans l'onglet informations personnelles
            document.getElementById('profile-info-tab').click();
        @elseif($errors->has('avatar'))
            // Erreurs dans l'onglet photo
            document.getElementById('profile-photo-tab').click();
        @elseif($errors->has(['current_password', 'new_password']))
            // Erreurs dans l'onglet mot de passe
            document.getElementById('profile-password-tab').click();
        @endif
    @endif
});
</script>
@endsection
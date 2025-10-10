@extends('partials.layouts.master')

@section('title', 'Mon Profil | SmartHealth')
@section('title-sub', 'Profil')
@section('pagetitle', 'Mon Profil')

@section('content')
<div id="layout-wrapper">
    <!-- Header du profil avec photo de couverture -->
    <div class="card overflow-hidden">
        <div class="card-body h-176px" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        </div>
        <div class="mt-2">
            <div class="card-body p-5">
                <div class="d-flex float-end gap-2 flex-shrink-0">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="ri-edit-line me-1"></i>Modifier le profil
                    </a>
                </div>
                <div class="d-flex flex-wrap align-items-start gap-5">
                    <div class="mt-n12 flex-shrink-0">
                        <div class="position-relative d-inline-block">
                            @if($user->avatar)
                                <img src="{{ $user->avatar_url }}" alt="Avatar de {{ $user->name }}"
                                     class="h-128px w-128px border border-4 border-light shadow-lg rounded-circle object-fit-cover">
                            @else
                                <div class="h-128px w-128px border border-4 border-light shadow-lg rounded-circle bg-primary d-flex align-items-center justify-content-center">
                                    <span class="text-white fs-36 fw-bold">{{ $user->initials }}</span>
                                </div>
                            @endif
                            <span class="position-absolute profile-dot bg-success rounded-circle">
                                <span class="visually-hidden">En ligne</span>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="mb-5">
                            <h5 class="mb-1">{{ $user->name }} 
                                @if($user->email_verified_at)
                                    <i class="ri-verified-badge-fill fs-16 ms-1 text-success"></i>
                                @endif
                            </h5>
                            <p class="text-muted fs-12 mb-0">{{ $user->bio ?: 'Membre SmartHealth' }}</p>
                        </div>
                        
                        <!-- Statistiques du profil (SANS les blogs) -->
                        <div class="w-50 border-dashed border border-1">
                            <div class="p-4 d-flex">
                                <div class="d-flex flex-column justify-content-center gap-1 w-208px text-center border-end border-dark border-opacity-20">
                                    <h5 class="mb-0 lh-1">{{ $user->created_at->format('M Y') }}</h5>
                                    <span class="text-muted lh-sm fs-12">Membre depuis</span>
                                </div>
                                <div class="d-flex flex-column justify-content-center gap-1 w-208px text-center border-end border-dark border-opacity-20">
                                    <h5 class="mb-0 lh-1">
                                        @if($user->birth_date)
                                            {{ $user->birth_date->age }} ans
                                        @else
                                            Non précisé
                                        @endif
                                    </h5>
                                    <span class="text-muted lh-sm fs-12">Âge</span>
                                </div>
                                <div class="d-flex flex-column justify-content-center gap-1 w-208px text-center">
                                    <h5 class="mb-0 lh-1">
                                        @if($user->gender == 'male') Homme
                                        @elseif($user->gender == 'female') Femme
                                        @elseif($user->gender == 'other') Autre
                                        @else Non précisé
                                        @endif
                                    </h5>
                                    <span class="text-muted lh-sm fs-12">Genre</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de contact -->
                        <div class="row g-5 mt-2">
                            <div class="col-md-6 col-xl-6">
                                <div class="d-flex gap-2">
                                    <i class="ri-mail-line fs-16"></i>
                                    <p class="text-muted mb-2">Email</p>
                                </div>
                                <h6 class="mb-0">{{ $user->email }}</h6>
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="d-flex gap-2">
                                    <i class="ri-phone-line fs-16"></i>
                                    <p class="text-muted mb-2">Téléphone</p>
                                </div>
                                <h6 class="mb-0">{{ $user->phone ?: 'Non renseigné' }}</h6>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <i class="ri-map-pin-line fs-16"></i>
                                    <p class="text-muted mb-2">Adresse</p>
                                </div>
                                <h6 class="mb-0">{{ $user->address ?: 'Non renseignée' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section supplémentaire pour la biographie (si elle est longue) -->
    @if($user->bio && strlen($user->bio) > 100)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">À propos de moi</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $user->bio }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Messages de succès -->
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
@endsection
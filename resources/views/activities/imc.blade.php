{{-- filepath: c:\Users\ferie\OneDrive\Bureau\projetLaravel\SmartHealth_Laravel\resources\views\activities\imc.blade.php --}}

@extends('partials.layouts.master')

@section('title', 'Calculateur IMC | SmartHealth')
@section('title-sub', 'Activités Sportives')
@section('pagetitle', 'Calculateur IMC')

@section('css')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Animate.css pour les animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --danger-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        --card-shadow: 0 20px 40px rgba(0,0,0,0.1);
        --border-radius: 20px;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #f8f9fa;
    }

    .imc-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
    }

    .card-header-modern {
        background: var(--primary-gradient);
        padding: 2rem;
        text-align: center;
        border: none;
    }

    .card-header-modern h2 {
        color: white;
        font-weight: 600;
        margin: 0;
        font-size: 2rem;
    }

    .card-header-modern .subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        margin-top: 0.5rem;
    }

    .profile-section {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: none;
    }

    .profile-section h5 {
        color: #1565c0;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .profile-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .profile-item {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .form-control-modern {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        transform: translateY(-2px);
    }

    .btn-modern {
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }

    .btn-primary-modern {
        background: var(--primary-gradient);
        color: white;
    }

    .btn-primary-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-success-modern {
        background: var(--success-gradient);
        color: white;
    }

    .btn-success-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(79, 172, 254, 0.4);
    }

    .btn-pdf-modern {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
    }

    .btn-pdf-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(255, 107, 107, 0.4);
    }

    .results-container {
        margin-top: 2rem;
    }

    .result-card {
        border-radius: 15px;
        border: none;
        padding: 2rem;
        margin-bottom: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .result-success {
        background: var(--success-gradient);
        color: white;
    }

    .result-warning {
        background: var(--warning-gradient);
        color: white;
    }

    .result-danger {
        background: var(--danger-gradient);
        color: white;
    }

    .imc-display {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .imc-number {
        font-size: 3rem;
        font-weight: 700;
        margin: 0;
    }

    .imc-category {
        font-size: 1.2rem;
        font-weight: 500;
        opacity: 0.9;
    }

    .recommendations {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .recommendations h6 {
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .recommendations ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .recommendations li {
        margin-bottom: 0.5rem;
    }

    .ideal-weight {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        margin-top: 1rem;
    }

    .result-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
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

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    .input-group-modern {
        position: relative;
    }

    .input-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #667eea;
        z-index: 5;
    }

    .btn-group-modern {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .btn-group-modern {
            flex-direction: column;
        }
        
        .result-actions {
            flex-direction: column;
        }
        
        .imc-number {
            font-size: 2.5rem;
        }
        
        .profile-info {
            grid-template-columns: 1fr;
        }
    }

    /* Styles pour l'intégration avec le layout master */
    .page-content {
        padding: 20px;
    }

    .header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .avatar-lg {
        width: 60px;
        height: 60px;
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: white;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- En-tête avec breadcrumb -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 header-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="fas fa-calculator text-white fs-3"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 text-white">Calculateur IMC</h4>
                                    <p class="text-white-50 mb-0">Analysez votre indice de masse corporelle et obtenez des recommandations personnalisées</p>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0 mt-2">
                                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                                            <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Activités</a></li>
                                            <li class="breadcrumb-item active">Calculateur IMC</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('activities.index') }}" class="btn btn-light">
                                    <i class="fas fa-arrow-left me-1"></i>Retour aux activités
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card imc-card fade-in-up shadow-lg">
                    <div class="card-header-modern">
                        <h2><i class="fas fa-heartbeat me-3"></i>Calculateur IMC</h2>
                        <p class="subtitle">Analysez votre indice de masse corporelle avec précision</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Profil utilisateur -->
                        @auth
                        <div class="profile-section">
                            <h5><i class="fas fa-user-circle me-2"></i>Mon Profil</h5>
                            <div class="profile-info">
                                <div class="profile-item">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    <strong>Nom:</strong> {{ Auth::user()->name }}
                                </div>
                                @if(isset($userProfileData) && $userProfileData['weight'] && $userProfileData['height'])
                                    <div class="profile-item">
                                        <i class="fas fa-weight me-2 text-success"></i>
                                        <strong>Poids:</strong> {{ $userProfileData['weight'] }} kg
                                    </div>
                                    <div class="profile-item">
                                        <i class="fas fa-ruler-vertical me-2 text-info"></i>
                                        <strong>Taille:</strong> {{ $userProfileData['height'] }} cm
                                    </div>
                                    @if(isset($userImcData))
                                        <div class="profile-item">
                                            <i class="fas fa-chart-line me-2 text-warning"></i>
                                            <strong>IMC actuel:</strong> {{ $userImcData['imc'] }} - {{ $userImcData['category'] }}
                                        </div>
                                    @endif
                                @else
                                    <div class="profile-item">
                                        <i class="fas fa-info-circle me-2 text-muted"></i>
                                        <em>Aucune donnée de profil disponible. Calculez votre IMC pour commencer !</em>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endauth

                        <!-- Formulaire de calcul -->
                        <form id="imcForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="weight" class="form-label">
                                            <i class="fas fa-weight me-2"></i>Poids (kg)
                                        </label>
                                        <div class="input-group-modern">
                                            <input type="number" class="form-control form-control-modern" id="weight" name="weight" 
                                                   min="20" max="300" step="0.1" required
                                                   value="{{ $userProfileData['weight'] ?? 70 }}"
                                                   placeholder="Entrez votre poids en kilogrammes">
                                            <i class="fas fa-weight-hanging input-icon"></i>
                                        </div>
                                        <small class="form-text text-muted">Entre 20 et 300 kg</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="height" class="form-label">
                                            <i class="fas fa-ruler-vertical me-2"></i>Taille (cm)
                                        </label>
                                        <div class="input-group-modern">
                                            <input type="number" class="form-control form-control-modern" id="height" name="height" 
                                                   min="100" max="250" required
                                                   value="{{ $userProfileData['height'] ?? 175 }}"
                                                   placeholder="Entrez votre taille en centimètres">
                                            <i class="fas fa-ruler input-icon"></i>
                                        </div>
                                        <small class="form-text text-muted">Entre 100 et 250 cm</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="btn-group-modern">
                                <button type="submit" class="btn btn-modern btn-primary-modern">
                                    <i class="fas fa-calculator me-2"></i>Calculer IMC
                                </button>
                                @auth
                                <button type="button" class="btn btn-modern btn-success-modern" id="saveToProfileBtn" style="display: none;">
                                    <i class="fas fa-save me-2"></i>Sauvegarder dans mon profil
                                </button>
                                @endauth
                            </div>
                        </form>

                        <!-- Loading spinner -->
                        <div id="loading" class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Calcul en cours...</span>
                            </div>
                            <p class="mt-3">🧮 Calcul de votre IMC en cours...</p>
                        </div>

                        <!-- Zone des résultats -->
                        <div id="results" class="results-container" style="display: none;">
                            <!-- Les résultats seront affichés ici dynamiquement -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Variable globale pour stocker les résultats actuels
    let currentImcData = null;

    // Configuration CSRF pour toutes les requêtes AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Gestion de la soumission du formulaire
    $('#imcForm').on('submit', function(e) {
        e.preventDefault();
        
        const weight = parseFloat($('#weight').val());
        const height = parseFloat($('#height').val());
        
        // Validation des données
        if (!weight || !height) {
            showAlert('Veuillez remplir tous les champs', 'warning');
            return;
        }
        
        if (weight < 20 || weight > 300) {
            showAlert('Le poids doit être entre 20 et 300 kg', 'warning');
            return;
        }
        
        if (height < 100 || height > 250) {
            showAlert('La taille doit être entre 100 et 250 cm', 'warning');
            return;
        }
        
        // Afficher le loading
        $('#loading').show();
        $('#results').hide();
        
        // Requête AJAX pour calculer l'IMC
        $.ajax({
            url: '/api/imc/calculate',
            method: 'POST',
            data: { 
                weight: weight, 
                height: height 
            },
            success: function(data) {
                $('#loading').hide();
                currentImcData = data; // Stocker les données pour le PDF et la sauvegarde
                displayResults(data);
                $('#saveToProfileBtn').show();
                
                // Notification de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Calcul terminé !',
                    text: 'Votre IMC a été calculé avec succès',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                $('#loading').hide();
                console.error('Erreur AJAX:', xhr);
                
                let errorMessage = 'Erreur lors du calcul de l\'IMC';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 404) {
                    errorMessage = 'Service de calcul IMC non disponible';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erreur serveur lors du calcul';
                }
                
                showAlert(errorMessage, 'danger');
            }
        });
    });

    // Gestion de la sauvegarde dans le profil
    $('#saveToProfileBtn').on('click', function() {
        const weight = parseFloat($('#weight').val());
        const height = parseFloat($('#height').val());
        
        // Animation du bouton
        $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...');
        $(this).prop('disabled', true);
        
        $.ajax({
            url: '/api/imc/save-profile',
            method: 'POST',
            data: { 
                weight: weight, 
                height: height 
            },
            success: function(response) {
                Swal.fire({
                    title: 'Succès !',
                    text: 'Vos données IMC ont été sauvegardées dans votre profil !',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Recharger la page pour mettre à jour le profil
                    location.reload();
                });
            },
            error: function(xhr) {
                console.error('Erreur AJAX sauvegarde:', xhr);
                
                let errorMessage = 'Erreur lors de la sauvegarde';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 401) {
                    errorMessage = 'Vous devez être connecté pour sauvegarder';
                }
                
                showAlert(errorMessage, 'danger');
                
                // Restaurer le bouton
                $('#saveToProfileBtn').html('<i class="fas fa-save me-2"></i>Sauvegarder dans mon profil');
                $('#saveToProfileBtn').prop('disabled', false);
            }
        });
    });

    // Fonction pour afficher les résultats
    function displayResults(data) {
        const colorClass = data.advice.color === 'success' ? 'success' : 
                          data.advice.color === 'warning' ? 'warning' : 'danger';
        
        let recommendations = '';
        data.advice.recommendations.forEach(function(rec) {
            recommendations += `<li>${rec}</li>`;
        });

        const html = `
            <div class="result-card result-${colorClass} fade-in-up">
                <div class="imc-display">
                    <h1 class="imc-number">${data.imc}</h1>
                    <p class="imc-category">${data.category}</p>
                </div>
                
                <div class="text-center mb-3">
                    <p class="fs-5">${data.advice.message}</p>
                </div>
                
                <div class="recommendations">
                    <h6><i class="fas fa-lightbulb me-2"></i>Recommandations personnalisées:</h6>
                    <ul>${recommendations}</ul>
                </div>
                
                <div class="ideal-weight">
                    <strong><i class="fas fa-target me-2"></i>Poids idéal recommandé: ${data.ideal_weight_min} - ${data.ideal_weight_max} kg</strong>
                </div>

                <div class="result-actions">
                    @auth
                    <button type="button" class="btn btn-modern btn-success-modern" id="saveToProfileBtn2">
                        <i class="fas fa-save me-2"></i>Sauvegarder
                    </button>
                    @endauth
                    <button type="button" class="btn btn-modern btn-pdf-modern" onclick="downloadPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Télécharger PDF
                    </button>
                    <button type="button" class="btn btn-modern btn-primary-modern" onclick="shareResults()">
                        <i class="fas fa-share-alt me-2"></i>Partager
                    </button>
                </div>
            </div>
        `;
        
        $('#results').html(html).fadeIn('slow');

        // Event listener pour le bouton sauvegarder dans les résultats
        $('#saveToProfileBtn2').on('click', function() {
            $('#saveToProfileBtn').click();
        });
    }

    // Fonction pour télécharger le PDF
    function downloadPDF() {
        if (!currentImcData) {
            showAlert('Aucune donnée à exporter', 'warning');
            return;
        }

        // Afficher une notification de génération
        Swal.fire({
            title: '📄 Génération du PDF...',
            text: 'Création de votre rapport IMC personnalisé en cours',
            icon: 'info',
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            generatePDFReport();
        });
    }

    // Fonction pour générer le rapport PDF complet
    function generatePDFReport() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Configuration
        const pageWidth = doc.internal.pageSize.width;
        const margin = 20;
        let yPos = 30;

        // En-tête du document
        doc.setFontSize(24);
        doc.setTextColor(102, 126, 234);
        doc.text('Rapport d\'Analyse IMC', pageWidth / 2, yPos, { align: 'center' });

        yPos += 20;

        // Sous-titre
        doc.setFontSize(14);
        doc.setTextColor(118, 75, 162);
        doc.text('SmartHealth - Calculateur IMC Professionnel', pageWidth / 2, yPos, { align: 'center' });

        yPos += 20;

        // Date de génération
        doc.setFontSize(10);
        doc.setTextColor(100, 100, 100);
        const currentDate = new Date().toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        doc.text('Rapport généré le: ' + currentDate, pageWidth / 2, yPos, { align: 'center' });

        yPos += 30;

        // Informations utilisateur (si connecté)
        @auth
        doc.setFontSize(16);
        doc.setTextColor(0, 0, 0);
        doc.text('Informations du patient:', margin, yPos);
        yPos += 10;

        doc.setFontSize(12);
        doc.text('Nom: {{ Auth::user()->name }}', margin, yPos);
        yPos += 8;
        doc.text('Email: {{ Auth::user()->email }}', margin, yPos);
        yPos += 8;
        doc.text('Poids: ' + currentImcData.weight + ' kg', margin, yPos);
        yPos += 8;
        doc.text('Taille: ' + currentImcData.height + ' cm', margin, yPos);
        yPos += 8;
        doc.text('Date du calcul: ' + currentImcData.calculated_at, margin, yPos);

        yPos += 20;
        @endauth

        // Résultats principaux
        doc.setFontSize(16);
        doc.setTextColor(0, 0, 0);
        doc.text('Résultats de l\'analyse:', margin, yPos);
        yPos += 15;

        // IMC principal avec couleur
        doc.setFontSize(20);
        const imcColor = currentImcData.advice.color === 'success' ? [79, 172, 254] : 
                       currentImcData.advice.color === 'warning' ? [240, 147, 251] : [255, 154, 158];
        doc.setTextColor(imcColor[0], imcColor[1], imcColor[2]);
        doc.text('IMC: ' + currentImcData.imc, margin, yPos);
        yPos += 12;

        doc.setFontSize(14);
        doc.text('Catégorie: ' + currentImcData.category, margin, yPos);
        yPos += 20;

        // Interprétation
        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0);
        doc.text('Interprétation:', margin, yPos);
        yPos += 8;

        // Découper le texte du message pour qu'il s'adapte à la largeur
        const splitMessage = doc.splitTextToSize(currentImcData.advice.message, pageWidth - 2 * margin);
        doc.text(splitMessage, margin, yPos);
        yPos += splitMessage.length * 6 + 10;

        // Poids idéal
        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0);
        doc.text('Poids idéal recommandé:', margin, yPos);
        yPos += 8;
        doc.setTextColor(79, 172, 254);
        doc.text(currentImcData.ideal_weight_min + ' - ' + currentImcData.ideal_weight_max + ' kg', margin, yPos);
        yPos += 15;

        // Recommandations détaillées
        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0);
        doc.text('Recommandations personnalisées:', margin, yPos);
        yPos += 8;

        currentImcData.advice.recommendations.forEach((rec, index) => {
            const recommendation = (index + 1) + '. ' + rec;
            const splitRec = doc.splitTextToSize(recommendation, pageWidth - 2 * margin - 10);
            doc.text(splitRec, margin + 5, yPos);
            yPos += splitRec.length * 6 + 3;
        });

        yPos += 20;

        // Échelle IMC de référence
        doc.setFontSize(14);
        doc.setTextColor(0, 0, 0);
        doc.text('Échelle de référence IMC:', margin, yPos);
        yPos += 10;

        doc.setFontSize(10);
        const imcScale = [
            'Sous-poids: < 18.5',
            'Poids normal: 18.5 - 24.9',
            'Surpoids: 25.0 - 29.9',
            'Obésité classe I: 30.0 - 34.9',
            'Obésité classe II: 35.0 - 39.9',
            'Obésité classe III: ≥ 40.0'
        ];

        imcScale.forEach(scale => {
            doc.text('• ' + scale, margin + 5, yPos);
            yPos += 6;
        });

        yPos += 20;

        // Avertissement médical
        doc.setFontSize(10);
        doc.setTextColor(200, 0, 0);
        const disclaimer = "AVERTISSEMENT MÉDICAL: Ce rapport est généré à titre informatif uniquement et ne remplace en aucun cas un avis médical professionnel. L'IMC est un indicateur général qui ne prend pas en compte la composition corporelle (masse musculaire, osseuse, etc.). Consultez toujours votre médecin ou un professionnel de santé qualifié pour un suivi personnalisé et des conseils adaptés à votre situation.";
        const splitDisclaimer = doc.splitTextToSize(disclaimer, pageWidth - 2 * margin);
        doc.text(splitDisclaimer, margin, yPos);

        // Pied de page
        doc.setFontSize(8);
        doc.setTextColor(100, 100, 100);
        doc.text('SmartHealth - Votre partenaire santé numérique', pageWidth / 2, doc.internal.pageSize.height - 15, { align: 'center' });
        doc.text('Généré automatiquement par le système SmartHealth', pageWidth / 2, doc.internal.pageSize.height - 10, { align: 'center' });

        // Télécharger le fichier
        const fileName = 'rapport_imc_' + new Date().toISOString().slice(0, 10) + '_' + new Date().getTime() + '.pdf';
        doc.save(fileName);

        // Notification de succès
        Swal.fire({
            title: 'PDF généré !',
            text: 'Votre rapport IMC a été téléchargé avec succès',
            icon: 'success',
            confirmButtonText: 'Parfait !'
        });
    }

    // Fonction pour partager les résultats
    function shareResults() {
        if (!currentImcData) {
            showAlert('Aucune donnée à partager', 'warning');
            return;
        }

        const shareText = `Mon IMC: ${currentImcData.imc} (${currentImcData.category})\nCalculé avec SmartHealth 💪`;
        
        if (navigator.share) {
            // API Web Share (mobile)
            navigator.share({
                title: 'Mon résultat IMC - SmartHealth',
                text: shareText,
                url: window.location.href
            }).catch(console.error);
        } else {
            // Fallback: copier dans le presse-papiers
            navigator.clipboard.writeText(shareText).then(() => {
                Swal.fire({
                    title: 'Copié !',
                    text: 'Le résultat a été copié dans votre presse-papiers',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(() => {
                showAlert('Impossible de copier le texte', 'warning');
            });
        }
    }

    // Fonction pour afficher les alertes avec SweetAlert2
    function showAlert(message, type) {
        const icon = type === 'success' ? 'success' : 
                    type === 'warning' ? 'warning' : 
                    type === 'danger' ? 'error' : 'info';
        
        Swal.fire({
            title: type === 'success' ? 'Succès !' : 
                  type === 'warning' ? 'Attention !' : 
                  type === 'danger' ? 'Erreur !' : 'Information',
            text: message,
            icon: icon,
            confirmButtonText: 'OK'
        });
    }

    // Animations et effets visuels
    $('.form-control-modern').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });

    // Validation en temps réel
    $('#weight, #height').on('input', function() {
        const weight = parseFloat($('#weight').val());
        const height = parseFloat($('#height').val());
        
        if (weight && height) {
            // Calcul rapide pour prévisualisation
            const heightM = height / 100;
            const imc = (weight / (heightM * heightM)).toFixed(1);
            
            if (imc > 0 && imc < 100) {
                $(this).addClass('is-valid').removeClass('is-invalid');
            }
        }
    });

    // Message d'aide contextuel
    $('#weight, #height').on('focus', function() {
        const fieldName = $(this).attr('name');
        let helpText = '';
        
        if (fieldName === 'weight') {
            helpText = '💡 Pesez-vous le matin, à jeun, sans vêtements lourds pour plus de précision.';
        } else {
            helpText = '📏 Mesurez-vous pieds nus, dos droit contre un mur.';
        }
        
        if (!$('.help-tooltip').length) {
            $('<div class="help-tooltip alert alert-info mt-2">' + helpText + '</div>')
                .insertAfter($(this).closest('.form-group'))
                .fadeIn();
        }
    }).on('blur', function() {
        $('.help-tooltip').fadeOut(function() {
            $(this).remove();
        });
    });

    // Animation de la page au chargement
    $(document).ready(function() {
        $('.fade-in-up').addClass('animate__animated animate__fadeInUp');
        
        // Focus automatique sur le premier champ si vide
        if (!$('#weight').val()) {
            setTimeout(() => $('#weight').focus(), 500);
        }
    });
</script>
@endsection
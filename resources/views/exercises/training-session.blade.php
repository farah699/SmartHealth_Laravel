{{-- filepath: c:\Users\ferie\OneDrive\Bureau\projetLaravel\SmartHealth_Laravel\resources\views\exercises\training-session.blade.php --}}

@extends('partials.layouts.master')

@section('title', 'Entraînement en cours | SmartHealth')
@section('title-sub', 'Activités Sportives')
@section('pagetitle', 'Entraînement en cours')

@section('css')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
.header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.breadcrumb-item a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: white;
}

.avatar-lg {
    width: 60px;
    height: 60px;
}

.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
    border-radius: 15px;
}

.progress {
    border-radius: 10px;
}

.display-1 {
    font-family: 'Courier New', monospace;
}

#heartRateDisplay {
    animation: heartbeat 1.5s ease-in-out infinite;
}

@keyframes heartbeat {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

.timer-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #667eea;
}

.stats-card {
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
}

.controls-card {
    background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
}

/* 🎮 SYSTÈME DE GAMIFICATION */
.gamification-panel {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    border-radius: 15px;
    border: 2px solid #ff9a9e;
}

.achievement-badge {
    transition: all 0.3s ease;
    cursor: pointer;
}

.achievement-badge:hover {
    transform: scale(1.1);
}

.achievement-unlocked {
    animation: bounceIn 0.6s ease-out;
}

@keyframes bounceIn {
    0%, 20%, 40%, 60%, 80% {
        animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
    }
    0% {
        opacity: 0;
        transform: scale3d(.3, .3, .3);
    }
    20% {
        transform: scale3d(1.1, 1.1, 1.1);
    }
    40% {
        transform: scale3d(.9, .9, .9);
    }
    60% {
        opacity: 1;
        transform: scale3d(1.03, 1.03, 1.03);
    }
    80% {
        transform: scale3d(.97, .97, .97);
    }
    100% {
        opacity: 1;
        transform: scale3d(1, 1, 1);
    }
}

.xp-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    height: 20px;
    position: relative;
    overflow: hidden;
}

.xp-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shine 2s infinite;
}

@keyframes shine {
    0% { left: -100%; }
    100% { left: 100%; }
}

.streak-flame {
    animation: flicker 1.5s ease-in-out infinite alternate;
}

@keyframes flicker {
    0% { transform: scale(1) rotate(-1deg); }
    100% { transform: scale(1.1) rotate(1deg); }
}

.challenge-card {
    background: linear-gradient(135deg, #e0c3fc 0%, #9bb5ff 100%);
    border: 2px solid #667eea;
    transition: all 0.3s ease;
}

.challenge-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.level-badge {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4a 100%);
    border: 3px solid #f39c12;
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
    0% { box-shadow: 0 0 5px #ffd700; }
    100% { box-shadow: 0 0 20px #ffd700, 0 0 30px #ffd700; }
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
                                    <i class="fas fa-running text-white fs-3"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 text-white">🏃‍♂️ {{ $exercise['exercise_name'] }}</h4>
                                    <p class="text-white-50 mb-0">Entraînement en cours • Type: {{ ucfirst($exercise['exercise_type']) }}</p>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0 mt-2">
                                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                                            <li class="breadcrumb-item"><a href="{{ route('exercises.recommendations') }}">Recommandations</a></li>
                                            <li class="breadcrumb-item active">Entraînement</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button id="stopBtn" class="btn btn-danger btn-lg">
                                    <i class="fas fa-stop me-2"></i>
                                    Arrêter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🎮 PANNEAU DE GAMIFICATION -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card gamification-panel">
                    <div class="card-header bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy text-warning me-2"></i>🎮 Panneau Gamification
                            </h5>
                            <div class="d-flex align-items-center">
                                <span class="level-badge badge rounded-pill px-3 py-2 fs-6 fw-bold">
                                    <i class="fas fa-star me-1"></i>Niveau <span id="playerLevel">1</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- XP et Niveau -->
                            <div class="col-lg-4">
                                <h6><i class="fas fa-chart-line me-2"></i>🌟 Expérience</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>XP: <span id="currentXP">0</span>/<span id="nextLevelXP">100</span></span>
                                    <span><span id="xpGained">0</span> XP gagnés</span>
                                </div>
                                <div class="progress mb-3" style="height: 15px;">
                                    <div id="xpProgress" class="xp-bar" style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Série actuelle -->
                            <div class="col-lg-4">
                                <h6><i class="fas fa-fire me-2"></i>🔥 Série d'entraînements</h6>
                                <div class="text-center">
                                    <div class="h2 text-danger streak-flame" id="currentStreak">0</div>
                                    <small class="text-muted">jours consécutifs</small>
                                </div>
                            </div>

                            <!-- Achievements débloqués -->
                            <div class="col-lg-4">
                                <h6><i class="fas fa-medal me-2"></i>🏆 Achievements Récents</h6>
                                <div id="recentAchievements" class="d-flex flex-wrap gap-2">
                                    <!-- Les achievements seront ajoutés dynamiquement -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Défis Actifs -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <!-- Panneau de contrôle principal -->
                <div class="row">
                    <!-- Timer principal -->
                    <div class="col-lg-4">
                        <div class="card h-100 timer-card">
                            <div class="card-body text-center">
                                <h5 class="card-title">
                                    <i class="fas fa-stopwatch me-2"></i>⏱️ Temps d'entraînement
                                </h5>
                                <div id="mainTimer" class="display-1 text-primary fw-bold mb-3">00:00</div>
                                <div class="d-flex justify-content-center gap-2">
                                    <button id="playPauseBtn" class="btn btn-success btn-lg">
                                        <i class="fas fa-play"></i>
                                        <span>Démarrer</span>
                                    </button>
                                    <button id="resetBtn" class="btn btn-warning btn-lg">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <kbd>Espace</kbd> Pause | <kbd>R</kbd> Reset | <kbd>S</kbd> Stop
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Calories brûlées -->
                    <div class="col-lg-4">
                        <div class="card h-100 stats-card">
                            <div class="card-body text-center">
                                <h5 class="card-title">
                                    <i class="fas fa-fire me-2"></i>🔥 Calories brûlées
                                </h5>
                                <div id="caloriesDisplay" class="display-2 text-danger fw-bold mb-3">0</div>
                                <small class="text-muted">{{ $exercise['calories_per_minute'] }} cal/min</small>
                                <div class="progress mt-3" style="height: 8px;">
                                    <div id="caloriesProgress" class="progress-bar bg-danger" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="col-lg-4">
                        <div class="card h-100 controls-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-chart-line me-2"></i>📊 Statistiques
                                </h5>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="h4 mb-1" id="avgHeartRate">--</div>
                                            <small class="text-muted">BPM moyen</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="h4 mb-1">{{ $exercise['difficulty_level'] }}/10</div>
                                            <small class="text-muted">Difficulté</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="h4 mb-1" id="progress">0%</div>
                                            <small class="text-muted">Progression</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="h4 mb-1">{{ $exercise['recommended_duration'] }}</div>
                                            <small class="text-muted">Durée cible (min)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🎯 DÉFIS ACTIFS -->
            <div class="col-lg-4">
                <div class="card challenge-card h-100">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0">
                            <i class="fas fa-target me-2"></i>🎯 Défis Actifs
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="activeChallenges">
                            <!-- Les défis seront ajoutés dynamiquement -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre de progression -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">
                                <i class="fas fa-tasks me-2"></i>Progression de l'entraînement
                            </h6>
                            <span id="progressText">0% terminé</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="progressBar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contrôles avancés -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-bullhorn me-2"></i>🎵 Motivation
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="motivationMessage" class="alert alert-info text-center">
                            🚀 Prêt à commencer votre entraînement ? C'est parti !
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-heartbeat me-2"></i>⚡ Rythme cardiaque simulé
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div id="heartRateDisplay" class="h2 text-danger">
                            <i class="fas fa-heart"></i>
                            <span id="heartRateValue">--</span> BPM
                        </div>
                        <div class="mt-2">
                            <small class="badge bg-secondary" id="heartRateZone">Au repos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de fin d'entraînement avec gamification -->
<div class="modal fade" id="completionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trophy me-2"></i>🎉 Entraînement terminé !
                </h5>
            </div>
            <div class="modal-body">
                <div class="row text-center mb-4">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <div class="h2 text-primary" id="finalTime">00:00</div>
                            <small class="text-muted">Temps total</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <div class="h2 text-danger" id="finalCalories">0</div>
                            <small class="text-muted">Calories brûlées</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <div class="h2 text-warning" id="finalXP">0</div>
                            <small class="text-muted">XP gagnés</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <div class="h2 text-success" id="finalProgress">0%</div>
                            <small class="text-muted">Objectif atteint</small>
                        </div>
                    </div>
                </div>
                
                <!-- Achievements débloqués -->
                <div id="unlockedAchievements" style="display: none;">
                    <h5 class="text-center mb-3">🏆 Nouveaux Achievements Débloqués !</h5>
                    <div id="achievementsList" class="row justify-content-center">
                        <!-- Les achievements seront ajoutés ici -->
                    </div>
                </div>
                
                <div class="alert alert-success text-center">
                    <h5>
                        <i class="fas fa-star me-2"></i>Excellent travail ! 💪
                    </h5>
                    <p class="mb-0">Votre entraînement a été sauvegardé dans votre historique d'activités.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('exercises.recommendations') }}'">
                    <i class="fas fa-redo me-2"></i>Nouvelle recommandation
                </button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('dashboard') }}'">
                    <i class="fas fa-home me-2"></i>Retour au tableau de bord
                </button>
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
class TrainingSession {
    constructor() {
        this.isRunning = false;
        this.isPaused = false;
        this.totalSeconds = 0;
        this.targetDuration = {{ $exercise['recommended_duration'] }};
        this.caloriesPerMinute = {{ $exercise['calories_per_minute'] }};
        this.timer = null;
        this.heartRateTimer = null;
        this.baseHeartRate = 70;
        this.currentHeartRate = this.baseHeartRate;
        this.heartRateHistory = [];
        
        // 🎮 SYSTÈME DE GAMIFICATION
        this.gamification = {
            currentXP: 0,
            currentLevel: 1,
            xpToNextLevel: 100,
            xpGained: 0,
            currentStreak: 3, // Simulation
            achievements: [],
            challenges: []
        };
        
        this.initializeElements();
        this.bindEvents();
        this.initializeGamification();
        this.startMotivationMessages();
        this.showWelcomeMessage();
    }
    
    initializeElements() {
        this.mainTimer = document.getElementById('mainTimer');
        this.playPauseBtn = document.getElementById('playPauseBtn');
        this.resetBtn = document.getElementById('resetBtn');
        this.stopBtn = document.getElementById('stopBtn');
        this.caloriesDisplay = document.getElementById('caloriesDisplay');
        this.caloriesProgress = document.getElementById('caloriesProgress');
        this.progressBar = document.getElementById('progressBar');
        this.progressText = document.getElementById('progressText');
        this.progress = document.getElementById('progress');
        this.heartRateValue = document.getElementById('heartRateValue');
        this.heartRateZone = document.getElementById('heartRateZone');
        this.avgHeartRate = document.getElementById('avgHeartRate');
        this.motivationMessage = document.getElementById('motivationMessage');
        
        // 🎮 Éléments de gamification
        this.playerLevel = document.getElementById('playerLevel');
        this.currentXPElement = document.getElementById('currentXP');
        this.nextLevelXP = document.getElementById('nextLevelXP');
        this.xpGainedElement = document.getElementById('xpGained');
        this.xpProgress = document.getElementById('xpProgress');
        this.currentStreakElement = document.getElementById('currentStreak');
        this.recentAchievements = document.getElementById('recentAchievements');
        this.activeChallenges = document.getElementById('activeChallenges');
    }
    
    // 🎮 INITIALISATION DU SYSTÈME DE GAMIFICATION
    initializeGamification() {
        this.updateGamificationDisplay();
        this.initializeChallenges();
        this.loadUserStats();
    }
    
    initializeChallenges() {
        const challenges = [
            {
                id: 1,
                title: "🔥 Brûleur de Calories",
                description: "Brûlez 100 calories",
                target: 10,
                current: 0,
                type: "calories",
                reward: 50,
                icon: "fas fa-fire"
            },
            {
                id: 2,
                title: "⏰ Endurance",
                description: "Entraînez-vous 15 minutes",
                target: 1,
                current: 0,
                type: "duration",
                reward: 30,
                icon: "fas fa-clock"
            },
            {
                id: 3,
                title: "💓 Cardio Master",
                description: "Maintenez FC > 130 BPM pendant 5 min",
                target: 1,
                current: 0,
                type: "heartrate",
                reward: 75,
                icon: "fas fa-heartbeat"
            }
        ];
        
        this.gamification.challenges = challenges;
        this.displayChallenges();
    }
    
    displayChallenges() {
        this.activeChallenges.innerHTML = '';
        
        this.gamification.challenges.forEach(challenge => {
            const progressPercent = (challenge.current / challenge.target) * 100;
            const challengeHtml = `
                <div class="challenge-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="mb-0"><i class="${challenge.icon} me-1"></i>${challenge.title}</h6>
                        <span class="badge bg-warning">+${challenge.reward} XP</span>
                    </div>
                    <p class="small text-muted mb-2">${challenge.description}</p>
                    <div class="progress mb-1" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: ${progressPercent}%"></div>
                    </div>
                    <small class="text-muted">${challenge.current}/${challenge.target}</small>
                </div>
            `;
            this.activeChallenges.innerHTML += challengeHtml;
        });
    }
    
    updateGamificationDisplay() {
        this.playerLevel.textContent = this.gamification.currentLevel;
        this.currentXPElement.textContent = this.gamification.currentXP;
        this.nextLevelXP.textContent = this.gamification.xpToNextLevel;
        this.xpGainedElement.textContent = this.gamification.xpGained;
        this.currentStreakElement.textContent = this.gamification.currentStreak;
        
        const xpPercent = (this.gamification.currentXP / this.gamification.xpToNextLevel) * 100;
        this.xpProgress.style.width = `${xpPercent}%`;
    }
    
    loadUserStats() {
        // Simulation des stats utilisateur
        this.gamification.currentXP = 250;
        this.gamification.currentLevel = 3;
        this.gamification.xpToNextLevel = 400;
        this.updateGamificationDisplay();
    }
    
    // 🎮 GAIN D'XP ET ACHIEVEMENTS
    gainXP(amount, reason) {
        this.gamification.xpGained += amount;
        this.gamification.currentXP += amount;
        
        // Vérifier si le joueur monte de niveau
        if (this.gamification.currentXP >= this.gamification.xpToNextLevel) {
            this.levelUp();
        }
        
        this.updateGamificationDisplay();
        this.showXPGain(amount, reason);
    }
    
    levelUp() {
        this.gamification.currentLevel++;
        this.gamification.currentXP = 0;
        this.gamification.xpToNextLevel = this.gamification.currentLevel * 100;
        
        Swal.fire({
            title: '🎉 NIVEAU SUPÉRIEUR !',
            html: `
                <div class="text-center">
                    <div class="h1 text-warning mb-3">
                        <i class="fas fa-star"></i>
                        Niveau ${this.gamification.currentLevel}
                    </div>
                    <p>Félicitations ! Vous avez atteint le niveau ${this.gamification.currentLevel} !</p>
                    <div class="badge bg-success fs-6 px-3 py-2">+100 XP Bonus</div>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'Continuer !',
            timer: 5000
        });
        
        // Bonus XP pour le niveau
        this.gamification.xpGained += 100;
    }
    
    showXPGain(amount, reason) {
        const xpNotification = document.createElement('div');
        xpNotification.innerHTML = `
            <div class="position-fixed" style="top: 100px; right: 20px; z-index: 9999;">
                <div class="alert alert-warning alert-dismissible fade show">
                    <strong>+${amount} XP</strong> ${reason}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        `;
        document.body.appendChild(xpNotification);
        
        setTimeout(() => {
            xpNotification.remove();
        }, 3000);
    }
    
    // 🎮 VÉRIFICATION DES ACHIEVEMENTS
    checkAchievements() {
        const achievements = [
            {
                id: 'first_workout',
                title: '🥇 Premier Entraînement',
                description: 'Commencer votre premier entraînement',
condition: () => this.totalSeconds > 10,        // 10 secondes
                xp: 25
            },
            {
                id: 'calorie_burner',
                title: '🔥 Brûleur de Calories',
                description: 'Brûler 50 calories en une session',
                condition: () => this.getCaloriesBurned() >= 10,
                xp: 50
            },
            {
                id: 'time_keeper',
                title: '⏰ Gardien du Temps',
                description: 'S\'entraîner pendant 10 minutes',
condition: () => this.totalSeconds >= 30,       // 30 secondes  
                xp: 40
            },
            {
                id: 'heart_warrior',
                title: '💓 Guerrier du Cœur',
                description: 'Maintenir FC > 130 pendant 3 minutes',
condition: () => this.heartRateHistory.filter(hr => hr > 130).length >= 5, // 10 secondes                xp: 60
            },
            {
                id: 'goal_crusher',
                title: '🎯 Briseur d\'Objectifs',
                description: 'Atteindre 100% de l\'objectif',
                condition: () => this.getProgressPercent() >= 100,
                xp: 100
            }
        ];
        
        achievements.forEach(achievement => {
            if (!this.gamification.achievements.includes(achievement.id) && achievement.condition()) {
                this.unlockAchievement(achievement);
            }
        });
    }
    
    unlockAchievement(achievement) {
        this.gamification.achievements.push(achievement.id);
        
        // Ajouter l'achievement aux récents
        const achievementBadge = document.createElement('div');
        achievementBadge.innerHTML = `
            <span class="badge bg-warning achievement-badge achievement-unlocked" 
                  title="${achievement.description}" data-bs-toggle="tooltip">
                ${achievement.title}
            </span>
        `;
        this.recentAchievements.appendChild(achievementBadge);
        
        // Gagner XP
        this.gainXP(achievement.xp, `Achievement: ${achievement.title}`);
        
        // Notification
        Swal.fire({
            title: '🏆 ACHIEVEMENT DÉBLOQUÉ !',
            html: `
                <div class="text-center">
                    <div class="h2 text-warning mb-3">${achievement.title}</div>
                    <p>${achievement.description}</p>
                    <div class="badge bg-success fs-6 px-3 py-2">+${achievement.xp} XP</div>
                </div>
            `,
            icon: 'success',
            timer: 4000,
            showConfirmButton: false
        });
    }
    
    // 🎮 MISE À JOUR DES DÉFIS
    updateChallenges() {
        let challengesCompleted = 0;
        
        this.gamification.challenges.forEach(challenge => {
            let newCurrent = challenge.current;
            
            switch(challenge.type) {
                case 'calories':
                    newCurrent = this.getCaloriesBurned();
                    break;
                case 'duration':
                    newCurrent = Math.floor(this.totalSeconds / 60);
                    break;
                case 'heartrate':
                    const highHRCount = this.heartRateHistory.filter(hr => hr > 130).length;
                    newCurrent = Math.floor(highHRCount / 60); // Convertir en minutes
                    break;
            }
            
            if (newCurrent !== challenge.current) {
                challenge.current = Math.min(newCurrent, challenge.target);
                
                // Vérifier si le défi est terminé
                if (challenge.current >= challenge.target && !challenge.completed) {
                    challenge.completed = true;
                    challengesCompleted++;
                    this.gainXP(challenge.reward, `Défi: ${challenge.title}`);
                }
            }
        });
        
        if (challengesCompleted > 0) {
            this.displayChallenges();
        }
    }
    
    bindEvents() {
        this.playPauseBtn.addEventListener('click', () => this.toggleTimer());
        this.resetBtn.addEventListener('click', () => this.resetTimer());
        this.stopBtn.addEventListener('click', () => this.stopTraining());
        
        // Raccourcis clavier
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                this.toggleTimer();
            } else if (e.code === 'KeyR') {
                this.resetTimer();
            } else if (e.code === 'KeyS') {
                this.stopTraining();
            }
        });
    }
    
    showWelcomeMessage() {
        setTimeout(() => {
            Swal.fire({
                title: '🏃‍♂️ Prêt pour l\'entraînement ?',
                html: `
                    <div class="text-center">
                        <h5>{{ $exercise['exercise_name'] }}</h5>
                        <p><strong>Durée recommandée:</strong> {{ $exercise['recommended_duration'] }} minutes</p>
                        <p><strong>Difficulté:</strong> {{ $exercise['difficulty_level'] }}/10</p>
                        <hr>
                        <div class="alert alert-info">
                            <h6>🎮 Système de Gamification Activé !</h6>
                            <small>Gagnez de l'XP, débloquez des achievements et relevez des défis !</small>
                        </div>
                        <hr>
                        <p><strong>Raccourcis clavier:</strong></p>
                        <small>
                            <kbd>Espace</kbd> Démarrer/Pause |
                            <kbd>R</kbd> Reset |
                            <kbd>S</kbd> Stop
                        </small>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Commencer l\'entraînement !',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.startTimer();
                }
            });
        }, 1000);
    }
    
    toggleTimer() {
        if (!this.isRunning) {
            this.startTimer();
        } else {
            this.pauseTimer();
        }
    }
    
    startTimer() {
        this.isRunning = true;
        this.isPaused = false;
        
        this.timer = setInterval(() => {
            this.totalSeconds++;
            this.updateDisplay();
            this.updateProgress();
            this.updateCalories();
            
            // 🎮 Mise à jour gamification
            if (this.totalSeconds % 2 === 0) { // Toutes les 2 secondes
                this.gainXP(1, 'Entraînement actif');
                this.checkAchievements();
                this.updateChallenges();
            }
        }, 1000);
        
        this.startHeartRateSimulation();
        this.updatePlayPauseButton();
        
        // Notification de démarrage
        if (this.totalSeconds === 0) {
            Swal.fire({
                title: 'C\'est parti ! 🚀',
                text: 'Votre entraînement a commencé',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    }
    
    pauseTimer() {
        this.isRunning = false;
        this.isPaused = true;
        
        clearInterval(this.timer);
        clearInterval(this.heartRateTimer);
        this.updatePlayPauseButton();
        
        // Notification de pause
        Swal.fire({
            title: 'Pause ⏸️',
            text: 'Entraînement en pause',
            icon: 'warning',
            timer: 1000,
            showConfirmButton: false
        });
    }
    
    resetTimer() {
        // Demander confirmation
        Swal.fire({
            title: 'Remettre à zéro ?',
            text: 'Voulez-vous vraiment recommencer l\'entraînement ? Vous perdrez votre progression actuelle.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, recommencer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                this.isRunning = false;
                this.isPaused = false;
                this.totalSeconds = 0;
                this.heartRateHistory = [];
                
                // 🎮 Reset gamification session
                this.gamification.xpGained = 0;
                this.gamification.achievements = this.gamification.achievements.filter(a => !a.startsWith('session_'));
                
                clearInterval(this.timer);
                clearInterval(this.heartRateTimer);
                
                this.updateDisplay();
                this.updateProgress();
                this.updateCalories();
                this.updatePlayPauseButton();
                this.resetHeartRate();
                this.updateGamificationDisplay();
                this.initializeChallenges();
                
                Swal.fire({
                    title: 'Remis à zéro !',
                    text: 'Prêt pour un nouvel entraînement',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
    
    stopTraining() {
        if (this.totalSeconds === 0) {
            Swal.fire({
                title: 'Attention !',
                text: 'Vous devez commencer l\'entraînement avant de l\'arrêter !',
                icon: 'warning'
            });
            return;
        }
        
        Swal.fire({
            title: 'Arrêter l\'entraînement ?',
            text: 'Voulez-vous vraiment terminer cet entraînement ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, terminer',
            cancelButtonText: 'Continuer'
        }).then((result) => {
            if (result.isConfirmed) {
                this.pauseTimer();
                this.completeTraining();
            }
        });
    }
    
    updateDisplay() {
        const minutes = Math.floor(this.totalSeconds / 60);
        const seconds = this.totalSeconds % 60;
        this.mainTimer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
    updateProgress() {
        const targetSeconds = this.targetDuration * 60;
        const progressPercent = Math.min((this.totalSeconds / targetSeconds) * 100, 100);
        
        this.progressBar.style.width = `${progressPercent}%`;
        this.progressBar.setAttribute('aria-valuenow', progressPercent);
        this.progressText.textContent = `${Math.round(progressPercent)}% terminé`;
        this.progress.textContent = `${Math.round(progressPercent)}%`;
        
        // Changer la couleur selon la progression
        if (progressPercent >= 100) {
            this.progressBar.className = 'progress-bar bg-success progress-bar-striped';
            if (progressPercent === 100) {
                // Objectif atteint !
                Swal.fire({
                    title: '🎯 Objectif atteint !',
                    text: 'Félicitations ! Vous avez atteint votre objectif de temps.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
                this.gainXP(50, 'Objectif atteint !');
            }
        } else if (progressPercent >= 75) {
            this.progressBar.className = 'progress-bar bg-info progress-bar-striped progress-bar-animated';
        } else if (progressPercent >= 50) {
            this.progressBar.className = 'progress-bar bg-warning progress-bar-striped progress-bar-animated';
        }
    }
    
    getProgressPercent() {
        const targetSeconds = this.targetDuration * 60;
        return Math.min((this.totalSeconds / targetSeconds) * 100, 100);
    }
    
    updateCalories() {
        const minutes = this.totalSeconds / 60;
        const calories = Math.round(minutes * this.caloriesPerMinute);
        this.caloriesDisplay.textContent = calories;
        
        // Progression des calories (basée sur l'objectif de temps)
        const targetCalories = this.targetDuration * this.caloriesPerMinute;
        const caloriesPercent = Math.min((calories / targetCalories) * 100, 100);
        this.caloriesProgress.style.width = `${caloriesPercent}%`;
    }
    
    getCaloriesBurned() {
        const minutes = this.totalSeconds / 60;
        return Math.round(minutes * this.caloriesPerMinute);
    }
    
    updatePlayPauseButton() {
        const icon = this.playPauseBtn.querySelector('i');
        const text = this.playPauseBtn.querySelector('span');
        
        if (this.isRunning) {
            icon.className = 'fas fa-pause';
            text.textContent = 'Pause';
            this.playPauseBtn.className = 'btn btn-warning btn-lg';
        } else {
            icon.className = 'fas fa-play';
            text.textContent = this.isPaused ? 'Reprendre' : 'Démarrer';
            this.playPauseBtn.className = 'btn btn-success btn-lg';
        }
    }
    
    startHeartRateSimulation() {
        // Simuler un rythme cardiaque réaliste pendant l'exercice
        this.heartRateTimer = setInterval(() => {
            if (this.isRunning) {
                // Augmenter progressivement le rythme cardiaque
                const targetHeartRate = this.baseHeartRate + ({{ $exercise['difficulty_level'] }} * 12);
                const variation = Math.random() * 10 - 5; // Variation de ±5 BPM
                
                this.currentHeartRate = Math.round(targetHeartRate + variation);
                this.currentHeartRate = Math.max(60, Math.min(200, this.currentHeartRate));
                
                this.heartRateHistory.push(this.currentHeartRate);
                
                this.heartRateValue.textContent = this.currentHeartRate;
                this.updateHeartRateZone();
                this.updateAverageHeartRate();
            }
        }, 1000); // Mise à jour toutes les 2 secondes
    }
    
    updateHeartRateZone() {
        let zone = 'Au repos';
        let badgeClass = 'bg-secondary';
        
        if (this.currentHeartRate < 100) {
            zone = 'Échauffement';
            badgeClass = 'bg-info';
        } else if (this.currentHeartRate < 130) {
            zone = 'Endurance';
            badgeClass = 'bg-success';
        } else if (this.currentHeartRate < 160) {
            zone = 'Intensif';
            badgeClass = 'bg-warning';
        } else {
            zone = 'Maximum';
            badgeClass = 'bg-danger';
        }
        
        this.heartRateZone.textContent = zone;
        this.heartRateZone.className = `badge ${badgeClass}`;
    }
    
    updateAverageHeartRate() {
        if (this.heartRateHistory.length > 0) {
            const sum = this.heartRateHistory.reduce((a, b) => a + b, 0);
            const avgHR = Math.round(sum / this.heartRateHistory.length);
            this.avgHeartRate.textContent = avgHR;
        }
    }
    
    resetHeartRate() {
        this.currentHeartRate = this.baseHeartRate;
        this.heartRateHistory = [];
        this.heartRateValue.textContent = '--';
        this.heartRateZone.textContent = 'Au repos';
        this.heartRateZone.className = 'badge bg-secondary';
        this.avgHeartRate.textContent = '--';
    }
    
    startMotivationMessages() {
        const messages = [
            "🚀 Prêt à commencer votre entraînement ? C'est parti !",
            "💪 Vous êtes en train de faire quelque chose de formidable !",
            "🔥 Chaque seconde compte, continuez !",
            "⭐ Excellente progression, ne lâchez rien !",
            "🏆 Vous êtes un champion, persévérez !",
            "🎯 Concentrez-vous sur votre objectif !",
            "⚡ L'énergie que vous investissez aujourd'hui vous rendra plus fort !",
            "🌟 Dépassez vos limites, vous en êtes capable !",
            "💯 Donnez le meilleur de vous-même !",
            "🎪 Transformez la sueur en sourire !"
        ];
        
        setInterval(() => {
            if (this.isRunning && this.totalSeconds > 0) {
                const randomMessage = messages[Math.floor(Math.random() * messages.length)];
                this.motivationMessage.innerHTML = `<strong>${randomMessage}</strong>`;
                this.motivationMessage.className = 'alert alert-success text-center pulse';
                
                setTimeout(() => {
                    this.motivationMessage.className = 'alert alert-info text-center';
                }, 3000);
            }
        }, 5000); // Nouveau message toutes les 5 secondes
    }
    
    completeTraining() {
        const duration = Math.round(this.totalSeconds / 60);
        const calories = this.getCaloriesBurned();
        const progressPercent = this.getProgressPercent();
        
        // 🎮 Bonus XP de fin et achievements finaux
        this.gainXP(25, 'Entraînement terminé');
        this.checkAchievements();
        
        // Mettre à jour le modal avec les données de gamification
        document.getElementById('finalTime').textContent = this.mainTimer.textContent;
        document.getElementById('finalCalories').textContent = calories;
        document.getElementById('finalXP').textContent = this.gamification.xpGained;
        document.getElementById('finalProgress').textContent = `${Math.round(progressPercent)}%`;
        
        // Afficher les achievements débloqués
        if (this.gamification.achievements.length > 0) {
            document.getElementById('unlockedAchievements').style.display = 'block';
            // Ici vous pouvez ajouter la logique pour afficher les achievements
        }
        
        // Envoyer les données au serveur
        fetch('{{ route("training.complete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                exercise_name: '{{ $exercise["exercise_name"] }}',
                exercise_type: '{{ $exercise["exercise_type"] }}',
                duration_minutes: duration,
                calories_burned: calories,
                difficulty_level: {{ $exercise['difficulty_level'] }},
                average_heart_rate: this.heartRateHistory.length > 0 ? 
                    Math.round(this.heartRateHistory.reduce((a, b) => a + b, 0) / this.heartRateHistory.length) : null,
                completion_percentage: Math.round(progressPercent),
                // 🎮 Données de gamification
                xp_gained: this.gamification.xpGained,
                achievements_unlocked: this.gamification.achievements,
                challenges_completed: this.gamification.challenges.filter(c => c.completed).length
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher le modal de complétion
                const modal = new bootstrap.Modal(document.getElementById('completionModal'));
                modal.show();
                
                // Notification de succès avec gamification
                Swal.fire({
                    title: '🎉 Bravo !',
                    html: `
                        <div class="text-center">
                            <p>Votre entraînement a été sauvegardé avec succès</p>
                            <div class="badge bg-warning fs-5 px-3 py-2 mb-2">
                                +${this.gamification.xpGained} XP Total
                            </div>
                            <br>
                            <small class="text-muted">${this.gamification.achievements.length} achievements débloqués</small>
                        </div>
                    `,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: 'Erreur lors de la sauvegarde de l\'entraînement',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                title: 'Erreur',
                text: 'Erreur de communication avec le serveur',
                icon: 'error'
            });
        });
    }
}

// Initialiser la session d'entraînement avec gamification
document.addEventListener('DOMContentLoaded', function() {
    const session = new TrainingSession();
    
    // Empêcher la fermeture accidentelle de la page
    window.addEventListener('beforeunload', function(e) {
        if (session.isRunning && session.totalSeconds > 0) {
            e.preventDefault();
            e.returnValue = 'Votre entraînement est en cours. Êtes-vous sûr de vouloir quitter ?';
        }
    });
});
</script>
@endsection
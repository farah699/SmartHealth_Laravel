
@extends('partials.layouts.master')

@section('title', 'Pratique de Yoga | SmartHealth')
@section('title-sub', 'Yoga IA')
@section('pagetitle', 'Détection de poses')

@section('css')
<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
.yoga-container {
    max-width: 1200px;
    margin: 0 auto;
}

.camera-container {
    position: relative;
    background: #000;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
    border: 3px solid #667eea;
}

#videoElement {
    width: 100%;
    height: 480px;
    object-fit: cover;
}

.pose-status {
    position: absolute;
    top: 20px;
    left: 20px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 18px;
    font-weight: bold;
    z-index: 10;
}

.pose-status.correct {
    background: rgba(40, 167, 69, 0.9);
    animation: pulse 0.5s;
}

.pose-status.incorrect {
    background: rgba(220, 53, 69, 0.9);
}

.pose-status.waiting {
    background: rgba(255, 193, 7, 0.9);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.control-buttons {
    text-align: center;
    margin: 20px 0;
}

.btn-yoga {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin: 0 10px;
    transition: all 0.3s ease;
}

.btn-yoga:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.btn-yoga:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
    transform: none;
}

.btn-yoga.stop {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.session-info {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin: 20px 0;
}

.info-item {
    text-align: center;
}

.info-value {
    font-size: 24px;
    font-weight: bold;
    color: #667eea;
}

.info-label {
    font-size: 14px;
    color: #6c757d;
}

.session-active {
    border-color: #28a745 !important;
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.3);
}

.detection-indicator {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #dc3545;
    z-index: 10;
}

.detection-indicator.active {
    background-color: #28a745;
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.3; }
}
</style>
@endsection

@section('content')
<div class="yoga-container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-0">
                    <div class="camera-container" id="cameraContainer">
                        <video id="videoElement" autoplay muted></video>
                        <canvas id="captureCanvas" style="display: none;"></canvas>
                        <div id="poseStatus" class="pose-status waiting" style="display: none;">
                            En attente...
                        </div>
                        <div id="detectionIndicator" class="detection-indicator"></div>
                    </div>
                    
                    <div class="control-buttons p-3">
                        <button id="startBtn" class="btn-yoga">
                            <i class="bi bi-play-fill"></i> Commencer la séance
                        </button>
                        <button id="stopBtn" class="btn-yoga stop" disabled>
                            <i class="bi bi-stop-fill"></i> Terminer la séance
                        </button>
                    </div>

                    <div class="session-info">
                        <div class="info-item">
                            <div class="info-value" id="sessionTime">00:00</div>
                            <div class="info-label">Temps</div>
                        </div>
                        <div class="info-item">
                            <div class="info-value" id="sessionPoints">0</div>
                            <div class="info-label">Points</div>
                        </div>
                        <div class="info-item">
                            <div class="info-value" id="correctPoses">0</div>
                            <div class="info-label">Poses correctes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="stats-card">
                <h5><i class="bi bi-trophy"></i> Vos statistiques</h5>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Points totaux:</span>
                        <strong>{{ $stats->total_points }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Séances:</span>
                        <strong>{{ $stats->total_sessions }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Niveau:</span>
                        <strong>{{ $stats->level }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Série actuelle:</span>
                        <strong>{{ $stats->current_streak }} jours</strong>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-list-check"></i> Poses détectées</h6>
                </div>
                <div class="card-body">
                    <div id="detectedPoses">
                        <p class="text-muted">Aucune pose détectée</p>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="bi bi-info-circle"></i> Instructions</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li>✅ <strong>T Pose:</strong> Bras étendus horizontalement</li>
                        <li>🌳 <strong>Tree Pose:</strong> Une jambe pliée, équilibre</li>
                        <li>⚔️ <strong>Warrior II:</strong> Bras étendus, jambes écartées</li>
                        <li>🧘 <strong>Méditation:</strong> Position assise, mains jointes</li>
                        <li>🐍 <strong>Cobra:</strong> Allongé, bras tendus</li>
                        <li>🤲 <strong>Child's Pose:</strong> Genoux pliés, bras vers l'avant</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
class YogaSession {
    constructor() {
        this.sessionId = null;
        this.isActive = false;
        this.sessionStartTime = null;
        this.timerInterval = null;
        this.detectionInterval = null;
        this.sessionPoints = 0;
        this.correctPoses = 0;
        this.detectedPoses = new Map();
        this.isProcessing = false;
        this.lastDetectionTime = 0;
        
        this.initializeElements();
        this.setupEventListeners();
        this.initializeCamera();
    }

    initializeElements() {
        this.video = document.getElementById('videoElement');
        this.canvas = document.getElementById('captureCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.startBtn = document.getElementById('startBtn');
        this.stopBtn = document.getElementById('stopBtn');
        this.poseStatus = document.getElementById('poseStatus');
        this.sessionTimeEl = document.getElementById('sessionTime');
        this.sessionPointsEl = document.getElementById('sessionPoints');
        this.correctPosesEl = document.getElementById('correctPoses');
        this.detectedPosesEl = document.getElementById('detectedPoses');
        this.cameraContainer = document.getElementById('cameraContainer');
        this.detectionIndicator = document.getElementById('detectionIndicator');
    }

    setupEventListeners() {
        this.startBtn.addEventListener('click', () => this.startSession());
        this.stopBtn.addEventListener('click', () => this.endSession());
        
        // Éviter les clics multiples
        this.stopBtn.addEventListener('click', (e) => {
            if (this.isProcessing) {
                e.preventDefault();
                return false;
            }
        });
    }

    async initializeCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    width: { ideal: 640 }, 
                    height: { ideal: 480 },
                    frameRate: { ideal: 30 }
                }
            });
            this.video.srcObject = stream;
            
            this.video.addEventListener('loadedmetadata', () => {
                console.log('Caméra initialisée:', this.video.videoWidth, 'x', this.video.videoHeight);
            });
            
        } catch (error) {
            console.error('Erreur d\'accès à la caméra:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur caméra',
                text: 'Impossible d\'accéder à la caméra. Veuillez vérifier les permissions.',
                confirmButtonColor: '#667eea'
            });
        }
    }

    async startSession() {
        if (this.isProcessing) return;
        
        try {
            this.isProcessing = true;
            this.startBtn.disabled = true;
            
            const response = await fetch('{{ route("yoga.start") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.sessionId = data.session_id;
                this.isActive = true;
                this.sessionStartTime = new Date();
                
                this.stopBtn.disabled = false;
                this.poseStatus.style.display = 'block';
                this.cameraContainer.classList.add('session-active');
                
                this.startTimer();
                this.startPoseDetection();
                
                this.showPoseStatus('Session démarrée!', 'correct');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Session démarrée!',
                    text: 'Placez-vous devant la caméra et commencez vos poses de yoga.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Erreur:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Erreur lors du démarrage de la session: ' + error.message,
                confirmButtonColor: '#667eea'
            });
            this.startBtn.disabled = false;
        } finally {
            this.isProcessing = false;
        }
    }

    async endSession() {
        if (this.isProcessing || !this.isActive) return;
        
        try {
            this.isProcessing = true;
            
            // Arrêter immédiatement la détection
            this.isActive = false;
            this.stopTimer();
            this.stopPoseDetection();
            
            // Désactiver les boutons
            this.startBtn.disabled = true;
            this.stopBtn.disabled = true;
            
            // Afficher le statut
            this.showPoseStatus('Arrêt en cours...', 'waiting');
            this.cameraContainer.classList.remove('session-active');
            this.detectionIndicator.classList.remove('active');

            const response = await fetch('{{ route("yoga.end") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    session_id: this.sessionId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Masquer le statut
                this.poseStatus.style.display = 'none';
                
                // Réactiver le bouton start
                this.startBtn.disabled = false;
                
                // Afficher le résumé avec SweetAlert
                await Swal.fire({
                    icon: 'success',
                    title: '🎉 Session terminée!',
                    html: `
                        <div class="text-center">
                            <div class="mb-3">
                                <h4 class="text-primary">Félicitations!</h4>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="border rounded p-2 mb-2">
                                        <div class="h3 text-primary">${data.formatted_duration}</div>
                                        <small class="text-muted">Durée</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2 mb-2">
                                        <div class="h3 text-success">${data.total_points}</div>
                                        <small class="text-muted">Points</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2 mb-2">
                                        <div class="h3 text-warning">${this.correctPoses}</div>
                                        <small class="text-muted">Poses</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Continuer',
                    confirmButtonColor: '#667eea',
                    allowOutsideClick: false
                });
                
                // Recharger la page pour mettre à jour les statistiques
                window.location.reload();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Erreur:', error);
            
            // En cas d'erreur, réactiver les boutons
            this.startBtn.disabled = false;
            this.stopBtn.disabled = false;
            
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Erreur lors de la fin de session: ' + error.message,
                confirmButtonColor: '#667eea'
            });
        } finally {
            this.isProcessing = false;
        }
    }

    startTimer() {
        this.timerInterval = setInterval(() => {
            if (this.sessionStartTime && this.isActive) {
                const now = new Date();
                const elapsed = Math.floor((now - this.sessionStartTime) / 1000);
                this.sessionTimeEl.textContent = this.formatTime(elapsed);
            }
        }, 1000);
    }

    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }

    startPoseDetection() {
        this.detectionInterval = setInterval(() => {
            if (this.isActive && !this.isProcessing) {
                this.detectPose();
            }
        }, 800); // Détection toutes les 800ms pour plus de stabilité
    }

    stopPoseDetection() {
        if (this.detectionInterval) {
            clearInterval(this.detectionInterval);
            this.detectionInterval = null;
        }
    }

    async detectPose() {
        if (!this.isActive || this.isProcessing) return;
        
        try {
            // Throttle des détections
            const now = Date.now();
            if (now - this.lastDetectionTime < 500) return;
            this.lastDetectionTime = now;
            
            // Vérifier que la vidéo est prête
            if (this.video.readyState !== this.video.HAVE_ENOUGH_DATA) return;
            
            // Indiquer que la détection est active
            this.detectionIndicator.classList.add('active');
            
            // Capturer l'image de la vidéo
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;
            this.ctx.drawImage(this.video, 0, 0);
            
            // Convertir en base64 avec une qualité réduite pour la performance
            const imageData = this.canvas.toDataURL('image/jpeg', 0.7);
            
            const response = await fetch('{{ route("yoga.detect") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    image: imageData,
                    session_id: this.sessionId
                })
            });

            if (!response.ok) {
                throw new Error('Erreur de communication avec le serveur');
            }

            const data = await response.json();
            
            if (data.success) {
                this.handlePoseDetection(data);
            }
        } catch (error) {
            console.error('Erreur de détection:', error);
        } finally {
            // Désactiver l'indicateur de détection
            setTimeout(() => {
                this.detectionIndicator.classList.remove('active');
            }, 200);
        }
    }

    handlePoseDetection(data) {
        const { pose_name, is_correct, points } = data;
        
        if (is_correct && pose_name !== 'Unknown Pose' && pose_name !== 'Position en cours') {
            this.correctPoses++;
            this.sessionPoints += points || 1;
            
            this.showPoseStatus(`${pose_name} - Correct! +${points}pts`, 'correct');
            
            // Mettre à jour les compteurs
            if (this.detectedPoses.has(pose_name)) {
                this.detectedPoses.set(pose_name, this.detectedPoses.get(pose_name) + 1);
            } else {
                this.detectedPoses.set(pose_name, 1);
            }
            
            this.updateUI();
            
            // Feedback audio optionnel
            this.playSuccessSound();
            
        } else if (pose_name === 'Position en cours' || pose_name === 'Position incomplète') {
            this.showPoseStatus(pose_name, 'waiting');
        } else {
            this.showPoseStatus(`${pose_name}`, 'incorrect');
        }
    }

    playSuccessSound() {
        // Créer un son de succès simple
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (error) {
            // Ignore les erreurs de son
        }
    }

    showPoseStatus(text, type) {
        this.poseStatus.textContent = text;
        this.poseStatus.className = `pose-status ${type}`;
        
        // Masquer après 3 secondes si incorrect ou waiting
        if (type === 'incorrect' || type === 'waiting') {
            setTimeout(() => {
                if (this.isActive) {
                    this.poseStatus.textContent = 'En attente...';
                    this.poseStatus.className = 'pose-status waiting';
                }
            }, 3000);
        }
    }

    updateUI() {
        this.sessionPointsEl.textContent = this.sessionPoints;
        this.correctPosesEl.textContent = this.correctPoses;
        
        // Mettre à jour la liste des poses détectées
        let posesList = '';
        for (const [pose, count] of this.detectedPoses) {
            posesList += `<div class="d-flex justify-content-between mb-1">
                <span class="small">${pose}:</span>
                <strong class="badge bg-success">${count}</strong>
            </div>`;
        }
        
        this.detectedPosesEl.innerHTML = posesList || '<p class="text-muted small">Aucune pose détectée</p>';
    }

    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
}

// Initialiser la session de yoga quand la page est chargée
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier le support des technologies nécessaires
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        Swal.fire({
            icon: 'error',
            title: 'Navigateur non supporté',
            text: 'Votre navigateur ne supporte pas l\'accès à la caméra. Veuillez utiliser un navigateur récent.',
            confirmButtonColor: '#667eea'
        });
        return;
    }
    
    new YogaSession();
});
</script>
@endsection
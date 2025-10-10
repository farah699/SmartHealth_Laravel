<div id="virtualTrainerModal" class="modal fade" tabindex="-1" role="dialog" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down" role="document">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-user-ninja me-2"></i>
                    Entraîneur Virtuel - <span id="currentExerciseName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0">
                <div class="row g-0 h-100">
                    <!-- Zone d'animation du personnage -->
                    <div class="col-lg-8">
                        <div class="trainer-stage position-relative" id="trainerStage">
                            <!-- Canvas pour l'animation -->
                            <canvas id="trainerCanvas" width="800" height="600"></canvas>
                            
                            <!-- Overlay avec instructions -->
                            <div class="instruction-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <div class="instruction-card bg-dark bg-opacity-75 rounded p-4 m-4">
                                    <h4 id="currentInstruction" class="text-center mb-3">Préparez-vous...</h4>
                                    <div class="progress mb-3">
                                        <div id="exerciseProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <div class="text-center">
                                        <h2 id="exerciseTimer" class="text-primary mb-2">00:00</h2>
                                        <p id="exercisePhase" class="text-muted">Phase d'échauffement</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panneau de contrôle -->
                    <div class="col-lg-4 bg-secondary">
                        <div class="p-4">
                            <!-- Informations de l'exercice -->
                            <div class="exercise-info mb-4">
                                <h6>Informations de l'exercice</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="stat-mini">
                                            <h5 id="exerciseDuration" class="text-info">--</h5>
                                            <small>Durée</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-mini">
                                            <h5 id="exerciseIntensity" class="text-warning">--</h5>
                                            <small>Intensité</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-mini">
                                            <h5 id="caloriesBurned" class="text-success">0</h5>
                                            <small>Calories</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contrôles -->
                            <div class="controls mb-4">
                                <div class="d-grid gap-2">
                                    <button id="startBtn" class="btn btn-success btn-lg">
                                        <i class="fas fa-play me-2"></i>Commencer
                                    </button>
                                    <button id="pauseBtn" class="btn btn-warning btn-lg" style="display: none;">
                                        <i class="fas fa-pause me-2"></i>Pause
                                    </button>
                                    <button id="stopBtn" class="btn btn-danger btn-lg" style="display: none;">
                                        <i class="fas fa-stop me-2"></i>Arrêter
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Instructions détaillées -->
                            <div class="instructions-panel">
                                <h6>Instructions</h6>
                                <div id="detailedInstructions" class="small text-light bg-dark rounded p-3">
                                    Préparez-vous à commencer l'exercice...
                                </div>
                            </div>
                            
                            <!-- Progression -->
                            <div class="progression mt-4">
                                <h6>Progression</h6>
                                <div id="exerciseSteps" class="exercise-steps">
                                    <!-- Les étapes seront ajoutées dynamiquement -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
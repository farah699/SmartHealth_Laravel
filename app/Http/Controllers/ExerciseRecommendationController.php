<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExerciseRecommendation;
use App\Models\Exercise;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;

class ExerciseRecommendationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $recommendations = $this->generateRecommendations($user);
        
        return view('exercises.recommendations', compact('recommendations'));
    }

    public function generateRecommendations($user)
    {
        // Vérification du profil utilisateur
        if (!$user->age || !$user->imc) {
            return ['error' => 'Profil incomplet. Veuillez compléter vos informations (âge, poids, taille).'];
        }

        // Calcul de la fréquence d'activité (dernières 4 semaines)
        $activityFrequency = Activity::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subWeeks(4))
            ->count();

        // Détermination du niveau de fitness
        $fitnessLevel = $this->determineFitnessLevel($user->age, $user->imc, $activityFrequency);

        // Profil utilisateur pour l'IA
        $userProfile = [
            'age' => $user->age,
            'imc' => round($user->imc, 2),
            'fitness_level' => $fitnessLevel,
            'activity_frequency' => $activityFrequency,
            'activity_types' => $this->getUserActivityTypes($user->id)
        ];

        // Appel du modèle IA Python
        $recommendations = $this->callPythonAI($userProfile);

        // Sauvegarde des recommandations
        $this->saveRecommendations($user->id, $recommendations, $userProfile);

        return $recommendations;
    }

    private function determineFitnessLevel($age, $imc, $activityFrequency)
    {
        $score = 0;

        // Score basé sur l'âge
        if ($age < 30) $score += 3;
        elseif ($age < 50) $score += 2;
        else $score += 1;

        // Score basé sur l'IMC
        if ($imc >= 18.5 && $imc <= 25) $score += 3;
        elseif ($imc <= 30) $score += 2;
        else $score += 1;

        // Score basé sur la fréquence d'activité
        if ($activityFrequency >= 4) $score += 3;
        elseif ($activityFrequency >= 2) $score += 2;
        else $score += 1;

        // Détermination du niveau
        if ($score >= 7) return 'advanced';
        elseif ($score >= 5) return 'intermediate';
        else return 'beginner';
    }

    private function getUserActivityTypes($userId)
    {
        return Activity::where('user_id', $userId)
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->toArray();
    }

    private function callPythonAI($userProfile)
    {
        try {
            // Création du fichier temporaire avec les données utilisateur
            $tempFile = storage_path('app/temp_user_profile_' . uniqid() . '.json');
            file_put_contents($tempFile, json_encode($userProfile));

            // Commande Python
            $pythonScript = base_path('AI/exercise_recommendation_predict.py');
            $process = new Process(['python', $pythonScript, $tempFile]);
            $process->setTimeout(30);
            $process->run();

            // Vérification des erreurs
            if (!$process->isSuccessful()) {
                throw new \Exception('Erreur Python: ' . $process->getErrorOutput());
            }

            // Récupération du résultat
            $result = json_decode($process->getOutput(), true);
            
            // Nettoyage du fichier temporaire
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            return $result;

        } catch (\Exception $e) {
            \Log::error('Erreur IA Recommandations: ' . $e->getMessage());
            
            // Fallback: recommandations basiques
            return $this->getFallbackRecommendations($userProfile);
        }
    }

    public function generate(Request $request)
{
    $user = Auth::user();
    
    try {
        $recommendations = $this->generateRecommendations($user);
        
        if (isset($recommendations['error'])) {
            return redirect()->route('exercise.recommendations')->with('error', $recommendations['error']);
        }
        
        return redirect()->route('exercise.recommendations')->with('success', 'Nouvelles recommandations générées avec succès!');
    } catch (\Exception $e) {
        return redirect()->route('exercise.recommendations')->with('error', 'Erreur lors de la génération: ' . $e->getMessage());
    }
}

    private function getFallbackRecommendations($userProfile)
    {
        // Récupération des exercices adaptés depuis la DB
        $exercises = Exercise::where('age_min', '<=', $userProfile['age'])
            ->where('age_max', '>=', $userProfile['age'])
            ->where('imc_min', '<=', $userProfile['imc'])
            ->where('imc_max', '>=', $userProfile['imc'])
            ->where('category', $userProfile['fitness_level'])
            ->limit(10)
            ->get();

        $recommendations = [];
        foreach ($exercises as $exercise) {
            $score = $this->calculateBasicScore($userProfile, $exercise);
            $recommendations[] = [
                'exercise' => [
                    'id' => $exercise->id,
                    'name' => $exercise->name,
                    'type' => $exercise->type,
                    'duration' => rand($exercise->duration_min, $exercise->duration_max),
                    'intensity' => $this->getIntensityForLevel($userProfile['fitness_level']),
                    'video_url' => $exercise->video_url,
                    'description' => $exercise->description,
                    'instructions' => $exercise->instructions
                ],
                'recommendation_score' => $score
            ];
        }

        // Tri par score décroissant
        usort($recommendations, function($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });

        return $recommendations;
    }

    private function calculateBasicScore($userProfile, $exercise)
    {
        $score = 50;

        // Ajustements basiques
        if ($userProfile['fitness_level'] === 'beginner' && $exercise->difficulty_level <= 3) {
            $score += 20;
        }

        if ($userProfile['imc'] > 25 && $exercise->type === 'cardio') {
            $score += 15;
        }

        if ($userProfile['age'] > 50 && in_array($exercise->type, ['flexibility', 'yoga'])) {
            $score += 10;
        }

        // Bonus si l'utilisateur a déjà fait ce type d'activité
        if (in_array($exercise->type, $userProfile['activity_types'])) {
            $score += 5;
        }

        return min(100, $score);
    }

    private function getIntensityForLevel($fitnessLevel)
    {
        switch ($fitnessLevel) {
            case 'beginner': return 'faible';
            case 'intermediate': return 'modere';
            case 'advanced': return 'intense';
            default: return 'modere';
        }
    }

    private function saveRecommendations($userId, $recommendations, $userProfile)
    {
        // Suppression des anciennes recommandations (plus de 7 jours)
        ExerciseRecommendation::where('user_id', $userId)
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->delete();

        // Sauvegarde des nouvelles recommandations
        foreach ($recommendations as $rec) {
            $exercise = isset($rec['exercise']['id']) 
                ? Exercise::find($rec['exercise']['id']) 
                : null;

            ExerciseRecommendation::create([
                'user_id' => $userId,
                'exercise_id' => $exercise ? $exercise->id : null,
                'exercise_name' => $rec['exercise']['name'],
                'exercise_type' => $rec['exercise']['type'],
                'duration_minutes' => $rec['exercise']['duration'],
                'intensity_level' => $rec['exercise']['intensity'],
                'calories_burned' => $this->estimateCalories($rec['exercise']),
                'description' => $rec['exercise']['description'] ?? "Exercice recommandé par l'IA",
                'instructions' => $rec['exercise']['instructions'] ?? $this->getExerciseInstructions($rec['exercise']['name']),
                'equipment_needed' => json_encode($this->getRequiredEquipment($rec['exercise']['name'])),
                'target_muscle_groups' => json_encode($this->getTargetMuscles($rec['exercise']['type'])),
                'user_age' => $userProfile['age'],
                'user_imc' => $userProfile['imc'],
                'user_fitness_level' => $userProfile['fitness_level'],
                'recommended_score' => $rec['recommendation_score']
            ]);
        }
    }

    private function estimateCalories($exercise)
    {
        $baseCalories = [
            'cardio' => 8,
            'strength' => 6,
            'flexibility' => 3,
            'balance' => 4,
            'yoga' => 4,
            'pilates' => 5
        ];

        $multiplier = ['faible' => 0.8, 'modere' => 1.0, 'intense' => 1.3];

        return ($baseCalories[$exercise['type']] ?? 5) * 
               ($multiplier[$exercise['intensity']] ?? 1.0) * 
               $exercise['duration'];
    }

    private function getExerciseInstructions($exerciseName)
    {
        $instructions = [
            'Marche rapide' => 'Marchez à un rythme soutenu, maintenez une posture droite.',
            'Course à pied' => 'Commencez par un échauffement, augmentez progressivement l\'allure.',
            'Yoga doux' => 'Effectuez les postures lentement, respirez profondément.',
            'Squats' => 'Pieds écartés largeur épaules, descendez comme pour vous asseoir.',
            'Pompes' => 'Gardez le corps aligné, descendez jusqu\'à effleurer le sol.',
            'Vélo' => 'Pédalez à un rythme régulier, ajustez la résistance selon votre niveau.'
        ];

        return $instructions[$exerciseName] ?? 'Suivez les instructions de base pour cet exercice.';
    }

    private function getRequiredEquipment($exerciseName)
    {
        $equipment = [
            'Vélo' => ['Vélo ou vélo stationnaire'],
            'Yoga doux' => ['Tapis de yoga'],
            'Natation' => ['Piscine', 'Maillot de bain'],
            'Course à pied' => ['Chaussures de course'],
            'Marche rapide' => ['Chaussures confortables'],
            'Squats' => ['Aucun équipement'],
            'Pompes' => ['Aucun équipement']
        ];

        return $equipment[$exerciseName] ?? ['Aucun équipement spécialisé'];
    }

    private function getTargetMuscles($exerciseType)
    {
        $muscles = [
            'cardio' => ['Système cardiovasculaire', 'Jambes', 'Core'],
            'strength' => ['Tous les groupes musculaires', 'Force fonctionnelle'],
            'flexibility' => ['Tous les muscles', 'Articulations', 'Mobilité'],
            'balance' => ['Core', 'Muscles stabilisateurs', 'Proprioception'],
            'yoga' => ['Corps entier', 'Flexibilité', 'Équilibre'],
            'pilates' => ['Core', 'Posture', 'Stabilité']
        ];

        return $muscles[$exerciseType] ?? ['Général'];
    }

    // API endpoints
    public function apiGenerateRecommendations(Request $request)
    {
        $user = Auth::user();
        $recommendations = $this->generateRecommendations($user);

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function apiGetUserRecommendations()
    {
        $recommendations = ExerciseRecommendation::where('user_id', Auth::id())
            ->with('exercise')
            ->orderBy('recommended_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function markAsWatched(Request $request, $id)
    {
        $recommendation = ExerciseRecommendation::where('user_id', Auth::id())
            ->findOrFail($id);

        $recommendation->update([
            'is_watched' => true,
            'watch_duration' => $request->watch_duration ?? 0
        ]);

        return response()->json(['success' => true]);
    }

    public function markAsCompleted(Request $request, $id)
    {
        $recommendation = ExerciseRecommendation::where('user_id', Auth::id())
            ->findOrFail($id);

        $recommendation->update([
            'is_completed' => true,
            'is_watched' => true
        ]);

        return response()->json(['success' => true]);
    }


public function saveRecommendation(Request $request)
{
    // Implémentation pour sauvegarder une recommandation spécifique
    return response()->json(['success' => true]);
}
public function getVideos($type)
{
    // Implémentation pour récupérer des vidéos par type
    return response()->json(['videos' => []]);
}


public function rateRecommendation(Request $request)
{
    $request->validate([
        'recommendation_id' => 'required|exists:exercise_recommendations,id',
        'rating' => 'required|integer|min:1|max:5'
    ]);

    $recommendation = ExerciseRecommendation::where('user_id', Auth::id())
        ->findOrFail($request->recommendation_id);

    $recommendation->update([
        'user_rating' => $request->rating
    ]);

    return response()->json(['success' => true]);
}

public function trackExerciseStart(Request $request)
{
    try {
        // Log de l'exercice commencé
        \Log::info('Exercise started', [
            'user_id' => Auth::id(),
            'exercise_name' => $request->exercise_name,
            'exercise_type' => $request->exercise_type,
            'duration' => $request->duration
        ]);
        
        return response()->json(['success' => true, 'message' => 'Exercise tracked']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

public function trackVideoView(Request $request)
{
    try {
        // Log de la vidéo visionnée
        \Log::info('Video viewed', [
            'user_id' => Auth::id(),
            'video_url' => $request->video_url,
            'exercise_name' => $request->exercise_name
        ]);
        
        return response()->json(['success' => true, 'message' => 'Video view tracked']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
    
public function getRecommendationStats()
{
    $stats = [
        'total_recommendations' => ExerciseRecommendation::where('user_id', Auth::id())->count(),
        'completed_exercises' => ExerciseRecommendation::where('user_id', Auth::id())->where('is_completed', true)->count(),
        'average_score' => ExerciseRecommendation::where('user_id', Auth::id())->avg('recommended_score'),
        'favorite_type' => ExerciseRecommendation::where('user_id', Auth::id())
            ->select('exercise_type')
            ->groupBy('exercise_type')
            ->orderByRaw('COUNT(*) DESC')
            ->first()
            ->exercise_type ?? 'N/A'
    ];

    return response()->json(['success' => true, 'stats' => $stats]);
}



public function saveWorkoutStats(Request $request)
{
    try {
        $user = Auth::user();
        
        // Créer une nouvelle activité
        Activity::create([
            'user_id' => $user->id,
            'type' => $request->exercise_type,
            'name' => $request->exercise_name,
            'duration' => $request->duration_minutes,
            'calories' => $request->calories_burned,
            'intensity' => $request->intensity,
            'notes' => "Séance guidée avec entraîneur virtuel - {$request->completion_rate}% complété"
        ]);
        
        // Mettre à jour les statistiques utilisateur
        $this->updateUserStats($user, $request);
        
        return response()->json([
            'success' => true, 
            'message' => 'Statistiques sauvegardées avec succès'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
}
private function safeJsonDecode($jsonString)
{
    if (is_array($jsonString)) {
        return $jsonString;
    }
    
    $decoded = json_decode($jsonString, true);
    
    // Si c'est encore une chaîne, essayer de décoder à nouveau
    if (is_string($decoded)) {
        $decoded = json_decode($decoded, true);
    }
    
    return is_array($decoded) ? $decoded : [];
}

private function updateUserStats($user, $request)
{
    // Mettre à jour les calories totales brûlées (si la colonne existe)
    if (Schema::hasColumn('users', 'total_calories_burned')) {
        $user->increment('total_calories_burned', $request->calories_burned);
    }
    
    // Mettre à jour le temps d'exercice total (si la colonne existe)
    if (Schema::hasColumn('users', 'total_exercise_time')) {
        $user->increment('total_exercise_time', $request->duration_minutes);
    }
    
    // Incrémenter le nombre de séances (si la colonne existe)
    if (Schema::hasColumn('users', 'total_workouts')) {
        $user->increment('total_workouts');
    }
}
}
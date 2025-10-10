<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Activity;

class AIRecommendationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les recommandations existantes s'il y en a
        $recommendations = session('ai_recommendations', []);
        
        return view('exercises.recommendations', compact('user', 'recommendations'));
    }

    public function generateRecommendations()
    {
        try {
            $user = Auth::user();
            
            // Construire le profil utilisateur
            $userProfile = $this->buildUserProfile($user);
            
            Log::info('Profil utilisateur pour IA:', $userProfile);
            
            // ✅ Appel HTTP vers l'API Docker au lieu du script Python local
            $response = Http::timeout(30)->post('http://exercise-ai:5001/recommendations', [
                'age' => $userProfile['age'],
                'imc' => $userProfile['imc'],
                'fitness_level' => $userProfile['fitness_level'],
                'activity_frequency' => $userProfile['activity_frequency'],
                'experience_months' => $userProfile['experience_months'],
                'has_medical_constraints' => $userProfile['has_medical_constraints'],
                'limit' => 15
            ]);
            
            if (!$response->successful()) {
                Log::error('Erreur API IA:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Erreur de communication avec l\'API IA: ' . $response->status());
            }
            
            $data = $response->json();
            
            Log::info('Réponse brute API IA:', ['data' => $data]);
            
            if (!isset($data['success']) || !$data['success']) {
                $errorMessage = $data['error'] ?? 'Erreur inconnue de l\'IA';
                throw new \Exception($errorMessage);
            }
            
            $recommendations = $data['recommendations'] ?? [];
            
            if (empty($recommendations)) {
                throw new \Exception('Aucune recommandation valide reçue de l\'IA');
            }
            
            Log::info('Recommandations IA:', [
                'count' => count($recommendations),
                'first_exercise' => $recommendations[0] ?? null
            ]);
            
            // Sauvegarder les recommandations en session
            session(['ai_recommendations' => $recommendations]);
            
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
                'message' => count($recommendations) . ' recommandations générées avec succès!'
            ]);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Erreur de connexion API IA:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Impossible de se connecter au serveur IA. Vérifiez que le service exercise-ai est démarré.'
            ], 503);
            
        } catch (\Exception $e) {
            Log::error('Erreur génération IA:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function buildUserProfile(User $user): array
    {
        // Calculer l'âge
        $age = $user->age ?? ($user->date_of_birth ? 
            now()->diffInYears($user->date_of_birth) : 28);
        
        // Calculer l'IMC
        $imc = $this->calculateIMC($user);
        
        // Déterminer le niveau de fitness
        $fitnessLevel = $this->calculateFitnessLevel($user);
        
        // Calculer la fréquence d'activité
        $activityFrequency = $this->calculateActivityFrequency($user);
        
        // Calculer l'expérience
        $experienceMonths = $this->calculateExperience($user);
        
        return [
            'age' => (int) $age,
            'imc' => (float) $imc,
            'fitness_level' => $fitnessLevel,
            'activity_frequency' => (int) $activityFrequency,
            'experience_months' => (int) $experienceMonths,
            'has_medical_constraints' => $this->hasHealthConstraints($user)
        ];
    }

    private function calculateIMC(User $user): float
    {
        if ($user->weight && $user->height) {
            $heightInMeters = $user->height / 100;
            return round($user->weight / ($heightInMeters * $heightInMeters), 1);
        }
        
        return 26.1; // Valeur par défaut
    }

    private function calculateFitnessLevel(User $user): string
    {
        $recentActivities = $user->activities()
            ->where('created_at', '>=', now()->subMonths(3))
            ->count();
        
        if ($recentActivities >= 30) {
            return 'advanced';
        } elseif ($recentActivities >= 10) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }

    private function calculateActivityFrequency(User $user): int
    {
        $activitiesLastMonth = $user->activities()
            ->where('created_at', '>=', now()->subMonth())
            ->count();
        
        return max(1, min(7, intval($activitiesLastMonth / 4)));
    }

    private function calculateExperience(User $user): int
    {
        $firstActivity = $user->activities()->oldest()->first();
        
        if ($firstActivity) {
            return now()->diffInMonths($firstActivity->created_at);
        }
        
        return 2; // Par défaut
    }

    private function hasHealthConstraints(User $user): bool
    {
        return $user->has_health_issues ?? false;
    }
}
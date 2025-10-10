<?php
// filepath: c:\Users\ferie\OneDrive\Bureau\projetLaravel\SmartHealth_Laravel\database\seeders\ExerciseRecommendationSeeder.php
// Dataset d'Entraînement du Modèle d'IA pour Recommandations d'Exercices
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Exercise;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ExerciseRecommendationSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $users = User::all();
        $exercises = Exercise::all();
        
        $this->command->info('🤖 Génération des recommandations d\'exercices pour l\'IA...');
        
        // Supprimer les anciennes recommandations
        DB::table('exercise_recommendations')->truncate();
        
        foreach ($users as $user) {
            // Calculer le niveau de fitness basé sur l'historique
            $fitnessLevel = $this->calculateFitnessLevel($user);
            $activityFrequency = $this->calculateActivityFrequency($user);
            
            // Générer des recommandations pour chaque exercice
            foreach ($exercises as $exercise) {
                $score = $this->calculateRecommendationScore($user, $exercise, $fitnessLevel, $activityFrequency);
                $duration = $this->calculateRecommendedDuration($exercise, $fitnessLevel);
                
                // Créer la recommandation
                DB::table('exercise_recommendations')->insert([
                    'user_id' => $user->id,
                    'exercise_id' => $exercise->id,
                    'recommendation_score' => $score,
                    'recommended_duration' => $duration,
                    'user_age' => $user->age,
                    'user_imc' => $user->imc,
                    'user_fitness_level' => $fitnessLevel,
                    'user_activity_frequency' => $activityFrequency,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        $totalRecommendations = $users->count() * $exercises->count();
        $this->command->info("✅ {$totalRecommendations} recommandations générées pour l'entraînement IA");
    }
    
    private function calculateFitnessLevel(User $user): string
    {
        $activitiesCount = $user->activities()->count();
        $avgDuration = $user->activities()->avg('duration') ?? 0;
        $recentSessions = $user->activities()
            ->with('sessions')
            ->get()
            ->flatMap->sessions
            ->where('session_date', '>=', now()->subMonth())
            ->count();
        
        // Algorithme de scoring
        $score = 0;
        
        // Nombre d'activités
        if ($activitiesCount >= 10) $score += 3;
        elseif ($activitiesCount >= 5) $score += 2;
        elseif ($activitiesCount >= 2) $score += 1;
        
        // Durée moyenne
        if ($avgDuration >= 60) $score += 3;
        elseif ($avgDuration >= 30) $score += 2;
        elseif ($avgDuration >= 15) $score += 1;
        
        // Activité récente
        if ($recentSessions >= 12) $score += 3;
        elseif ($recentSessions >= 6) $score += 2;
        elseif ($recentSessions >= 2) $score += 1;
        
        // IMC factor
        if ($user->imc >= 18.5 && $user->imc < 25) $score += 1;
        
        return match(true) {
            $score >= 8 => 'advanced',
            $score >= 5 => 'intermediate',
            default => 'beginner'
        };
    }
    
    private function calculateActivityFrequency(User $user): int
    {
        return $user->activities()
            ->with('sessions')
            ->get()
            ->flatMap->sessions
            ->where('session_date', '>=', now()->subMonth())
            ->count();
    }
    
    private function calculateRecommendationScore(User $user, Exercise $exercise, string $fitnessLevel, int $activityFrequency): float
    {
        $score = 50; // Score de base
        
        // Vérification compatibilité âge
        if ($user->age < $exercise->age_min || $user->age > $exercise->age_max) {
            return 0; // Incompatible
        }
        
        // Vérification compatibilité IMC
        if ($user->imc < $exercise->imc_min || $user->imc > $exercise->imc_max) {
            return 0; // Incompatible
        }
        
        // Bonus type d'exercice selon l'âge
        if ($user->age >= 60 && in_array($exercise->type, ['flexibility', 'balance'])) {
            $score += 15;
        } elseif ($user->age <= 30 && $exercise->type === 'cardio') {
            $score += 10;
        }
        
        // Bonus IMC
        if ($user->imc > 25 && $exercise->type === 'cardio' && $exercise->difficulty_level <= 5) {
            $score += 20;
        } elseif ($user->imc < 20 && $exercise->type === 'strength') {
            $score += 15;
        }
        
        // Correspondance niveau de fitness
        $levelDiff = abs($this->getLevelScore($fitnessLevel) - $exercise->difficulty_level);
        if ($levelDiff <= 1) $score += 15;
        elseif ($levelDiff <= 2) $score += 5;
        else $score -= 10;
        
        // Bonus fréquence d'activité
        if ($activityFrequency >= 12 && $exercise->difficulty_level >= 6) {
            $score += 10;
        } elseif ($activityFrequency <= 2 && $exercise->difficulty_level <= 3) {
            $score += 15;
        }
        
        return max(0, min(100, $score));
    }
    
    private function getLevelScore(string $level): int
    {
        return match($level) {
            'beginner' => 3,
            'intermediate' => 6,
            'advanced' => 9,
            default => 3
        };
    }
    
    private function calculateRecommendedDuration(Exercise $exercise, string $fitnessLevel): int
    {
        $baseDuration = ($exercise->duration_min + $exercise->duration_max) / 2;
        
        return match($fitnessLevel) {
            'beginner' => round($baseDuration * 0.7),
            'intermediate' => round($baseDuration),
            'advanced' => round($baseDuration * 1.3),
            default => round($baseDuration)
        };
    }
}
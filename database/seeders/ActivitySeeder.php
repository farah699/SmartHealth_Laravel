<?php
// filepath: c:\Users\ferie\OneDrive\Bureau\projetLaravel\SmartHealth_Laravel\database\seeders\ActivitySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Activity;
use App\Models\ActivitySession;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $this->command->info('🏃‍♂️ Génération d\'activités pour 40K utilisateurs...');
        
        // Traitement par batch pour éviter les problèmes de mémoire
        $batchSize = 1000;
        $totalUsers = User::count();
        
        for ($offset = 0; $offset < $totalUsers; $offset += $batchSize) {
            $users = User::skip($offset)->take($batchSize)->get();
            
            foreach ($users as $user) {
                $this->createActivitiesForUser($user, $faker);
            }
            
            $this->command->info("📊 Traité " . ($offset + $batchSize) . "/$totalUsers utilisateurs");
        }
        
        $this->command->info('✅ Activités générées avec succès !');
        $this->command->info('📊 Total : ' . Activity::count() . ' activités');
        $this->command->info('📊 Total : ' . ActivitySession::count() . ' sessions');
    }
    
    private function createActivitiesForUser(User $user, $faker)
    {
        $activityLevel = $this->getActivityLevelFromProfile($user);
        $activitiesCount = $this->getActivitiesCountFromLevel($activityLevel);
        
        $activityTypes = ['course', 'marche', 'velo', 'fitness', 'natation', 'yoga'];
        
        for ($i = 0; $i < $activitiesCount; $i++) {
            $activityType = $this->selectActivityTypeByProfile($user, $activityTypes);
            
            $activity = Activity::create([
                'user_id' => $user->id,
                'name' => $this->getActivityName($activityType),
                'type' => $activityType,
                'duration' => $faker->numberBetween(15, 120),
                'distance' => $this->getDistanceForActivity($activityType, $faker),
                'calories' => $faker->numberBetween(50, 800),
                'description' => $faker->sentence,
                'activity_date' => $faker->dateTimeBetween('-6 months', 'now'),
                'start_time' => $faker->time('H:i'),
                'intensity' => $faker->randomElement(['faible', 'modere', 'intense']),
                'is_recurring' => $faker->boolean(70),
                'status' => $faker->randomElement(['active', 'completed']),
                'target_sessions_per_week' => $faker->numberBetween(1, 7),
            ]);
            
            // Créer quelques sessions pour cette activité
            $this->createSessionsForActivity($activity, $user, $faker);
        }
    }
    
    private function getActivityLevelFromProfile(User $user): string
    {
        // Basé sur l'IMC et l'âge
        if ($user->imc < 18.5) return 'low';
        if ($user->imc < 25 && $user->age < 40) return 'high';
        if ($user->imc < 30) return 'medium';
        return 'low';
    }
    
    private function getActivitiesCountFromLevel(string $level): int
    {
        return match($level) {
            'high' => rand(6, 12),
            'medium' => rand(3, 6),
            'low' => rand(1, 3),
            default => rand(2, 4)
        };
    }
    
    private function selectActivityTypeByProfile(User $user, array $types): string
    {
        if ($user->age > 60) {
            return collect(['marche', 'yoga', 'natation'])->random();
        }
        
        if ($user->imc > 30) {
            return collect(['marche', 'natation', 'velo'])->random();
        }
        
        if ($user->age < 30 && $user->imc < 25) {
            return collect(['course', 'fitness', 'velo'])->random();
        }
        
        return collect($types)->random();
    }
    
    private function getActivityName(string $type): string
    {
        $names = [
            'course' => ['Footing matinal', 'Course en forêt', 'Jogging urbain'],
            'marche' => ['Marche rapide', 'Promenade nature', 'Marche nordique'],
            'velo' => ['Sortie vélo', 'VTT', 'Vélo route'],
            'fitness' => ['Cardio', 'Musculation', 'Circuit training'],
            'natation' => ['Nage libre', 'Aquagym', 'Longueurs'],
            'yoga' => ['Yoga détente', 'Hatha yoga', 'Vinyasa']
        ];
        
        return collect($names[$type] ?? ['Activité'])->random();
    }
    
    private function getDistanceForActivity(string $type, $faker): ?float
    {
        return match($type) {
            'course' => $faker->randomFloat(2, 2, 25),
            'marche' => $faker->randomFloat(2, 1, 15),
            'velo' => $faker->randomFloat(2, 5, 80),
            'natation' => $faker->randomFloat(3, 0.5, 5),
            default => null
        };
    }
    
    private function createSessionsForActivity(Activity $activity, User $user, $faker)
    {
        $sessionsCount = rand(2, 8);
        
        for ($i = 0; $i < $sessionsCount; $i++) {
            ActivitySession::create([
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'session_date' => $faker->dateTimeBetween($activity->created_at, 'now'),
                'start_time' => $faker->time('H:i'),
                'duration' => $activity->duration + rand(-10, 15),
                'distance' => $activity->distance ? $activity->distance * rand(80, 120) / 100 : null,
                'calories' => $activity->calories + rand(-50, 100),
                'intensity' => $faker->randomElement(['faible', 'modere', 'intense']),
                'rating' => $faker->randomFloat(1, 1, 5),
                'difficulty' => $faker->randomElement(['tres_facile', 'facile', 'normal', 'difficile'])
            ]);
        }
    }
}
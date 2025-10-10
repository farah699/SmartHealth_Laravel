<?php
// filepath: c:\Users\ferie\OneDrive\Bureau\projetLaravel\SmartHealth_Laravel\database\seeders\ExerciseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;
use Illuminate\Support\Facades\DB;

class ExerciseSeeder extends Seeder
{
    public function run()
    {
        $exercises = [
            // ============ EXERCICES CARDIO ============
            
            // Débutant
            [
                'name' => 'Marche lente',
                'type' => 'cardio',
                'category' => 'beginner',
                'difficulty_level' => 2,
                'duration_min' => 10,
                'duration_max' => 60,
                'calories_per_minute' => 3.5,
                'description' => 'Marche tranquille pour débuter en douceur',
                'instructions' => 'Marchez à votre rythme naturel, concentrez-vous sur votre respiration.',
                'equipment_needed' => json_encode(['Chaussures confortables']),
                'target_muscle_groups' => json_encode(['Jambes', 'Système cardiovasculaire']),
                'age_min' => 8,
                'age_max' => 95,
                'imc_min' => 15.0,
                'imc_max' => 45.0,
                'contraindications' => json_encode([]),
                'video_url' => 'https://www.youtube.com/embed/walking-slow'
            ],
            
            [
                'name' => 'Marche rapide',
                'type' => 'cardio',
                'category' => 'beginner',
                'difficulty_level' => 4,
                'duration_min' => 15,
                'duration_max' => 90,
                'calories_per_minute' => 5.0,
                'description' => 'Marche soutenue pour améliorer l\'endurance',
                'instructions' => 'Marchez d\'un bon pas, balancez les bras naturellement.',
                'equipment_needed' => json_encode(['Chaussures de marche']),
                'target_muscle_groups' => json_encode(['Jambes', 'Système cardiovasculaire', 'Core']),
                'age_min' => 12,
                'age_max' => 90,
                'imc_min' => 15.0,
                'imc_max' => 40.0,
                'contraindications' => json_encode([]),
                'video_url' => 'https://www.youtube.com/embed/walking-fast'
            ],

            [
                'name' => 'Vélo stationnaire léger',
                'type' => 'cardio',
                'category' => 'beginner',
                'difficulty_level' => 3,
                'duration_min' => 10,
                'duration_max' => 45,
                'calories_per_minute' => 6.0,
                'description' => 'Pédalage doux sur vélo d\'appartement',
                'instructions' => 'Pédalez à résistance faible, gardez un rythme régulier.',
                'equipment_needed' => json_encode(['Vélo stationnaire']),
                'target_muscle_groups' => json_encode(['Jambes', 'Système cardiovasculaire']),
                'age_min' => 15,
                'age_max' => 85,
                'imc_min' => 16.0,
                'imc_max' => 38.0,
                'contraindications' => json_encode(['Problèmes de genoux sévères']),
                'video_url' => 'https://www.youtube.com/embed/bike-light'
            ],

            [
                'name' => 'Course à pied modérée',
                'type' => 'cardio',
                'category' => 'intermediate',
                'difficulty_level' => 6,
                'duration_min' => 20,
                'duration_max' => 60,
                'calories_per_minute' => 12.0,
                'description' => 'Course à rythme modéré pour développer l\'endurance',
                'instructions' => 'Alternez course et marche si nécessaire, écoutez votre corps.',
                'equipment_needed' => json_encode(['Chaussures de course']),
                'target_muscle_groups' => json_encode(['Jambes', 'Système cardiovasculaire', 'Core']),
                'age_min' => 16,
                'age_max' => 70,
                'imc_min' => 16.0,
                'imc_max' => 35.0,
                'contraindications' => json_encode(['Problèmes articulaires sévères']),
                'video_url' => 'https://www.youtube.com/embed/running-moderate'
            ],

            [
                'name' => 'Vélo elliptique',
                'type' => 'cardio',
                'category' => 'intermediate',
                'difficulty_level' => 5,
                'duration_min' => 15,
                'duration_max' => 45,
                'calories_per_minute' => 10.0,
                'description' => 'Exercice complet sans impact sur les articulations',
                'instructions' => 'Mouvement fluide, utilisez aussi les bras.',
                'equipment_needed' => json_encode(['Vélo elliptique']),
                'target_muscle_groups' => json_encode(['Corps entier', 'Système cardiovasculaire']),
                'age_min' => 16,
                'age_max' => 75,
                'imc_min' => 16.0,
                'imc_max' => 35.0,
                'contraindications' => json_encode([]),
                'video_url' => 'https://www.youtube.com/embed/elliptical'
            ],

            [
                'name' => 'HIIT Débutant',
                'type' => 'cardio',
                'category' => 'intermediate',
                'difficulty_level' => 7,
                'duration_min' => 15,
                'duration_max' => 30,
                'calories_per_minute' => 15.0,
                'description' => 'Entraînement par intervalles haute intensité',
                'instructions' => '30s effort intense, 30s récupération, répétez 10-15 fois.',
                'equipment_needed' => json_encode(['Aucun équipement']),
                'target_muscle_groups' => json_encode(['Corps entier', 'Système cardiovasculaire']),
                'age_min' => 18,
                'age_max' => 55,
                'imc_min' => 18.0,
                'imc_max' => 30.0,
                'contraindications' => json_encode(['Problèmes cardiaques', 'Hypertension']),
                'video_url' => 'https://www.youtube.com/embed/hiit-beginner'
            ],

            [
                'name' => 'Course à pied intense',
                'type' => 'cardio',
                'category' => 'advanced',
                'difficulty_level' => 8,
                'duration_min' => 30,
                'duration_max' => 90,
                'calories_per_minute' => 16.0,
                'description' => 'Course soutenue pour sportifs confirmés',
                'instructions' => 'Maintenez un rythme soutenu, contrôlez votre respiration.',
                'equipment_needed' => json_encode(['Chaussures de course']),
                'target_muscle_groups' => json_encode(['Jambes', 'Système cardiovasculaire', 'Core']),
                'age_min' => 18,
                'age_max' => 60,
                'imc_min' => 18.0,
                'imc_max' => 28.0,
                'contraindications' => json_encode(['Problèmes cardiaques', 'Blessures récentes']),
                'video_url' => 'https://www.youtube.com/embed/running-intense'
            ],

            [
                'name' => 'HIIT Avancé',
                'type' => 'cardio',
                'category' => 'advanced',
                'difficulty_level' => 9,
                'duration_min' => 20,
                'duration_max' => 45,
                'calories_per_minute' => 18.0,
                'description' => 'HIIT intense avec exercices complexes',
                'instructions' => '45s effort maximal, 15s récupération, 15-20 rounds.',
                'equipment_needed' => json_encode(['Aucun équipement']),
                'target_muscle_groups' => json_encode(['Corps entier', 'Système cardiovasculaire']),
                'age_min' => 20,
                'age_max' => 45,
                'imc_min' => 18.5,
                'imc_max' => 26.0,
                'contraindications' => json_encode(['Débutants', 'Problèmes cardiaques']),
                'video_url' => 'https://www.youtube.com/embed/hiit-advanced'
            ],

            // ============ EXERCICES DE FORCE ============

            [
                'name' => 'Squats assistés',
                'type' => 'strength',
                'category' => 'beginner',
                'difficulty_level' => 3,
                'duration_min' => 5,
                'duration_max' => 15,
                'calories_per_minute' => 6.0,
                'description' => 'Squats avec support pour débuter',
                'instructions' => 'Utilisez une chaise comme support, descendez lentement.',
                'equipment_needed' => json_encode(['Chaise']),
                'target_muscle_groups' => json_encode(['Quadriceps', 'Fessiers']),
                'age_min' => 14,
                'age_max' => 80,
                'imc_min' => 16.0,
                'imc_max' => 40.0,
                'contraindications' => json_encode(['Problèmes de genoux sévères']),
                'video_url' => 'https://www.youtube.com/embed/squats-assisted'
            ],

            [
                'name' => 'Pompes sur genoux',
                'type' => 'strength',
                'category' => 'beginner',
                'difficulty_level' => 4,
                'duration_min' => 5,
                'duration_max' => 15,
                'calories_per_minute' => 7.0,
                'description' => 'Version modifiée des pompes pour débutants',
                'instructions' => 'Genoux au sol, descendez le torse en contrôlant.',
                'equipment_needed' => json_encode(['Tapis d\'exercice']),
                'target_muscle_groups' => json_encode(['Pectoraux', 'Triceps', 'Épaules']),
                'age_min' => 12,
                'age_max' => 75,
                'imc_min' => 16.0,
                'imc_max' => 35.0,
                'contraindications' => json_encode(['Problèmes de poignets']),
                'video_url' => 'https://www.youtube.com/embed/pushups-knees'
            ],

            [
                'name' => 'Planche genoux',
                'type' => 'strength',
                'category' => 'beginner',
                'difficulty_level' => 3,
                'duration_min' => 3,
                'duration_max' => 10,
                'calories_per_minute' => 5.0,
                'description' => 'Renforcement du core version débutant',
                'instructions' => 'Maintenez la position, genoux au sol, corps aligné.',
                'equipment_needed' => json_encode(['Tapis d\'exercice']),
                'target_muscle_groups' => json_encode(['Core', 'Épaules']),
                'age_min' => 12,
                'age_max' => 80,
                'imc_min' => 16.0,
                'imc_max' => 40.0,
                'contraindications' => json_encode([]),
                'video_url' => 'https://www.youtube.com/embed/plank-knees'
            ],

            [
                'name' => 'Squats au poids du corps',
                'type' => 'strength',
                'category' => 'intermediate',
                'difficulty_level' => 5,
                'duration_min' => 10,
                'duration_max' => 25,
                'calories_per_minute' => 8.0,
                'description' => 'Squats classiques pour renforcer les jambes',
                'instructions' => 'Pieds largeur d\'épaules, descendez comme pour vous asseoir.',
                'equipment_needed' => json_encode(['Aucun équipement']),
                'target_muscle_groups' => json_encode(['Quadriceps', 'Fessiers', 'Mollets']),
                'age_min' => 14,
                'age_max' => 75,
                'imc_min' => 16.0,
                'imc_max' => 35.0,
                'contraindications' => json_encode(['Problèmes de genoux sévères']),
                'video_url' => 'https://www.youtube.com/embed/squats-bodyweight'
            ],

            [
                'name' => 'Pompes classiques',
                'type' => 'strength',
                'category' => 'intermediate',
                'difficulty_level' => 6,
                'duration_min' => 8,
                'duration_max' => 20,
                'calories_per_minute' => 9.0,
                'description' => 'Pompes traditionnelles pour le haut du corps',
                'instructions' => 'Position de planche, descendez en contrôlant, remontez.',
                'equipment_needed' => json_encode(['Aucun équipement']),
                'target_muscle_groups' => json_encode(['Pectoraux', 'Triceps', 'Épaules', 'Core']),
                'age_min' => 14,
                'age_max' => 70,
                'imc_min' => 16.0,
                'imc_max' => 32.0,
                'contraindications' => json_encode(['Problèmes d\'épaules', 'Problèmes de poignets']),
                'video_url' => 'https://www.youtube.com/embed/pushups-classic'
            ],

            // ============ EXERCICES DE FLEXIBILITÉ ============

            [
                'name' => 'Étirements doux',
                'type' => 'flexibility',
                'category' => 'beginner',
                'difficulty_level' => 1,
                'duration_min' => 5,
                'duration_max' => 20,
                'calories_per_minute' => 2.0,
                'description' => 'Étirements légers pour tous',
                'instructions' => 'Maintenez chaque position 15-20 secondes sans forcer.',
                'equipment_needed' => json_encode(['Aucun équipement']),
                'target_muscle_groups' => json_encode(['Corps entier', 'Flexibilité']),
                'age_min' => 8,
                'age_max' => 95,
                'imc_min' => 15.0,
                'imc_max' => 45.0,
                'contraindications' => json_encode([]),
                'video_url' => 'https://www.youtube.com/embed/stretching-gentle'
            ],

            [
                'name' => 'Yoga débutant',
                'type' => 'flexibility',
                'category' => 'beginner',
                'difficulty_level' => 3,
                'duration_min' => 15,
                'duration_max' => 45,
                'calories_per_minute' => 3.0,
                'description' => 'Séance de yoga pour débutants',
                'instructions' => 'Enchaînements doux, respirez profondément.',
                'equipment_needed' => json_encode(['Tapis de yoga']),
                'target_muscle_groups' => json_encode(['Corps entier', 'Flexibilité', 'Mental']),
                'age_min' => 12,
                'age_max' => 85,
                'imc_min' => 15.0,
                'imc_max' => 40.0,
                'contraindications' => json_encode([]),
                'video_url' => 'https://www.youtube.com/embed/yoga-beginner'
            ],

            // ============ EXERCICES D'ÉQUILIBRE ============

            [
                'name' => 'Tai Chi débutant',
                'type' => 'balance',
                'category' => 'beginner',
                'difficulty_level' => 2,
                'duration_min' => 10,
                'duration_max' => 30,
                'calories_per_minute' => 3.5,
                'description' => 'Art martial doux pour l\'équilibre',
                'instructions' => 'Mouvements lents et contrôlés, concentration.',
                'equipment_needed' => json_encode(['Aucun équipement']),
                'target_muscle_groups' => json_encode(['Équilibre', 'Core', 'Mental']),
                'age_min' => 15,
                'age_max' => 90,
                'imc_min' => 15.0,
                'imc_max' => 40.0,
                'contraindications' => json_encode([]),
                'video_url' => 'https://www.youtube.com/embed/taichi-beginner'
            ],

            [
                'name' => 'Exercices d\'équilibre',
                'type' => 'balance',
                'category' => 'beginner',
                'difficulty_level' => 3,
                'duration_min' => 5,
                'duration_max' => 15,
                'calories_per_minute' => 4.0,
                'description' => 'Exercices spécifiques pour améliorer l\'équilibre',
                'instructions' => 'Tenez-vous sur une jambe, utilisez un support si nécessaire.',
                'equipment_needed' => json_encode(['Chaise (optionnelle)']),
                'target_muscle_groups' => json_encode(['Équilibre', 'Proprioception', 'Core']),
                'age_min' => 12,
                'age_max' => 85,
                'imc_min' => 15.0,
                'imc_max' => 40.0,
                'contraindications' => json_encode(['Vertiges fréquents']),
                'video_url' => 'https://www.youtube.com/embed/balance-exercises'
            ],

            // ============ EXERCICES AQUATIQUES ============

            [
                'name' => 'Aquagym débutant',
                'type' => 'cardio',
                'category' => 'beginner',
                'difficulty_level' => 3,
                'duration_min' => 20,
                'duration_max' => 45,
                'calories_per_minute' => 7.0,
                'description' => 'Exercices dans l\'eau, doux pour les articulations',
                'instructions' => 'Mouvements dans l\'eau, profitez de la résistance naturelle.',
                'equipment_needed' => json_encode(['Piscine', 'Maillot de bain']),
                'target_muscle_groups' => json_encode(['Corps entier', 'Système cardiovasculaire']),
                'age_min' => 12,
                'age_max' => 85,
                'imc_min' => 15.0,
                'imc_max' => 45.0,
                'contraindications' => json_encode(['Peur de l\'eau', 'Infections cutanées']),
                'video_url' => 'https://www.youtube.com/embed/aquagym-beginner'
            ],

            // ============ EXERCICES FONCTIONNELS ============

            [
                'name' => 'Montées de genoux',
                'type' => 'cardio',
                'category' => 'beginner',
                'difficulty_level' => 4,
                'duration_min' => 5,
                'duration_max' => 15,
                'calories_per_minute' => 8.0,
                'description' => 'Exercice cardio sur place',
                'instructions' => 'Alternez les genoux vers la poitrine, gardez le rythme.',
                'equipment_needed' => json_encode(['Aucun équipement']),
                'target_muscle_groups' => json_encode(['Jambes', 'Core', 'Système cardiovasculaire']),
                'age_min' => 12,
                'age_max' => 75,
                'imc_min' => 16.0,
                'imc_max' => 35.0,
                'contraindications' => json_encode(['Problèmes de hanches']),
                'video_url' => 'https://www.youtube.com/embed/knee-raises'
            ],

            [
                'name' => 'Jumping Jacks modifiés',
                'type' => 'cardio',
                'category' => 'beginner',
                'difficulty_level' => 4,
                'duration_min' => 3,
                'duration_max' => 10,
                'calories_per_minute' => 9.0,
                'description' => 'Version adaptée des jumping jacks',
                'instructions' => 'Alternez pas chassés sans saut si nécessaire.',
                'equipment_needed' => json_encode(['Aucun équipement']),
                'target_muscle_groups' => json_encode(['Corps entier', 'Système cardiovasculaire']),
                'age_min' => 15,
                'age_max' => 70,
                'imc_min' => 16.0,
                'imc_max' => 32.0,
                'contraindications' => json_encode(['Problèmes articulaires']),
                'video_url' => 'https://www.youtube.com/embed/jumping-jacks-modified'
            ]
        ];

        // ⚠️ SOLUTION : Supprimer d'abord les références, puis les exercices
        $this->command->info('🗑️ Suppression des anciennes recommandations...');
        DB::table('exercise_recommendations')->delete();
        
        $this->command->info('🗑️ Suppression des anciens exercices...');
        DB::table('exercises')->delete();

        // Insertion des nouveaux exercices
        $this->command->info('📥 Insertion des nouveaux exercices...');
        foreach ($exercises as $exerciseData) {
            Exercise::create($exerciseData);
        }

        $this->command->info('✅ ' . count($exercises) . ' exercices créés avec succès !');
        $this->command->info('📊 Répartition :');
        $this->command->info('   - Cardio : ' . collect($exercises)->where('type', 'cardio')->count());
        $this->command->info('   - Force : ' . collect($exercises)->where('type', 'strength')->count());
        $this->command->info('   - Flexibilité : ' . collect($exercises)->where('type', 'flexibility')->count());
        $this->command->info('   - Équilibre : ' . collect($exercises)->where('type', 'balance')->count());
    }
}
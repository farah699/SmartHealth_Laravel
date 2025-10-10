<?php
// filepath: c:\Users\ferie\OneDrive\Bureau\projetLaravel\SmartHealth_Laravel\database\seeders\UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');
        
        // Créer 40000 utilisateurs avec des profils variés pour l'IA
        $this->command->info('🤖 Génération de 40000 utilisateurs pour l\'entraînement IA...');
        
        for ($i = 0; $i < 40000; $i++) {
            $age = $faker->numberBetween(16, 75);
            $height = $faker->numberBetween(150, 200); // cm
            $gender = $faker->randomElement(['male', 'female']);
            
            // Générer un poids réaliste selon l'âge, la taille et le genre
            $baseWeight = $this->calculateBaseWeight($height, $gender, $age);
            $weightVariation = $faker->numberBetween(-25, 35);
            $weight = max(40, $baseWeight + $weightVariation);
            
            // Calcul de l'IMC avant création
            $heightInMeters = $height / 100;
            $imc = round($weight / ($heightInMeters * $heightInMeters), 1);
            $imcCategory = $this->getImcCategory($imc);
            
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password123'),
                'age' => $age,
                'height' => $height,
                'weight' => round($weight, 1),
                'imc' => $imc,  // ✅ Ajout de l'IMC calculé
                'imc_category' => $imcCategory,  // ✅ Ajout de la catégorie
                'imc_calculated_at' => now(),  // ✅ Date de calcul
                'gender' => $gender,
                'role' => $faker->randomElement(['Student', 'Teacher']),
                'enabled' => $faker->boolean(95), // 95% activés
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'birth_date' => now()->subYears($age)->subDays($faker->numberBetween(0, 365)),
                'bio' => $faker->optional(0.3)->sentence,
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
            ]);
            
            // Affichage du progrès
            if ($i % 5000 == 0) {
                $this->command->info("📊 {$i}/40000 utilisateurs créés...");
            }
        }

        $this->command->info('✅ 40000 utilisateurs créés avec IMC calculé');
        $this->displayStats();
    }
    
    /**
     * Calculer le poids de base selon la morphologie
     */
    private function calculateBaseWeight($height, $gender, $age): float
    {
        // Formule de Lorentz ajustée
        $baseWeight = $height - 100 - (($height - 150) / 4);
        
        // Ajustement selon le genre
        if ($gender === 'female') {
            $baseWeight -= 2.5;
        }
        
        // Ajustement selon l'âge (métabolisme)
        if ($age > 50) {
            $baseWeight += 3;
        } elseif ($age < 25) {
            $baseWeight -= 2;
        }
        
        return max(45, $baseWeight);
    }
    
    /**
     * Obtenir la catégorie IMC
     */
    private function getImcCategory(float $imc): string
    {
        if ($imc < 16.5) {
            return 'Dénutrition';
        } elseif ($imc < 18.5) {
            return 'Maigreur';
        } elseif ($imc < 25) {
            return 'Corpulence normale';
        } elseif ($imc < 30) {
            return 'Surpoids';
        } elseif ($imc < 35) {
            return 'Obésité modérée';
        } elseif ($imc < 40) {
            return 'Obésité sévère';
        } else {
            return 'Obésité morbide';
        }
    }
    
    /**
     * Afficher les statistiques de génération
     */
    private function displayStats()
    {
        $stats = User::selectRaw('
            imc_category,
            COUNT(*) as count,
            AVG(age) as avg_age,
            AVG(imc) as avg_imc
        ')
        ->groupBy('imc_category')
        ->get();
        
        $this->command->info('📊 STATISTIQUES DU DATASET :');
        $this->command->table(
            ['Catégorie IMC', 'Nombre', 'Âge moyen', 'IMC moyen'],
            $stats->map(function($stat) {
                return [
                    $stat->imc_category,
                    $stat->count,
                    round($stat->avg_age, 1),
                    round($stat->avg_imc, 1)
                ];
            })->toArray()
        );
    }
}
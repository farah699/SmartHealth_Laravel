<?php



namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer des utilisateurs de test si nécessaire
        if (User::count() === 0) {
            User::factory(10)->create();

            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
// Appeler le seeder des ressources
        $this->call(ResourceSeeder::class);
        //je met ça pour lancer le seeder de blogs (baha)
         $this->call([
            // 1. Créer les utilisateurs avec profils variés
            UserSeeder::class,
        ]);
          $this->call([
            // Exercices en premier (avant les recommandations)
            ExerciseSeeder::class,
        ]);
  // Appeler le seeder des activités
        $this->call([
            ActivitySeeder::class,
            
        ]);


         $this->call([  ExerciseRecommendationSeeder::class, ]);

    


        $this->call([
        BlogSeeder::class,  // <- Ajoutez juste ça
    ]);
        $this->call([
            // Créer d'abord les utilisateurs si pas déjà fait
            // UserSeeder::class,
            
            // Puis les entrées alimentaires
            FoodEntrySeeder::class,
        ]);

        // Appeler le seeder des activités
        $this->call([
            ActivitySeeder::class,
            
        ]);
    }
}
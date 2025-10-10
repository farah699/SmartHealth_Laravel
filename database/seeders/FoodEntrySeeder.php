<?php


namespace Database\Seeders;

use App\Models\FoodEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FoodEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier qu'il y a des utilisateurs
        $userCount = User::count();
        if ($userCount === 0) {
            $this->command->error('Aucun utilisateur trouvé. Veuillez d\'abord créer des utilisateurs.');
            return;
        }

        $this->command->info('🍎 Génération des entrées alimentaires...');

        // Récupérer tous les utilisateurs existants
        $users = User::all();

        foreach ($users as $user) {
            $this->command->info("📊 Génération pour l'utilisateur: {$user->name}");

            // Générer des données pour les 7 derniers jours
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $this->createDailyMealsForUser($user, $date);
            }

            // Ajouter quelques entrées aléatoires dans le mois passé
            FoodEntry::factory(15)
                ->for($user)
                ->create([
                    'entry_date' => fake()->dateTimeBetween('-30 days', '-8 days'),
                ]);
        }

        $totalEntries = FoodEntry::count();
        $this->command->info("✅ {$totalEntries} entrées alimentaires créées avec succès!");
    }

    /**
     * Créer les repas quotidiens pour un utilisateur
     */
    private function createDailyMealsForUser(User $user, Carbon $date): void
    {
        // Petit-déjeuner (90% de chance)
        if (fake()->boolean(90)) {
            FoodEntry::factory()
                ->for($user)
                ->breakfast()
                ->create([
                    'entry_date' => $date,
                    'entry_time' => fake()->time('07:00:00', '09:30:00'),
                ]);

            // Parfois un deuxième élément pour le petit-déjeuner
            if (fake()->boolean(40)) {
                FoodEntry::factory()
                    ->for($user)
                    ->breakfast()
                    ->create([
                        'entry_date' => $date,
                        'entry_time' => fake()->time('07:30:00', '09:30:00'),
                    ]);
            }
        }

        // Collation matinale (30% de chance)
        if (fake()->boolean(30)) {
            FoodEntry::factory()
                ->for($user)
                ->snack()
                ->create([
                    'entry_date' => $date,
                    'entry_time' => fake()->time('10:00:00', '11:00:00'),
                ]);
        }

        // Déjeuner (95% de chance)
        if (fake()->boolean(95)) {
            FoodEntry::factory()
                ->for($user)
                ->lunch()
                ->create([
                    'entry_date' => $date,
                    'entry_time' => fake()->time('12:00:00', '14:00:00'),
                ]);

            // Parfois un accompagnement
            if (fake()->boolean(60)) {
                FoodEntry::factory()
                    ->for($user)
                    ->lunch()
                    ->create([
                        'entry_date' => $date,
                        'entry_time' => fake()->time('12:00:00', '14:00:00'),
                    ]);
            }
        }

        // Collation après-midi (40% de chance)
        if (fake()->boolean(40)) {
            FoodEntry::factory()
                ->for($user)
                ->snack()
                ->create([
                    'entry_date' => $date,
                    'entry_time' => fake()->time('15:00:00', '17:00:00'),
                ]);
        }

        // Dîner (90% de chance)
        if (fake()->boolean(90)) {
            FoodEntry::factory()
                ->for($user)
                ->dinner()
                ->create([
                    'entry_date' => $date,
                    'entry_time' => fake()->time('18:30:00', '21:00:00'),
                ]);

            // Parfois un accompagnement
            if (fake()->boolean(50)) {
                FoodEntry::factory()
                    ->for($user)
                    ->dinner()
                    ->create([
                        'entry_date' => $date,
                        'entry_time' => fake()->time('18:30:00', '21:00:00'),
                    ]);
            }
        }

        // Collation soirée (20% de chance)
        if (fake()->boolean(20)) {
            FoodEntry::factory()
                ->for($user)
                ->snack()
                ->create([
                    'entry_date' => $date,
                    'entry_time' => fake()->time('21:30:00', '23:00:00'),
                ]);
        }
    }
}
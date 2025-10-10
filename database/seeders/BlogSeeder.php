<?php
// filepath: c:\Users\Lenovo\Desktop\laravel\SmartHealth_Laravel\database\seeders\BlogSeeder.php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des utilisateurs si aucun n'existe
        if (User::count() == 0) {
            User::factory(3)->create(); // Réduire à 3 utilisateurs
        }

        // Récupérer les utilisateurs existants
        $users = User::all();

        // Créer exactement 10 blogs au total
        Blog::factory(10)
            ->recycle($users) // Réutilise les utilisateurs existants
            ->create();

        // Si vous voulez créer un blog pour l'utilisateur de test (compris dans les 10)
        $testUser = User::where('email', 'test@example.com')->first();
        if ($testUser && Blog::count() < 10) {
            Blog::factory(1)
                ->forUser($testUser)
                ->create();
        }

        $this->command->info('10 blogs créés avec succès !');
    }
}
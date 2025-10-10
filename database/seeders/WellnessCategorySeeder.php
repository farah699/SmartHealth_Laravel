<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WellnessCategory;

class WellnessCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Révisions', 'color' => '#3498db', 'icon' => 'bi-book', 'description' => 'Sessions d\'étude et révisions'],
            ['name' => 'Pauses', 'color' => '#2ecc71', 'icon' => 'bi-pause-circle', 'description' => 'Pauses et temps de repos'],
            ['name' => 'Méditation', 'color' => '#9b59b6', 'icon' => 'bi-flower1', 'description' => 'Méditation et mindfulness'],
            ['name' => 'Exercice', 'color' => '#e74c3c', 'icon' => 'bi-heart-pulse', 'description' => 'Activité physique'],
            ['name' => 'Détente', 'color' => '#f39c12', 'icon' => 'bi-cup-hot', 'description' => 'Moments de détente'],
            ['name' => 'Sommeil', 'color' => '#34495e', 'icon' => 'bi-moon', 'description' => 'Planification du sommeil'],
        ];

        foreach ($categories as $category) {
            WellnessCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
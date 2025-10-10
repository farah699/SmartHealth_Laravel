<?php
// filepath: app/Models/WellnessCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellnessCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec les événements
     */
    public function events()
    {
        return $this->hasMany(WellnessEvent::class);
    }

    /**
     * Catégories par défaut
     */
    public static function getDefaultCategories()
    {
        return [
            ['name' => 'Révisions', 'color' => '#3498db', 'icon' => 'bi-book', 'description' => 'Sessions d\'étude et révisions'],
            ['name' => 'Pauses', 'color' => '#2ecc71', 'icon' => 'bi-pause-circle', 'description' => 'Pauses et temps de repos'],
            ['name' => 'Méditation', 'color' => '#9b59b6', 'icon' => 'bi-flower1', 'description' => 'Méditation et mindfulness'],
            ['name' => 'Exercice', 'color' => '#e74c3c', 'icon' => 'bi-heart-pulse', 'description' => 'Activité physique'],
            ['name' => 'Détente', 'color' => '#f39c12', 'icon' => 'bi-cup-hot', 'description' => 'Moments de détente'],
            ['name' => 'Sommeil', 'color' => '#34495e', 'icon' => 'bi-moon', 'description' => 'Planification du sommeil'],
        ];
    }
}
<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'gender', 'height', 'weight',  // Supprimé 'age'
        'activity_level', 'goal', 'bmr', 'tdee', 
        'daily_calories', 'daily_water_ml'
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'daily_water_ml' => 'decimal:2',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Calculs automatiques
    public function calculateBMR(): int
    {
        if (!$this->gender || !$this->user->age || !$this->height || !$this->weight) {
            return 0;
        }

        // Formule Mifflin-St Jeor - utilise $this->user->age
        if ($this->gender === 'male') {
            return round((10 * $this->weight) + (6.25 * $this->height) - (5 * $this->user->age) + 5);
        } else {
            return round((10 * $this->weight) + (6.25 * $this->height) - (5 * $this->user->age) - 161);
        }
    }

    public function calculateTDEE(): int
    {
        $bmr = $this->calculateBMR();
        
        $multipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9
        ];

        return round($bmr * ($multipliers[$this->activity_level] ?? 1.2));
    }

    public function calculateDailyCalories(): int
    {
        $tdee = $this->calculateTDEE();
        
        return match($this->goal) {
            'lose' => $tdee - 500,    // Déficit de 500 kcal
            'gain' => $tdee + 500,    // Surplus de 500 kcal
            'maintain' => $tdee,      // Maintenance
            default => $tdee
        };
    }

    public function calculateDailyWater(): float
    {
        // 35 ml par kg de poids corporel
        return $this->weight ? round($this->weight * 35, 2) : 2000;
    }

    // Méthode pour recalculer tous les objectifs
    public function updateCalculations(): void
    {
        $this->bmr = $this->calculateBMR();
        $this->tdee = $this->calculateTDEE();
        $this->daily_calories = $this->calculateDailyCalories();
        $this->daily_water_ml = $this->calculateDailyWater();
        $this->save();
    }

    // Accesseurs pour l'affichage
    public function getActivityLevelLabelAttribute(): string
    {
        return match($this->activity_level) {
            'sedentary' => 'Sédentaire',
            'light' => 'Légère activité',
            'moderate' => 'Activité modérée',
            'active' => 'Très actif',
            'very_active' => 'Extrêmement actif',
            default => 'Non défini'
        };
    }

    public function getGoalLabelAttribute(): string
    {
        return match($this->goal) {
            'lose' => 'Perdre du poids',
            'gain' => 'Prendre du poids',
            'maintain' => 'Maintenir le poids',
            default => 'Non défini'
        };
    }
}
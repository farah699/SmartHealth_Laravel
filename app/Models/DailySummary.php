<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DailySummary extends Model
{
    protected $fillable = [
        'user_id', 'summary_date', 'total_calories', 'total_proteins',
        'total_carbs', 'total_fats', 'total_fiber', 'total_water_ml',
        'calorie_goal', 'water_goal_ml', 'calorie_percentage', 'water_percentage'
    ];

    protected $casts = [
        'summary_date' => 'date',
        'total_calories' => 'decimal:2',
        'total_proteins' => 'decimal:2',
        'total_carbs' => 'decimal:2',
        'total_fats' => 'decimal:2',
        'total_fiber' => 'decimal:2',
        'total_water_ml' => 'decimal:2',
        'water_goal_ml' => 'decimal:2',
        'calorie_percentage' => 'decimal:2',
        'water_percentage' => 'decimal:2'
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('summary_date', Carbon::today());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Méthodes statiques pour créer/mettre à jour le résumé
    public static function updateForUser($userId, $date = null): self
    {
        $date = $date ?? Carbon::today();
        
        // Récupérer les entrées du jour
        $foodEntries = FoodEntry::forUser($userId)->whereDate('entry_date', $date)->get();
        $hydrationEntries = HydrationEntry::forUser($userId)->whereDate('entry_date', $date)->get();
        
        // Calculer les totaux alimentaires
        $totalCalories = $foodEntries->sum('total_calories');
        $totalProteins = $foodEntries->sum('proteins');
        $totalCarbs = $foodEntries->sum('carbs');
        $totalFats = $foodEntries->sum('fats');
        $totalFiber = $foodEntries->sum('fiber');
        
        // Calculer le total d'hydratation
        $totalWater = $hydrationEntries->sum('amount_ml');
        
        // Récupérer les objectifs de l'utilisateur
        $user = User::with('profile')->find($userId);
        $calorieGoal = $user->profile->daily_calories ?? 2000;
        $waterGoal = $user->profile->daily_water_ml ?? 2000;
        
        // Calculer les pourcentages
        $caloriePercentage = $calorieGoal > 0 ? round(($totalCalories / $calorieGoal) * 100, 2) : 0;
        $waterPercentage = $waterGoal > 0 ? round(($totalWater / $waterGoal) * 100, 2) : 0;
        
        // Créer ou mettre à jour le résumé
        return self::updateOrCreate(
            ['user_id' => $userId, 'summary_date' => $date],
            [
                'total_calories' => $totalCalories,
                'total_proteins' => $totalProteins,
                'total_carbs' => $totalCarbs,
                'total_fats' => $totalFats,
                'total_fiber' => $totalFiber,
                'total_water_ml' => $totalWater,
                'calorie_goal' => $calorieGoal,
                'water_goal_ml' => $waterGoal,
                'calorie_percentage' => $caloriePercentage,
                'water_percentage' => $waterPercentage,
            ]
        );
    }

    // Accesseurs pour l'affichage
    public function getCalorieStatusAttribute(): string
    {
        if ($this->calorie_percentage >= 100) return 'success';
        if ($this->calorie_percentage >= 80) return 'warning';
        return 'danger';
    }

    public function getWaterStatusAttribute(): string
    {
        if ($this->water_percentage >= 100) return 'success';
        if ($this->water_percentage >= 80) return 'warning';
        return 'danger';
    }

    public function getRemainingCaloriesAttribute(): int
    {
        return max(0, $this->calorie_goal - $this->total_calories);
    }

    public function getRemainingWaterAttribute(): float
    {
        return max(0, $this->water_goal_ml - $this->total_water_ml);
    }
}
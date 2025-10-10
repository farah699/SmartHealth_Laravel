<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FoodEntry extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id', 'food_name', 'quantity', 'unit', 'meal_type',
        'calories_per_100g', 'total_calories', 'proteins', 'carbs', 
        'fats', 'fiber', 'entry_date', 'entry_time', 'notes', 'api_response'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'calories_per_100g' => 'decimal:2',
        'total_calories' => 'decimal:2',
        'proteins' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fats' => 'decimal:2',
        'fiber' => 'decimal:2',
        'entry_date' => 'date',
        'entry_time' => 'datetime:H:i',
        'api_response' => 'array'
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('entry_date', Carbon::today());
    }

    public function scopeByMealType($query, $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accesseurs
    public function getMealTypeLabelAttribute(): string
    {
        return match($this->meal_type) {
            'breakfast' => 'Petit-déjeuner',
            'lunch' => 'Déjeuner',
            'dinner' => 'Dîner',
            'snack' => 'Collation',
            default => 'Non défini'
        };
    }

    public function getFormattedQuantityAttribute(): string
    {
        return $this->quantity . ' ' . $this->unit;
    }

    // Calculer les calories totales
    public function calculateTotalCalories(): float
    {
        if (!$this->calories_per_100g || !$this->quantity) {
            return 0;
        }

        // Convertir selon l'unité
        $quantityInGrams = $this->convertToGrams();
        return round(($this->calories_per_100g * $quantityInGrams) / 100, 2);
    }

    // Convertir la quantité en grammes
    private function convertToGrams(): float
    {
        return match($this->unit) {
            'g' => $this->quantity,
            'kg' => $this->quantity * 1000,
            'ml' => $this->quantity, // 1ml ≈ 1g pour la plupart des liquides
            'l' => $this->quantity * 1000,
            'cup' => $this->quantity * 240, // 1 cup ≈ 240g
            'tbsp' => $this->quantity * 15, // 1 tbsp ≈ 15g
            'tsp' => $this->quantity * 5,   // 1 tsp ≈ 5g
            'piece' => $this->quantity * 100, // Estimation : 1 pièce ≈ 100g
            default => $this->quantity
        };
    }
}
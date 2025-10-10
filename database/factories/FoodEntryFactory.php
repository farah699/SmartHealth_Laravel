<?php


namespace Database\Factories;

use App\Models\FoodEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class FoodEntryFactory extends Factory
{
    protected $model = FoodEntry::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // Données réalistes d'aliments avec leurs valeurs nutritionnelles (pour 100g)
        $foods = [
            // Fruits
            ['name' => 'Apple', 'calories' => 52, 'proteins' => 0.3, 'carbs' => 13.8, 'fats' => 0.2, 'fiber' => 2.4],
            ['name' => 'Banana', 'calories' => 89, 'proteins' => 1.1, 'carbs' => 23, 'fats' => 0.3, 'fiber' => 2.6],
            ['name' => 'Orange', 'calories' => 47, 'proteins' => 0.9, 'carbs' => 11.8, 'fats' => 0.1, 'fiber' => 2.4],
            ['name' => 'Strawberry', 'calories' => 32, 'proteins' => 0.7, 'carbs' => 7.7, 'fats' => 0.3, 'fiber' => 2.0],
            
            // Légumes
            ['name' => 'Broccoli', 'calories' => 34, 'proteins' => 2.8, 'carbs' => 7, 'fats' => 0.4, 'fiber' => 2.6],
            ['name' => 'Carrot', 'calories' => 41, 'proteins' => 0.9, 'carbs' => 9.6, 'fats' => 0.2, 'fiber' => 2.8],
            ['name' => 'Spinach', 'calories' => 23, 'proteins' => 2.9, 'carbs' => 3.6, 'fats' => 0.4, 'fiber' => 2.2],
            ['name' => 'Tomato', 'calories' => 18, 'proteins' => 0.9, 'carbs' => 3.9, 'fats' => 0.2, 'fiber' => 1.2],
            
            // Protéines
            ['name' => 'Chicken Breast', 'calories' => 165, 'proteins' => 31, 'carbs' => 0, 'fats' => 3.6, 'fiber' => 0],
            ['name' => 'Salmon', 'calories' => 208, 'proteins' => 20, 'carbs' => 0, 'fats' => 13, 'fiber' => 0],
            ['name' => 'Eggs', 'calories' => 155, 'proteins' => 13, 'carbs' => 1.1, 'fats' => 11, 'fiber' => 0],
            ['name' => 'Tuna', 'calories' => 144, 'proteins' => 23, 'carbs' => 0, 'fats' => 4.9, 'fiber' => 0],
            
            // Féculents
            ['name' => 'Brown Rice', 'calories' => 111, 'proteins' => 2.6, 'carbs' => 23, 'fats' => 0.9, 'fiber' => 1.8],
            ['name' => 'Pasta', 'calories' => 131, 'proteins' => 5, 'carbs' => 25, 'fats' => 1.1, 'fiber' => 1.8],
            ['name' => 'Bread', 'calories' => 265, 'proteins' => 9, 'carbs' => 49, 'fats' => 3.2, 'fiber' => 2.7],
            ['name' => 'Oats', 'calories' => 389, 'proteins' => 16.9, 'carbs' => 66.3, 'fats' => 6.9, 'fiber' => 10.6],
            
            // Produits laitiers
            ['name' => 'Greek Yogurt', 'calories' => 59, 'proteins' => 10, 'carbs' => 3.6, 'fats' => 0.4, 'fiber' => 0],
            ['name' => 'Milk', 'calories' => 42, 'proteins' => 3.4, 'carbs' => 5, 'fats' => 1, 'fiber' => 0],
            ['name' => 'Cheese', 'calories' => 113, 'proteins' => 7, 'carbs' => 1, 'fats' => 9, 'fiber' => 0],
            
            // Noix et graines
            ['name' => 'Almonds', 'calories' => 579, 'proteins' => 21.2, 'carbs' => 21.6, 'fats' => 49.9, 'fiber' => 12.5],
            ['name' => 'Walnuts', 'calories' => 654, 'proteins' => 15.2, 'carbs' => 13.7, 'fats' => 65.2, 'fiber' => 6.7],
        ];

        $selectedFood = $this->faker->randomElement($foods);
        
        // Unités possibles avec leurs quantités typiques
        $units = [
            'g' => $this->faker->numberBetween(50, 300),
            'piece' => $this->faker->numberBetween(1, 3),
            'cup' => $this->faker->randomFloat(1, 0.25, 2),
            'tbsp' => $this->faker->numberBetween(1, 4),
            'ml' => $this->faker->numberBetween(100, 500),
        ];

        $selectedUnit = $this->faker->randomKey($units);
        $quantity = $units[$selectedUnit];

        // Calculer les calories totales selon la quantité
        $quantityInGrams = $this->convertToGrams($quantity, $selectedUnit);
        $totalCalories = round(($selectedFood['calories'] * $quantityInGrams) / 100, 2);
        $totalProteins = round(($selectedFood['proteins'] * $quantityInGrams) / 100, 2);
        $totalCarbs = round(($selectedFood['carbs'] * $quantityInGrams) / 100, 2);
        $totalFats = round(($selectedFood['fats'] * $quantityInGrams) / 100, 2);
        $totalFiber = round(($selectedFood['fiber'] * $quantityInGrams) / 100, 2);

        // Générer une date aléatoire dans les 30 derniers jours
        $entryDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $entryTime = $this->faker->time('H:i:s');

        return [
            'user_id' => User::factory(),
            'food_name' => $selectedFood['name'],
            'quantity' => $quantity,
            'unit' => $selectedUnit,
            'meal_type' => $this->faker->randomElement(['breakfast', 'lunch', 'dinner', 'snack']),
            'calories_per_100g' => $selectedFood['calories'],
            'total_calories' => $totalCalories,
            'proteins' => $totalProteins,
            'carbs' => $totalCarbs,
            'fats' => $totalFats,
            'fiber' => $totalFiber,
            'entry_date' => $entryDate,
            'entry_time' => $entryTime,
            'notes' => $this->faker->optional(0.3)->sentence(), // 30% de chance d'avoir une note
        ];
    }

    /**
     * Convertir la quantité en grammes
     */
    private function convertToGrams(float $quantity, string $unit): float
    {
        return match($unit) {
            'g' => $quantity,
            'kg' => $quantity * 1000,
            'ml' => $quantity, // 1ml ≈ 1g pour la plupart des liquides
            'l' => $quantity * 1000,
            'cup' => $quantity * 240, // 1 cup ≈ 240g
            'tbsp' => $quantity * 15, // 1 tbsp ≈ 15g
            'tsp' => $quantity * 5,   // 1 tsp ≈ 5g
            'piece' => $quantity * 100, // Estimation : 1 pièce ≈ 100g
            default => $quantity
        };
    }

    /**
     * État pour créer une entrée d'aujourd'hui
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'entry_date' => Carbon::today(),
            'entry_time' => $this->faker->time('H:i:s'),
        ]);
    }

    /**
     * État pour créer une entrée de petit-déjeuner
     */
    public function breakfast(): static
    {
        $breakfastFoods = ['Oats', 'Eggs', 'Bread', 'Milk', 'Banana', 'Greek Yogurt'];
        
        return $this->state(fn (array $attributes) => [
            'meal_type' => 'breakfast',
            'food_name' => $this->faker->randomElement($breakfastFoods),
            'entry_time' => $this->faker->time('07:00:00', '09:30:00'),
        ]);
    }

    /**
     * État pour créer une entrée de déjeuner
     */
    public function lunch(): static
    {
        $lunchFoods = ['Chicken Breast', 'Salmon', 'Brown Rice', 'Pasta', 'Broccoli', 'Salad'];
        
        return $this->state(fn (array $attributes) => [
            'meal_type' => 'lunch',
            'food_name' => $this->faker->randomElement($lunchFoods),
            'entry_time' => $this->faker->time('11:30:00', '14:00:00'),
        ]);
    }

    /**
     * État pour créer une entrée de dîner
     */
    public function dinner(): static
    {
        $dinnerFoods = ['Salmon', 'Chicken Breast', 'Tuna', 'Pasta', 'Brown Rice', 'Vegetables'];
        
        return $this->state(fn (array $attributes) => [
            'meal_type' => 'dinner',
            'food_name' => $this->faker->randomElement($dinnerFoods),
            'entry_time' => $this->faker->time('18:00:00', '21:00:00'),
        ]);
    }

    /**
     * État pour créer une collation
     */
    public function snack(): static
    {
        $snackFoods = ['Apple', 'Banana', 'Almonds', 'Walnuts', 'Greek Yogurt', 'Strawberry'];
        
        return $this->state(fn (array $attributes) => [
            'meal_type' => 'snack',
            'food_name' => $this->faker->randomElement($snackFoods),
            'entry_time' => $this->faker->time('10:00:00', '16:00:00'),
        ]);
    }

    /**
     * État pour créer une entrée avec des données API réalistes
     */
    public function fromApi(): static
    {
        return $this->state(fn (array $attributes) => [
            'api_source' => 'calorieninjas',
            'api_response' => [
                'success' => true,
                'source' => 'calorieninjas',
                'query_sent' => $attributes['quantity'] . $attributes['unit'] . ' ' . $attributes['food_name'],
                'response_time' => $this->faker->randomFloat(2, 0.5, 2.0),
            ],
        ]);
    }
}
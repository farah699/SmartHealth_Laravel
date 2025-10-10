<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\FoodEntry;
use App\Models\HydrationEntry;
use App\Models\DailySummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\NutritionApiService;
use Illuminate\Support\Facades\Log;

class NutritionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard principal nutrition - VERSION CORRIGÉE
     */
 public function dashboard()
{
    $user = Auth::user();
    
    // S'assurer que l'utilisateur a un profil
    $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
    
    // ✅ NOUVEAU : Archiver les données des jours précédents
    $this->archivePreviousDays($user);
    
    // Récupérer les entrées d'aujourd'hui SEULEMENT
    $today = Carbon::today();
    
    $todayFoodEntries = FoodEntry::where('user_id', $user->id)
        ->whereDate('entry_date', $today)
        ->orderBy('entry_time', 'desc')
        ->get();
        
    $todayHydrationEntries = HydrationEntry::where('user_id', $user->id)
        ->whereDate('entry_date', $today)
        ->orderBy('entry_time', 'desc')
        ->get();
    
    // ✅ UTILISER la méthode du modèle pour calculer/sauvegarder le résumé d'aujourd'hui
    $todaySummary = DailySummary::updateForUser($user->id, $today);
    
    // Créer l'objet summary pour AUJOURD'HUI
    $summary = (object) [
        'date' => $today,
        'total_calories' => $todaySummary->total_calories,
        'total_protein' => $todaySummary->total_proteins,
        'total_carbs' => $todaySummary->total_carbs,
        'total_fats' => $todaySummary->total_fats,
        'total_fiber' => $todaySummary->total_fiber,
        'total_water_ml' => $todaySummary->total_water_ml,
        'calorie_goal' => $todaySummary->calorie_goal,
        'water_goal_ml' => $todaySummary->water_goal_ml,
        'calorie_percentage' => $todaySummary->calorie_percentage,
        'water_percentage' => $todaySummary->water_percentage,
        'meals_count' => $todayFoodEntries->count(),
        'hydration_entries_count' => $todayHydrationEntries->count(),
        'is_today' => true
    ];
    
    // ✅ NOUVEAU : Récupérer les 7 derniers jours d'historique
    $recentHistory = DailySummary::where('user_id', $user->id)
        ->where('summary_date', '<', $today)
        ->orderBy('summary_date', 'desc')
        ->limit(7)
        ->get();
    
    return view('nutrition.dashboard', compact(
        'user', 'profile', 'summary', 'todayFoodEntries', 'todayHydrationEntries', 'recentHistory'
    ));
}
    /**
     * Afficher/éditer le profil utilisateur
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        
        return view('nutrition.profile', compact('user', 'profile'));
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'age' => 'required|integer|min:10|max:120',
            'gender' => 'required|in:male,female',
            'height' => 'required|numeric|min:100|max:250',
            'weight' => 'required|numeric|min:30|max:300',
            'activity_level' => 'required|in:sedentary,light,moderate,active,very_active',
            'goal' => 'required|in:lose,maintain,gain'
        ]);

        $user = Auth::user();
        
        // Mettre à jour l'âge dans la table users
        $user->update(['age' => $request->age]);
        
        // Mettre à jour ou créer le profil
        $profile = UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'gender' => $request->gender,
                'height' => $request->height,
                'weight' => $request->weight,
                'activity_level' => $request->activity_level,
                'goal' => $request->goal,
            ]
        );
        
        // Recalculer automatiquement les objectifs
        $profile->updateCalculations();
        
        return redirect()->route('nutrition.profile')
            ->with('success', 'Profil mis à jour avec succès ! Vos objectifs ont été recalculés.');
    }

    /**
     * Ajouter une entrée d'hydratation
     */
    public function storeHydration(Request $request)
    {
        $request->validate([
            'drink_type' => 'required|in:water,tea,coffee,herbal_tea,sparkling_water,other',
            'amount_ml' => 'required|numeric|min:50|max:2000',
            'entry_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        
        // Créer l'entrée d'hydratation
        HydrationEntry::create([
            'user_id' => $user->id,
            'drink_type' => $request->drink_type,
            'amount_ml' => $request->amount_ml,
            'entry_date' => Carbon::today(),
            'entry_time' => $request->entry_time ? Carbon::createFromFormat('H:i', $request->entry_time) : Carbon::now(),
            'notes' => $request->notes
        ]);

        return redirect()->route('nutrition.dashboard')
            ->with('success', '💧 Hydratation ajoutée avec succès ! Continuez comme ça !');
    }

    /**
     * Ajout rapide d'eau (pour les boutons prédéfinis)
     */
    public function quickAddWater(Request $request)
    {
        $request->validate([
            'amount' => 'required|in:250,500,750,1000'
        ]);

        $user = Auth::user();
        
        HydrationEntry::create([
            'user_id' => $user->id,
            'drink_type' => 'water',
            'amount_ml' => $request->amount,
            'entry_date' => Carbon::today(),
            'entry_time' => Carbon::now(),
            'notes' => 'Ajout rapide'
        ]);

        // Recalculer le nouveau total
        $newTotal = HydrationEntry::where('user_id', $user->id)
            ->whereDate('entry_date', Carbon::today())
            ->sum('amount_ml');

        return response()->json([
            'success' => true,
            'message' => "💧 {$request->amount} ml d'eau ajoutés !",
            'newTotal' => $newTotal
        ]);
    }

    /**
     * Supprimer une entrée d'hydratation
     */
    public function deleteHydration($id)
    {
        $user = Auth::user();
        $entry = HydrationEntry::where('user_id', $user->id)->findOrFail($id);
        
        $entry->delete();
        
        return redirect()->route('nutrition.dashboard')
            ->with('success', 'Entrée d\'hydratation supprimée avec succès.');
    }

    /**
     * API pour obtenir les stats d'hydratation en temps réel
     */
    public function getHydrationStats()
    {
        $user = Auth::user();
        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);
        
        $todayTotal = HydrationEntry::where('user_id', $user->id)
            ->whereDate('entry_date', Carbon::today())
            ->sum('amount_ml');
            
        $goal = $profile->daily_water_ml ?? 2000;
        $percentage = $goal > 0 ? round(($todayTotal / $goal) * 100, 1) : 0;
        
        return response()->json([
            'total' => $todayTotal,
            'goal' => $goal,
            'percentage' => $percentage,
            'remaining' => max(0, $goal - $todayTotal)
        ]);
    }

    public function searchFood(Request $request)
{
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }

    $nutritionService = new NutritionApiService();
    $suggestions = $nutritionService->searchFood($query, 10);
    
    return response()->json($suggestions);
}


public function calculateNutrition(Request $request)
{
    try {
        Log::info('=== DÉBUT calculateNutrition ===', [
            'request_data' => $request->all()
        ]);

        $request->validate([
            'food_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.1',
            'unit' => 'required|string'
        ]);

        $nutritionService = new NutritionApiService();
        
        Log::info('Service créé, appel getNutritionInfo...', [
            'food_name' => $request->food_name,
            'quantity' => $request->quantity,
            'unit' => $request->unit
        ]);

        $nutritionData = $nutritionService->getNutritionInfo(
            $request->food_name,
            (float) $request->quantity,
            $request->unit
        );

        Log::info('Données reçues du service:', $nutritionData);

        // ✅ NOUVEAU : NE PAS SAUVEGARDER ICI - Juste retourner les données
        // La sauvegarde se fera dans storeFood()

        return response()->json([
            'success' => true,
            'data' => $nutritionData,
            'nutrition' => $nutritionData,
            'is_api_data' => $nutritionData['success'] ?? false,
            'source' => $nutritionData['api_source'] ?? 'unknown',
            'message' => "🍎 Données nutritionnelles calculées pour {$nutritionData['food_name']}",
            // ✅ Retourner les données formatées pour storeFood
            'formatted_for_store' => [
                'calories_per_100g' => $nutritionData['per_100g']['calories'] ?? 0,
                'protein_per_100g' => $nutritionData['per_100g']['protein'] ?? 0,
                'carbs_per_100g' => $nutritionData['per_100g']['carbs'] ?? 0,
                'fat_per_100g' => $nutritionData['per_100g']['fat'] ?? 0,
                'fiber_per_100g' => $nutritionData['per_100g']['fiber'] ?? 0,
                'sugar_per_100g' => $nutritionData['per_100g']['sugar'] ?? 0,
                'sodium_per_100g' => $nutritionData['per_100g']['sodium'] ?? 0,
            ]
        ], 200, [
            'Content-Type' => 'application/json; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);

    } catch (\Exception $e) {
        Log::error('Exception dans calculateNutrition:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du calcul nutritionnel: ' . $e->getMessage(),
            'error_details' => [
                'file' => basename($e->getFile()),
                'line' => $e->getLine()
            ]
        ], 500, ['Content-Type' => 'application/json']);
    }
}

/**
 * Obtenir les informations nutritionnelles d'un aliment
 */
public function getNutrition(Request $request)
{
    $request->validate([
        'food_name' => 'required|string|max:255',
        'quantity' => 'required|numeric|min:0.1',
        'unit' => 'required|string|in:g,ml,piece,cup,slice,tbsp,tsp,portion'
    ]);

    try {
        $nutritionService = new NutritionApiService();
        
        $nutrition = $nutritionService->getNutritionInfo(
            $request->food_name,
            $request->quantity,
            $request->unit
        );

        return response()->json([
            'success' => true,
            'nutrition' => $nutrition
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur calcul nutrition: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du calcul nutritionnel'
        ], 500);
    }
}

/**
 * Sauvegarder une entrée alimentaire
 */
public function storeFood(Request $request)
{
    try {
        // ✅ CORRIGÉ : Validation plus robuste
        $request->validate([
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'food_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.1',
            'unit' => 'required|string|in:g,ml,piece,cup,slice,tbsp,tsp,portion',
            'calories_per_100g' => 'required|numeric|min:0',
            'protein_per_100g' => 'required|numeric|min:0',
            'carbs_per_100g' => 'required|numeric|min:0',
            'fat_per_100g' => 'required|numeric|min:0',
            'fiber_per_100g' => 'nullable|numeric|min:0',
            'sugar_per_100g' => 'nullable|numeric|min:0',
            'sodium_per_100g' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        
        // ✅ CORRIGÉ : Calculer les valeurs totales directement
        $factor = $this->getConversionFactor($request->unit, $request->quantity);
        
        $totalCalories = ($request->calories_per_100g * $factor);
        $totalProtein = ($request->protein_per_100g * $factor);
        $totalCarbs = ($request->carbs_per_100g * $factor);
        $totalFat = ($request->fat_per_100g * $factor);
        $totalFiber = ($request->fiber_per_100g ?? 0) * $factor;
        $totalSugar = ($request->sugar_per_100g ?? 0) * $factor;
        $totalSodium = ($request->sodium_per_100g ?? 0) * $factor;

        // ✅ CORRIGÉ : Créer l'entrée avec gestion d'erreur
        $foodEntry = FoodEntry::create([
            'user_id' => $user->id,
            'meal_type' => $request->meal_type,
            'food_name' => $request->food_name,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            
            // ✅ UTILISER LES BONNES COLONNES DE LA BDD
            'calories_per_100g' => round($request->calories_per_100g, 2),
            'total_calories' => round($totalCalories, 1),
            
            // ✅ IMPORTANTES : Utiliser les colonnes correctes 
            'proteins' => round($request->protein_per_100g, 2),  // ✅ CORRIGÉ
            'carbs' => round($request->carbs_per_100g, 2),       // ✅ CORRIGÉ
            'fats' => round($request->fat_per_100g, 2),          // ✅ CORRIGÉ
            'fiber' => round($request->fiber_per_100g ?? 0, 2),  // ✅ CORRIGÉ
            
            'entry_date' => Carbon::today(),
            'entry_time' => Carbon::now(),
            'api_source' => $request->api_source ?? 'manual'
        ]);

        // ✅ CORRIGÉ : S'assurer que l'entrée a été créée
        if (!$foodEntry) {
            throw new \Exception('Impossible de créer l\'entrée alimentaire');
        }

        // Calculer le nouveau total des calories du jour
        $todayTotal = FoodEntry::where('user_id', $user->id)
            ->whereDate('entry_date', Carbon::today())
            ->sum('total_calories');

        // ✅ CORRIGÉ : Réponse JSON claire et complète
        $response = [
            'success' => true,
            'message' => "🍎 {$request->food_name} ajouté avec succès ! (" . 
                        number_format($totalCalories, 0) . " kcal)",
            'food_entry' => [
                'id' => $foodEntry->id,
                'food_name' => $foodEntry->food_name,
                'meal_type' => $foodEntry->meal_type,
                'quantity' => $foodEntry->quantity,
                'unit' => $foodEntry->unit,
                'total_calories' => $foodEntry->total_calories
            ],
            'today_total' => round($todayTotal, 1)
        ];

        // ✅ CORRIGÉ : Forcer le bon content-type
        return response()->json($response, 200, [
            'Content-Type' => 'application/json'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed in storeFood: ', $e->errors());
        
        return response()->json([
            'success' => false,
            'message' => 'Données invalides',
            'errors' => $e->errors()
        ], 422, ['Content-Type' => 'application/json']);
        
    } catch (\Exception $e) {
        Log::error('Erreur dans storeFood: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'ajout de l\'aliment: ' . $e->getMessage(),
            'error_details' => $e->getMessage()
        ], 500, ['Content-Type' => 'application/json']);
    }
}

/**
 * Supprimer une entrée alimentaire
 */
public function deleteFood($id)
{
    $user = Auth::user();
    $entry = FoodEntry::where('user_id', $user->id)->findOrFail($id);
    
    $foodName = $entry->food_name;
    $entry->delete();
    
    return redirect()->route('nutrition.dashboard')
        ->with('success', "Aliment '{$foodName}' supprimé avec succès.");
}

/**
 * Facteur de conversion pour les unités (méthode utilitaire)
 */
private function getConversionFactor($unit, $quantity)
{
    switch ($unit) {
        case 'g':
        case 'ml':
            return $quantity / 100;
        case 'piece':
            return $quantity * 0.5; // Estimation moyenne
        case 'cup':
            return $quantity * 2.4;
        case 'tbsp':
            return $quantity * 0.15;
        case 'tsp':
            return $quantity * 0.05;
        case 'slice':
            return $quantity * 0.3;
        case 'portion':
            return $quantity * 1.5;
        default:
            return $quantity / 100;
    }
}




/**
 * ✅ MÉTHODE SIMPLIFIÉE : Archiver les données des jours précédents
 */
private function archivePreviousDays($user)
{
    $yesterday = Carbon::yesterday();
    
    // Vérifier s'il y a des données non archivées d'hier ou avant
    $unarchived = FoodEntry::where('user_id', $user->id)
        ->where('entry_date', '<=', $yesterday)
        ->whereNotExists(function ($query) {
            $query->select('id')
                  ->from('daily_summaries')
                  ->whereRaw('daily_summaries.user_id = food_entries.user_id')
                  ->whereRaw('daily_summaries.summary_date = DATE(food_entries.entry_date)');
        })
        ->selectRaw('DATE(entry_date) as date')
        ->distinct()
        ->get();
        
    foreach ($unarchived as $entry) {
        // ✅ UTILISER la méthode du modèle
        DailySummary::updateForUser($user->id, Carbon::parse($entry->date));
        
        Log::info("Résumé quotidien créé pour {$user->name} - {$entry->date}");
    }
}

/**
 * ✅ MÉTHODE : Voir l'historique complet
 */
public function history()
{
    $user = Auth::user();
    
    // Archiver les données avant d'afficher l'historique
    $this->archivePreviousDays($user);
    
    $history = DailySummary::where('user_id', $user->id)
        ->orderBy('summary_date', 'desc')
        ->paginate(30);
    
    // Statistiques globales
    $totalDays = $history->total();
    $goalsAchieved = DailySummary::where('user_id', $user->id)
        ->where('calorie_percentage', '>=', 80)
        ->where('calorie_percentage', '<=', 120)
        ->where('water_percentage', '>=', 100)
        ->count();
    
    $stats = [
        'total_days' => $totalDays,
        'goals_achieved' => $goalsAchieved,
        'success_rate' => $totalDays > 0 ? round(($goalsAchieved / $totalDays) * 100, 1) : 0,
        'avg_calories' => round(DailySummary::where('user_id', $user->id)->avg('total_calories'), 0),
        'avg_water' => round(DailySummary::where('user_id', $user->id)->avg('total_water_ml'), 0),
        'best_day' => DailySummary::where('user_id', $user->id)->orderBy('calorie_percentage', 'desc')->first(),
        'streak' => $this->calculateStreak($user)
    ];
    
    return view('nutrition.history', compact('history', 'stats'));
}

/**
 * ✅ MÉTHODE : Calculer la série de réussite
 */
private function calculateStreak($user)
{
    $streak = 0;
    $date = Carbon::yesterday(); // Commencer par hier car aujourd'hui n'est pas encore archivé
    
    while (true) {
        $summary = DailySummary::where('user_id', $user->id)
            ->where('summary_date', $date)
            ->first();
            
        if (!$summary || 
            $summary->calorie_percentage < 80 || 
            $summary->calorie_percentage > 120 || 
            $summary->water_percentage < 100) {
            break;
        }
        
        $streak++;
        $date = $date->subDay();
        
        // Limiter à 365 jours pour éviter les boucles infinies
        if ($streak >= 365) break;
    }
    
    return $streak;
}
}
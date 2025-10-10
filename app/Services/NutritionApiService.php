<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NutritionApiService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.calorieninjas.api_key');
        $this->baseUrl = 'https://api.calorieninjas.com/v1/nutrition';
        
        Log::info('NutritionApiService initialized', [
            'api_key_present' => !empty($this->apiKey),
            'api_key_length' => strlen($this->apiKey ?? ''),
            'api_key_start' => substr($this->apiKey ?? '', 0, 10) . '...'
        ]);
    }

    /**
     * Obtenir les informations nutritionnelles d'un aliment - VERSION CORRIGÉE
     */
public function getNutritionInfo($foodQuery, $quantity = 100, $unit = 'g')
{
    // ✅ FORCER ta clé API qui marche avec PowerShell
    $workingApiKey = 'AEZMhsZZNEP9LznTglbz2Q==h6DSZH2PMaHZiOD9';
    
    Log::info('🔥🔥🔥 DEBUT METHODE REMPLACÉE COMPLÈTEMENT 🔥🔥🔥', [
        'food_query' => $foodQuery,
        'quantity' => $quantity,
        'unit' => $unit,
        'api_key_working' => substr($workingApiKey, 0, 10) . '...'
    ]);

    try {
        // ✅ SIMPLE : traduction directe
        $cleanName = $foodQuery;
        if (strtolower($foodQuery) === 'pomme') $cleanName = 'apple';
        if (strtolower($foodQuery) === 'banane') $cleanName = 'banana';
        
        // ✅ IMPORTANT : Calculer les grammes réels
        $actualGrams = $quantity;
        if ($unit === 'piece') $actualGrams = $quantity * 100;
        if ($unit === 'slice') $actualGrams = $quantity * 30;
        if ($unit === 'cup') $actualGrams = $quantity * 240;
        if ($unit === 'tbsp') $actualGrams = $quantity * 15;
        if ($unit === 'tsp') $actualGrams = $quantity * 5;
        if ($unit === 'portion') $actualGrams = $quantity * 150;
        
        $queryString = "{$actualGrams}g {$cleanName}";
        
        Log::info('🔥🔥🔥 APPEL API DIRECT 🔥🔥🔥', [
            'query' => $queryString,
            'actual_grams' => $actualGrams,
            'url' => 'https://api.calorieninjas.com/v1/nutrition'
        ]);

        $response = Http::withHeaders([
            'X-Api-Key' => $workingApiKey,
            'Accept' => 'application/json'
        ])
        ->withOptions([
            'verify' => false, // ✅ DÉSACTIVER SSL pour dev
            'timeout' => 20
        ])
        ->get('https://api.calorieninjas.com/v1/nutrition', [
            'query' => $queryString
        ]);

        Log::info('🔥🔥🔥 RÉPONSE REÇUE 🔥🔥🔥', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $response->body()
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            Log::info('🔥🔥🔥 JSON PARSÉ 🔥🔥🔥', $data);
            
            if (!empty($data['items'])) {
                $item = $data['items'][0];
                
                // ✅ CORRIGÉ : Calculer les valeurs pour 100g
                $per100gFactor = 100 / $actualGrams; // Facteur pour ramener à 100g
                
                // Valeurs totales pour la quantité demandée
                $totalCalories = round($item['calories'] ?? 0, 1);
                $totalProtein = round($item['protein_g'] ?? 0, 1);
                $totalCarbs = round($item['carbohydrates_total_g'] ?? 0, 1);
                $totalFat = round($item['fat_total_g'] ?? 0, 1);
                $totalFiber = round($item['fiber_g'] ?? 0, 1);
                $totalSugar = round($item['sugar_g'] ?? 0, 1);
                $totalSodium = round($item['sodium_mg'] ?? 0, 1);
                
                // ✅ IMPORTANT : Valeurs pour 100g (ce que le contrôleur attend)
                $per100gCalories = round($totalCalories * $per100gFactor, 1);
                $per100gProtein = round($totalProtein * $per100gFactor, 1);
                $per100gCarbs = round($totalCarbs * $per100gFactor, 1);
                $per100gFat = round($totalFat * $per100gFactor, 1);
                $per100gFiber = round($totalFiber * $per100gFactor, 1);
                $per100gSugar = round($totalSugar * $per100gFactor, 1);
                $per100gSodium = round($totalSodium * $per100gFactor, 1);
                
                $result = [
                    'food_name' => $item['name'] ?? $foodQuery,
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'success' => true, // ✅ IMPORTANT
                    
                    // Valeurs totales pour la quantité demandée
                    'calories' => $totalCalories,
                    'protein_g' => $totalProtein,
                    'carbs_g' => $totalCarbs,
                    'fat_g' => $totalFat,
                    'fiber_g' => $totalFiber,
                    'sugar_g' => $totalSugar,
                    'sodium_mg' => $totalSodium,
                    
                    // ✅ CORRIGÉ : Vraies valeurs pour 100g
                    'per_100g' => [
                        'calories' => $per100gCalories,
                        'protein' => $per100gProtein,
                        'carbs' => $per100gCarbs,
                        'fat' => $per100gFat,
                        'fiber' => $per100gFiber,
                        'sugar' => $per100gSugar,
                        'sodium' => $per100gSodium,
                    ],
                    
                    'api_source' => 'calorieninjas',
                    'debug_info' => [
                        'actual_grams' => $actualGrams,
                        'per_100g_factor' => $per100gFactor,
                        'query_sent' => $queryString
                    ]
                ];
                
                Log::info('🎉🎉🎉 API SUCCESS TOTAL !!! 🎉🎉🎉', [
                    'result' => $result,
                    'per_100g_calories' => $per100gCalories,
                    'per_100g_protein' => $per100gProtein,
                    'per_100g_carbs' => $per100gCarbs,
                    'per_100g_fat' => $per100gFat
                ]);
                return $result;
            } else {
                Log::warning('⚠️ API OK mais pas d\'items');
            }
        } else {
            Log::error('❌ ERREUR HTTP', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }

    } catch (\Exception $e) {
        Log::error('💥💥💥 EXCEPTION CAPTURÉE 💥💥💥', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

    Log::info('🔄🔄🔄 FALLBACK UTILISÉ 🔄🔄🔄');
    return $this->getFallbackNutritionData($foodQuery, $quantity, $unit);
}
    /**
     * Rechercher des aliments (suggestions améliorées)
     */
    public function searchFood($query, $limit = 10)
    {
        return $this->getEnhancedSuggestions($query, $limit);
    }

    /**
     * ✅ CORRIGÉ : Construire une requête optimisée pour l'API CalorieNinjas
     */
    private function buildOptimizedQuery($foodName, $quantity, $unit)
    {
        // Nettoyer le nom de l'aliment
        $cleanFoodName = $this->cleanFoodName($foodName);
        
        // L'API CalorieNinjas fonctionne mieux avec des requêtes en grammes
        switch ($unit) {
            case 'g':
                return "{$quantity}g {$cleanFoodName}";
                
            case 'piece':
                $grams = $this->convertPiecesToGrams($cleanFoodName, $quantity);
                return "{$grams}g {$cleanFoodName}";
                
            case 'cup':
                $grams = $quantity * 240;
                return "{$grams}g {$cleanFoodName}";
                
            case 'slice':
                $grams = $quantity * 30;
                return "{$grams}g {$cleanFoodName}";
                
            case 'tbsp':
                $grams = $quantity * 15;
                return "{$grams}g {$cleanFoodName}";
                
            case 'tsp':
                $grams = $quantity * 5;
                return "{$grams}g {$cleanFoodName}";
                
            case 'ml':
                return "{$quantity}ml {$cleanFoodName}";
                
            case 'portion':
                $grams = $quantity * 150;
                return "{$grams}g {$cleanFoodName}";
                
            default:
                return "{$quantity}g {$cleanFoodName}";
        }
    }

    /**
     * ✅ Nettoyer le nom de l'aliment pour l'API
     */
    private function cleanFoodName($foodName)
    {
        $translations = [
            'pomme' => 'apple',
            'banane' => 'banana',
            'orange' => 'orange',
            'carotte' => 'carrot',
            'tomate' => 'tomato',
            'poulet' => 'chicken',
            'bœuf' => 'beef',
            'boeuf' => 'beef',
            'saumon' => 'salmon',
            'riz' => 'rice',
            'pain' => 'bread',
            'lait' => 'milk',
            'œuf' => 'egg',
            'oeuf' => 'egg',
            'fromage' => 'cheese',
            'yaourt' => 'yogurt',
            'pâtes' => 'pasta',
            'pates' => 'pasta',
            'brocoli' => 'broccoli',
            'pizza' => 'pizza',
            'chocolat' => 'chocolate',
            'salade' => 'salad'
        ];

        $lowerName = strtolower(trim($foodName));
        
        if (isset($translations[$lowerName])) {
            return $translations[$lowerName];
        }
        
        foreach ($translations as $french => $english) {
            if (strpos($lowerName, $french) !== false) {
                return str_replace($french, $english, $lowerName);
            }
        }
        
        return $foodName;
    }

    /**
     * ✅ Convertir les pièces en grammes selon le type d'aliment
     */
    private function convertPiecesToGrams($foodName, $quantity)
    {
        $weights = [
            'apple' => 150,
            'banana' => 120,
            'orange' => 180,
            'egg' => 50,
            'carrot' => 80,
            'tomato' => 100,
            'slice' => 30,
        ];

        $lowerName = strtolower($foodName);
        
        foreach ($weights as $food => $weight) {
            if (strpos($lowerName, $food) !== false) {
                return $quantity * $weight;
            }
        }
        
        return $quantity * 100; // Poids par défaut
    }

    /**
     * ✅ Formater les données de l'API
     */
    private function formatApiNutritionData($apiData, $foodName, $quantity, $unit)
    {
        if (empty($apiData['items'])) {
            return $this->getFallbackNutritionData($foodName, $quantity, $unit);
        }

        $item = $apiData['items'][0];
        $actualGramsInApi = $this->getQuantityInGrams($quantity, $unit);
        $per100gFactor = 100 / $actualGramsInApi;

        $totalCalories = $item['calories'] ?? 0;
        $totalProtein = $item['protein_g'] ?? 0;
        $totalCarbs = $item['carbohydrates_total_g'] ?? 0;
        $totalFat = $item['fat_total_g'] ?? 0;
        $totalFiber = $item['fiber_g'] ?? 0;
        $totalSugar = $item['sugar_g'] ?? 0;
        $totalSodium = $item['sodium_mg'] ?? 0;

        return [
            'food_name' => $item['name'] ?? $foodName,
            'quantity' => $quantity,
            'unit' => $unit,
            'success' => true,
            
            'calories' => round($totalCalories, 1),
            'protein_g' => round($totalProtein, 1),
            'carbs_g' => round($totalCarbs, 1),
            'fat_g' => round($totalFat, 1),
            'fiber_g' => round($totalFiber, 1),
            'sugar_g' => round($totalSugar, 1),
            'sodium_mg' => round($totalSodium, 1),
            
            'per_100g' => [
                'calories' => round($totalCalories * $per100gFactor, 1),
                'protein' => round($totalProtein * $per100gFactor, 1),
                'carbs' => round($totalCarbs * $per100gFactor, 1),
                'fat' => round($totalFat * $per100gFactor, 1),
                'fiber' => round($totalFiber * $per100gFactor, 1),
                'sugar' => round($totalSugar * $per100gFactor, 1),
                'sodium' => round($totalSodium * $per100gFactor, 1),
            ],
            
            'api_source' => 'calorieninjas',
            'raw_data' => $item
        ];
    }

    /**
     * ✅ Convertir en grammes pour les calculs
     */
    private function getQuantityInGrams($quantity, $unit)
    {
        switch ($unit) {
            case 'g': return $quantity;
            case 'piece': return $quantity * 100;
            case 'cup': return $quantity * 240;
            case 'slice': return $quantity * 30;
            case 'tbsp': return $quantity * 15;
            case 'tsp': return $quantity * 5;
            case 'ml': return $quantity;
            case 'portion': return $quantity * 150;
            default: return $quantity;
        }
    }

    /**
     * ✅ Suggestions d'aliments étendues
     */
    private function getEnhancedSuggestions($query, $limit)
    {
        $foods = [
            'pomme' => ['name' => 'Pomme', 'category' => '🍎 Fruits', 'unit_suggestions' => ['piece', 'g']],
            'banane' => ['name' => 'Banane', 'category' => '🍌 Fruits', 'unit_suggestions' => ['piece', 'g']],
            'riz' => ['name' => 'Riz blanc', 'category' => '🍚 Céréales', 'unit_suggestions' => ['g', 'cup']],
            'poulet' => ['name' => 'Blanc de poulet', 'category' => '🐔 Viandes', 'unit_suggestions' => ['g', 'piece']],
            'pain' => ['name' => 'Pain blanc', 'category' => '🍞 Céréales', 'unit_suggestions' => ['slice', 'g']],
            'lait' => ['name' => 'Lait', 'category' => '🥛 Laitiers', 'unit_suggestions' => ['ml', 'cup']],
            'œuf' => ['name' => 'Œuf', 'category' => '🥚 Protéines', 'unit_suggestions' => ['piece', 'g']],
            'tomate' => ['name' => 'Tomate', 'category' => '🍅 Légumes', 'unit_suggestions' => ['piece', 'g']],
            'fromage' => ['name' => 'Fromage', 'category' => '🧀 Laitiers', 'unit_suggestions' => ['g', 'slice']],
            'yaourt' => ['name' => 'Yaourt', 'category' => '🥛 Laitiers', 'unit_suggestions' => ['g', 'cup']],
            'saumon' => ['name' => 'Saumon', 'category' => '🐟 Poissons', 'unit_suggestions' => ['g', 'piece']],
            'pizza' => ['name' => 'Pizza', 'category' => '🍕 Plats', 'unit_suggestions' => ['slice', 'g']],
            'salade' => ['name' => 'Salade verte', 'category' => '🥗 Plats', 'unit_suggestions' => ['g', 'cup']],
        ];

        $results = [];
        $query = strtolower(trim($query));

        foreach ($foods as $key => $food) {
            if (stripos($key, $query) !== false || stripos($food['name'], $query) !== false) {
                $results[] = [
                    'id' => $key,
                    'name' => $food['name'],
                    'category' => $food['category'],
                    'unit_suggestions' => $food['unit_suggestions'],
                    'is_suggestion' => true
                ];
            }
        }

        if (strlen($query) >= 2) {
            array_unshift($results, [
                'id' => 'custom_' . time(),
                'name' => ucfirst($query),
                'category' => '🔍 Recherche libre - "' . $query . '"',
                'unit_suggestions' => ['g', 'piece', 'cup', 'ml'],
                'is_suggestion' => false
            ]);
        }

        return array_slice($results, 0, $limit);
    }

    /**
     * ✅ Données de fallback plus intelligentes
     */
   private function getFallbackNutritionData($foodName, $quantity, $unit)
{
    $baseCalories = $this->getSmartBaseCalories($foodName);
    $macros = $this->getSmartMacros($foodName);
    
    // ✅ CORRIGÉ : Calculer le facteur correctement
    $actualGrams = $this->getQuantityInGrams($quantity, $unit);
    $factor = $actualGrams / 100; // Facteur pour la quantité demandée
    
    // Valeurs totales pour la quantité demandée
    $totalCalories = round($baseCalories * $factor, 1);
    $totalProtein = round($baseCalories * $macros['protein'] * $factor, 1);
    $totalCarbs = round($baseCalories * $macros['carbs'] * $factor, 1);
    $totalFat = round($baseCalories * $macros['fat'] * $factor, 1);
    $totalFiber = round($factor * 3, 1);
    $totalSugar = round($totalCarbs * 0.4, 1);
    $totalSodium = round($factor * 150, 1);
    
    return [
        'food_name' => $foodName,
        'quantity' => $quantity,
        'unit' => $unit,
        'success' => false,
        
        // Valeurs totales pour la quantité demandée
        'calories' => $totalCalories,
        'protein_g' => $totalProtein,
        'carbs_g' => $totalCarbs,
        'fat_g' => $totalFat,
        'fiber_g' => $totalFiber,
        'sugar_g' => $totalSugar,
        'sodium_mg' => $totalSodium,
        
        // ✅ CORRIGÉ : Vraies valeurs pour 100g (pas facteur appliqué)
        'per_100g' => [
            'calories' => $baseCalories,
            'protein' => round($baseCalories * $macros['protein'], 1),
            'carbs' => round($baseCalories * $macros['carbs'], 1),
            'fat' => round($baseCalories * $macros['fat'], 1),
            'fiber' => 3.0,
            'sugar' => round($baseCalories * $macros['carbs'] * 0.4, 1),
            'sodium' => 150.0,
        ],
        
        'api_source' => 'fallback',
        'is_estimated' => true
    ];
}

    private function getSmartBaseCalories($foodName)
    {
        $calories = [
            'pomme' => 52, 'apple' => 52,
            'banane' => 89, 'banana' => 89,
            'riz' => 130, 'rice' => 130,
            'poulet' => 165, 'chicken' => 165,
            'pain' => 265, 'bread' => 265,
            'lait' => 42, 'milk' => 42,
            'œuf' => 155, 'egg' => 155, 'oeuf' => 155,
            'tomate' => 18, 'tomato' => 18,
            'fromage' => 300, 'cheese' => 300,
            'yaourt' => 60, 'yogurt' => 60,
            'saumon' => 180, 'salmon' => 180,
            'pizza' => 266,
            'salade' => 20, 'salad' => 20,
        ];
        
        $foodLower = strtolower($foodName);
        
        if (isset($calories[$foodLower])) return $calories[$foodLower];
        
        foreach ($calories as $key => $value) {
            if (strpos($foodLower, $key) !== false) return $value;
        }
        
        return 100; // Valeur par défaut
    }

    private function getSmartMacros($foodName)
    {
        $foodLower = strtolower($foodName);
        
        if (preg_match('/poulet|chicken|viande|meat/', $foodLower)) {
            return ['protein' => 0.06, 'carbs' => 0.01, 'fat' => 0.03];
        }
        
        if (preg_match('/pomme|apple|fruit/', $foodLower)) {
            return ['protein' => 0.005, 'carbs' => 0.08, 'fat' => 0.002];
        }
        
        return ['protein' => 0.03, 'carbs' => 0.06, 'fat' => 0.02]; // Défaut
    }

    private function getConversionFactor($unit, $quantity)
    {
        switch ($unit) {
            case 'g':
            case 'ml':
                return $quantity / 100;
            case 'piece':
                return $quantity * 1.0;
            case 'cup':
                return $quantity * 2.4;
            case 'slice':
                return $quantity * 0.3;
            case 'tbsp':
                return $quantity * 0.15;
            case 'tsp':
                return $quantity * 0.05;
            case 'portion':
                return $quantity * 1.5;
            default:
                return $quantity / 100;
        }
    }
}
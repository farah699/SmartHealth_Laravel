<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\FoodEntry;
use Carbon\Carbon;

class AiFoodController extends Controller
{
    // ✅ CHANGEMENT 1: Utiliser l'URL Docker au lieu de localhost
    private $aiServiceUrl = 'http://food-ai:5100';  // Au lieu de http://127.0.0.1:5100
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function analyzeFood(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|string'
            ]);
            
            $user = Auth::user();
            
            // Vérifier que le service IA est disponible
            // ✅ CHANGEMENT 2: Augmenter le timeout pour Docker
            $healthCheck = Http::timeout(10)->get($this->aiServiceUrl . '/health');
            
            if (!$healthCheck->successful()) {
                throw new \Exception('🤖 AI service unavailable - Please make sure the food-ai container is running');
            }
            
            Log::info('AI Health Check OK', ['ai_status' => $healthCheck->json()]);
            
            // Appeler le service IA avancé
            // ✅ CHANGEMENT 3: Timeout plus long pour le traitement IA
            $response = Http::timeout(30)->post($this->aiServiceUrl . '/detect_food', [
                'image' => $request->image,
                'user_id' => $user->id
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('AI Detection Result', [
                    'success' => $data['success'],
                    'foods_detected' => count($data['foods'] ?? []),
                    'ai_models' => $data['ai_info']['models_used'] ?? []
                ]);
                
                if ($data['success'] && !empty($data['foods'])) {
                    return response()->json([
                        'success' => true,
                        'foods' => $data['foods'],
                        'total_nutrition' => $data['total_nutrition'] ?? null,
                        'foods_count' => $data['foods_count'] ?? 0,
                        'ai_info' => $data['ai_info'] ?? null,
                        'detection_time' => $data['detection_time'] ?? null,
                        'message' => '🤖 Advanced AI detected ' . count($data['foods']) . ' food(s) using ' . 
                                   implode(' + ', $data['ai_info']['models_used'] ?? ['AI']) . '!',
                        'ai_features_used' => [
                            'YOLOv8 Object Detection',
                            'Food-101 Classification (101 dishes)',
                            'OpenCV Advanced Analysis',
                            'Dominant Color Extraction',
                            'Freshness Assessment',
                            '3D Volume Estimation'
                        ]
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'foods' => [],
                        'suggestions' => $data['suggestions'] ?? [],
                        'message' => '🔍 No food detected by advanced AI. Try with better lighting or closer image.',
                        'ai_status' => 'All models loaded and working'
                    ]);
                }
            }
            
            throw new \Exception('AI service error: HTTP ' . $response->status());
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // ✅ CHANGEMENT 4: Gestion spécifique erreur de connexion Docker
            Log::error('AI Food Connection Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Cannot connect to AI Food service',
                'error' => 'Please verify that food-ai container is running (docker-compose ps)',
                'troubleshooting' => [
                    'Run: docker-compose ps food-ai',
                    'Check logs: docker-compose logs food-ai',
                    'Restart service: docker-compose restart food-ai'
                ]
            ], 503);
            
        } catch (\Exception $e) {
            Log::error('AI Food Analysis Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null,
                'troubleshooting' => [
                    'Check if food-ai container is running',
                    'Verify image format is valid',
                    'Try with a clearer food image'
                ]
            ], 500);
        }
    }
    
    public function saveFoodsFromAi(Request $request)
    {
        try {
            $request->validate([
                'foods' => 'required|array|min:1',
                'foods.*.name' => 'required|string',
                'foods.*.nutrition' => 'required|array',
                'foods.*.estimated_weight' => 'required|numeric|min:1',
                'meal_type' => 'required|in:breakfast,lunch,dinner,snack'
            ]);
            
            $user = Auth::user();
            $savedFoods = [];
            $totalCalories = 0;
            
            foreach ($request->foods as $foodData) {
                $nutrition = $foodData['nutrition'];
                
                $foodEntry = FoodEntry::create([
                    'user_id' => $user->id,
                    'food_name' => $foodData['name'],
                    'quantity' => $foodData['estimated_weight'],
                    'unit' => 'g',
                    'meal_type' => $request->meal_type,
                    'calories_per_100g' => round(($nutrition['calories'] / $foodData['estimated_weight']) * 100, 2),
                    'total_calories' => $nutrition['calories'],
                    'proteins' => $nutrition['protein_g'] ?? 0,
                    'carbs' => $nutrition['carbs_g'] ?? 0,
                    'fats' => $nutrition['fat_g'] ?? 0,
                    'fiber' => $nutrition['fiber_g'] ?? 0,
                    'entry_date' => Carbon::today(),
                    'entry_time' => Carbon::now(),
                    'api_source' => 'advanced_ai_docker_v2',  // ✅ CHANGEMENT 5: Indiquer version Docker
                    'api_response' => [
                        'confidence' => $foodData['confidence'] ?? 0,
                        'confidence_level' => $foodData['confidence_level'] ?? 'Unknown',
                        'detection_method' => $foodData['detection_method'] ?? 'ai',
                        'estimated_weight' => $foodData['estimated_weight'],
                        'freshness' => $foodData['freshness'] ?? null,
                        'volume_analysis' => $foodData['volume_analysis'] ?? null,
                        'dominant_colors' => $foodData['dominant_colors'] ?? null,
                        'ai_models_used' => ['YOLOv8', 'Food-101', 'OpenCV'],
                        'ai_analysis_time' => now()->toISOString(),
                        'docker_service' => 'food-ai:5100'  // ✅ Traçabilité Docker
                    ],
                    'notes' => "🐳 Added via Docker AI Food Service\n" .
                              "Service: food-ai:5100\n" .
                              "Models: YOLOv8 + Food-101 + OpenCV\n" .
                              "Confidence: " . ($foodData['confidence_level'] ?? 'Unknown') . 
                              " (" . round(($foodData['confidence'] ?? 0) * 100) . "%)\n" .
                              "Method: " . ($foodData['detection_method'] ?? 'ai') . "\n" .
                              "Freshness: " . ($foodData['freshness']['level'] ?? 'Unknown') . "\n" .
                              "Volume: " . ($foodData['volume_analysis']['estimated_volume_cm3'] ?? 'N/A') . " cm³"
                ]);
                
                $savedFoods[] = $foodEntry;
                $totalCalories += $nutrition['calories'];
            }
            
            // Calculer le nouveau total quotidien
            $todayTotal = FoodEntry::where('user_id', $user->id)
                ->whereDate('entry_date', Carbon::today())
                ->sum('total_calories');
            
            Log::info('AI Foods Saved Successfully', [
                'user_id' => $user->id,
                'foods_count' => count($savedFoods),
                'total_calories_added' => $totalCalories,
                'service' => 'food-ai:5100'  // ✅ Log Docker service
            ]);
            
            return response()->json([
                'success' => true,
                'message' => '🎉 ' . count($savedFoods) . ' food(s) added successfully via Docker AI Food Service!',
                'foods_added' => count($savedFoods),
                'calories_added' => $totalCalories,
                'today_total_calories' => $todayTotal,
                'ai_features_used' => [
                    '🎯 YOLOv8 Object Detection',
                    '🍽️ Food-101 Classification (101 dishes)', 
                    '🎨 OpenCV Advanced Analysis',
                    '🌈 Dominant Color Extraction',
                    '🌿 Freshness Assessment',
                    '📏 3D Volume Estimation',
                    '🥗 Nutritional API Integration'
                ],
                'analysis_details' => [
                    'models_count' => 3,
                    'features_analyzed' => ['colors', 'shapes', 'textures', 'freshness', 'volume'],
                    'confidence_levels' => array_column($request->foods, 'confidence_level'),
                    'docker_service' => 'food-ai:5100'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Save AI Foods Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'foods_data' => $request->foods ?? []
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving AI-detected foods: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
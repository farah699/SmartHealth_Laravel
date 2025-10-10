@extends('partials.layouts.master')

@section('title', 'Nutrition Dashboard | SmartHealth')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">🥗 Nutrition Dashboard</h4>
               
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Profile completion check -->
    @if (!$user->hasCompleteProfile())
        <div class="alert alert-warning">
            <h5><i class="mdi mdi-alert me-1"></i> Incomplete Profile</h5>
            <p class="mb-2">To calculate your personalized goals, please complete your profile.</p>
            <a href="{{ route('nutrition.profile') }}" class="btn btn-sm btn-warning">Complete my profile</a>
        </div>
    @endif

    <!-- Objectifs du jour -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">🔥 Calories</h5>
                            <h3 class="mb-0">{{ number_format($summary->total_calories, 0) }}</h3>
                            <p class="text-muted">/ {{ number_format($summary->calorie_goal, 0) }} kcal</p>
                        </div>
                        <div class="align-self-center">
                            <div class="progress-circle calorie-circle" 
                                 style="--progress-deg: {{ min(360, $summary->calorie_percentage * 3.6) }}deg; 
                                        background: conic-gradient(#727cf5 0deg {{ min(360, $summary->calorie_percentage * 3.6) }}deg, #e9ecef {{ min(360, $summary->calorie_percentage * 3.6) }}deg 360deg);">
                                <div class="progress-circle-inner">
                                    {{ number_format($summary->calorie_percentage, 0) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-primary" style="width: {{ min(100, $summary->calorie_percentage) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">💧 Hydration</h5>
                            <h3 class="mb-0" id="water-total">{{ number_format($summary->total_water_ml, 0) }} ml</h3>
                            <p class="text-muted">/ {{ number_format($summary->water_goal_ml, 0) }} ml</p>
                        </div>
                        <div class="align-self-center">
                            <div class="progress-circle water-circle" id="water-circle"
                                 style="--progress-deg: {{ min(360, $summary->water_percentage * 3.6) }}deg; 
                                        background: conic-gradient(#17a2b8 0deg {{ min(360, $summary->water_percentage * 3.6) }}deg, #e9ecef {{ min(360, $summary->water_percentage * 3.6) }}deg 360deg);">
                                <div class="progress-circle-inner" id="water-percentage">
                                    {{ number_format($summary->water_percentage, 0) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-info" id="water-progress" style="width: {{ min(100, $summary->water_percentage) }}%"></div>
                    </div>
                    
                    <div class="mt-2" id="water-status">
                        @if($summary->water_percentage >= 100)
                            <small class="text-success"><i class="mdi mdi-check-circle me-1"></i>Goal achieved! 🎉</small>
                        @elseif($summary->water_percentage >= 80)
                            <small class="text-warning"><i class="mdi mdi-clock-outline me-1"></i>Almost there! {{ number_format($summary->water_goal_ml - $summary->total_water_ml) }} ml remaining</small>
                        @else
                            <small class="text-info"><i class="mdi mdi-information-outline me-1"></i>{{ number_format($summary->water_goal_ml - $summary->total_water_ml) }} ml remaining</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides avec boutons d'ajout rapide d'eau -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">⚡ Actions Rapides</h5>
                    
                    <!-- Ajout rapide d'eau -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">💧 Quick water add</h6>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-info quick-water" data-amount="250">
                                    🥛 250ml
                                </button>
                                <button type="button" class="btn btn-outline-info quick-water" data-amount="500">
                                    🧴 500ml
                                </button>
                                <button type="button" class="btn btn-outline-info quick-water" data-amount="750">
                                    🍶 750ml
                                </button>
                                <button type="button" class="btn btn-outline-info quick-water" data-amount="1000">
                                    💧 1L
                                </button>
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addWaterModal">
                                    ⚙️ Custom
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <button class="btn btn-soft-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#addFoodModal">
                                <i class="mdi mdi-food-apple me-1"></i>
                                Add food
                            </button>
                        </div>
                        <div class="col">
                            <button class="btn btn-soft-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#addWaterModal">
                                <i class="mdi mdi-cup-water me-1"></i>
                                Add water
                            </button>
                        </div>
                        <div class="col">
                            <button class="btn btn-soft-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#aiFoodModal">
                                <i class="mdi mdi-robot-love me-1"></i>
                                AI Food Scan
                            </button>
                        </div>
                        <div class="col">
                            <a href="{{ route('nutrition.profile') }}" class="btn btn-soft-secondary w-100 mb-2">
                                <i class="mdi mdi-account-cog me-1"></i>
                                Edit profile
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('nutrition.history') }}" class="btn btn-soft-warning w-100 mb-2">
                                <i class="mdi mdi-chart-line me-1"></i>
                                View history
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hydratation d'aujourd'hui -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">💧 Today's Hydration</h5>
                    
                    @if($todayHydrationEntries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Drink</th>
                                        <th>Amount</th>
                                        <th>Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayHydrationEntries as $entry)
                                        <tr>
                                            <td>{{ $entry->drink_type_label }}</td>
                                            <td><strong>{{ $entry->formatted_amount }}</strong></td>
                                            <td>{{ $entry->entry_time->format('H:i') }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('nutrition.hydration.delete', $entry->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Delete this entry?')">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <strong>Total: {{ number_format($todayHydrationEntries->sum('amount_ml'), 0) }} ml</strong>
                            </div>
                            <small class="text-muted">{{ $todayHydrationEntries->count() }} entries</small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-cup-water text-muted" style="font-size: 3rem;"></i>
                            <h6 class="mt-2 text-muted">No hydration recorded today</h6>
                            <p class="text-muted mb-3">Start tracking your hydration to reach your goals.</p>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#addWaterModal">
                                <i class="mdi mdi-plus me-1"></i> Add my first drink
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Today's meals -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">🍽️ Today's Meals</h5>
                    
                    @if($todayFoodEntries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Food</th>
                                        <th>Meal</th>
                                        <th>Amount</th>
                                        <th>Calories</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayFoodEntries as $entry)
                                        <tr>
                                            <td>
                                                <strong>{{ $entry->food_name }}</strong>
                                                @if(isset($entry->brand_name) && $entry->brand_name)
                                                    <br><small class="text-muted">{{ $entry->brand_name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($entry->meal_type)
                                                    @case('breakfast')
                                                        <span class="badge bg-warning">🌅 Breakfast</span>
                                                        @break
                                                    @case('lunch')
                                                        <span class="badge bg-primary">🌞 Lunch</span>
                                                        @break
                                                    @case('dinner')
                                                        <span class="badge bg-info">🌙 Dinner</span>
                                                        @break
                                                    @case('snack')
                                                        <span class="badge bg-success">🍪 Snack</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $entry->meal_type }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $entry->quantity }} {{ $entry->unit }}</td>
                                            <td><strong>{{ number_format($entry->total_calories, 0) }} kcal</strong></td>
                                            <td>
                                                <form method="POST" action="{{ route('nutrition.food.delete', $entry->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Delete this entry?')">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <strong>Total: {{ number_format($todayFoodEntries->sum('total_calories'), 0) }} kcal</strong>
                            </div>
                            <small class="text-muted">{{ $todayFoodEntries->count() }} entries</small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-food-off text-muted" style="font-size: 3rem;"></i>
                            <h6 class="mt-2 text-muted">No meals recorded today</h6>
                            <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addFoodModal">
                                <i class="mdi mdi-plus me-1"></i> Add a meal
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add water -->
<div class="modal fade" id="addWaterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">💧 Add hydration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('nutrition.hydration.store') }}">
                @csrf
                <div class="modal-body">
                    <!-- Drink type -->
                    <div class="mb-3">
                        <label for="drink_type" class="form-label">Drink type</label>
                        <select class="form-select" id="drink_type" name="drink_type" required>
                            <option value="water" selected>💧 Water</option>
                            <option value="tea">🍵 Tea (no sugar)</option>
                            <option value="coffee">☕ Coffee (no sugar)</option>
                            <option value="herbal_tea">🌿 Herbal tea</option>
                            <option value="sparkling_water">🫧 Sparkling water</option>
                            <option value="other">🥤 Other unsweetened drink</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="amount_ml" class="form-label">Amount (ml)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="amount_ml" name="amount_ml" 
                                   min="50" max="2000" value="250" required>
                            <span class="input-group-text">ml</span>
                        </div>
                        <div class="form-text">Between 50ml and 2000ml</div>
                    </div>

                    <!-- Quick amount buttons -->
                    <div class="mb-3">
                        <label class="form-label">Common amounts</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="amount_preset" id="preset_250" value="250">
                            <label class="btn btn-outline-info" for="preset_250">250ml</label>
                            
                            <input type="radio" class="btn-check" name="amount_preset" id="preset_500" value="500">
                            <label class="btn btn-outline-info" for="preset_500">500ml</label>
                            
                            <input type="radio" class="btn-check" name="amount_preset" id="preset_750" value="750">
                            <label class="btn btn-outline-info" for="preset_750">750ml</label>
                            
                            <input type="radio" class="btn-check" name="amount_preset" id="preset_1000" value="1000">
                            <label class="btn btn-outline-info" for="preset_1000">1L</label>
                        </div>
                    </div>

                    <!-- Time -->
                    <div class="mb-3">
                        <label for="entry_time" class="form-label">Time (optional)</label>
                        <input type="time" class="form-control" id="entry_time" name="entry_time" 
                               value="{{ date('H:i') }}">
                        <div class="form-text">Leave empty for current time</div>
                    </div>

                    <!-- Notes -->  
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Ex: With lemon, during sport..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="mdi mdi-plus me-1"></i> Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- AI Food Recognition Modal -->
<div class="modal fade" id="aiFoodModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🤖 AI Food Recognition</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="aiUploadSection">
                    <!-- Meal type selection -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Meal type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="ai_meal_type" id="ai_breakfast" value="breakfast" checked>
                                <label class="btn btn-outline-warning" for="ai_breakfast">🌅 Breakfast</label>
                                
                                <input type="radio" class="btn-check" name="ai_meal_type" id="ai_lunch" value="lunch">
                                <label class="btn btn-outline-primary" for="ai_lunch">🌞 Lunch</label>
                                
                                <input type="radio" class="btn-check" name="ai_meal_type" id="ai_dinner" value="dinner">
                                <label class="btn btn-outline-info" for="ai_dinner">🌙 Dinner</label>
                                
                                <input type="radio" class="btn-check" name="ai_meal_type" id="ai_snack" value="snack">
                                <label class="btn btn-outline-success" for="ai_snack">🍪 Snack</label>
                            </div>
                        </div>
                    </div>

                    <!-- Image upload zone -->
                    <div class="mb-3">
                        <label class="form-label">Upload food image</label>
                        <div class="border-2 border-dashed border-primary rounded p-4 text-center" 
                             id="aiDropZone" 
                             ondrop="dropHandler(event);" 
                             ondragover="dragOverHandler(event);" 
                             ondragleave="dragLeaveHandler(event);"
                             style="cursor: pointer;">
                            <div id="aiUploadContent">
                                <i class="mdi mdi-cloud-upload mdi-48px text-primary mb-2"></i>
                                <h5>Drag and drop your food image here</h5>
                                <p class="text-muted">or click to select a file</p>
                                <small class="text-muted">Supported formats: JPG, PNG, WebP (max 5MB)</small>
                            </div>
                            <input type="file" id="aiFileInput" accept="image/*" style="display: none;">
                        </div>
                    </div>

                    <!-- Image preview -->
                    <div id="aiImagePreview" style="display: none;" class="mb-3">
                        <label class="form-label">Image preview</label>
                        <div class="position-relative">
                            <img id="aiPreviewImage" class="img-fluid rounded" style="max-height: 200px;">
                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" 
                                    onclick="clearAiImage()">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Analyze button -->
                    <div class="d-grid mb-3">
                        <button type="button" id="aiAnalyzeBtn" class="btn btn-primary" onclick="analyzeFood()" disabled>
                            <i class="mdi mdi-robot-love me-1"></i>
                            Analyze with AI
                        </button>
                    </div>
                </div>

                <!-- Loading state -->
                <div id="aiLoadingSection" style="display: none;" class="text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Analyzing your food...</h5>
                    <p class="text-muted">Our AI is recognizing the foods in your image</p>
                </div>

                <!-- Results section -->
                <div id="aiResultsSection" style="display: none;">
                    <h5 class="mb-3">🎯 AI Analysis Results</h5>
                    <div id="aiResultsContent"></div>
                    
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-success" onclick="saveAiResults()">
                            <i class="mdi mdi-check me-1"></i>
                            Save to diary
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="startNewAiAnalysis()">
                            <i class="mdi mdi-refresh me-1"></i>
                            New analysis
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- NEW: Modal Add food with CalorieNinjas -->
<div class="modal fade" id="addFoodModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🍎 Add food</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFoodForm">
                <div class="modal-body">
                    <!-- Meal type -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Meal type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="meal_type" id="breakfast" value="breakfast" checked>
                                <label class="btn btn-outline-warning" for="breakfast">🌅 Breakfast</label>
                                
                                <input type="radio" class="btn-check" name="meal_type" id="lunch" value="lunch">
                                <label class="btn btn-outline-primary" for="lunch">🌞 Lunch</label>
                                
                                <input type="radio" class="btn-check" name="meal_type" id="dinner" value="dinner">
                                <label class="btn btn-outline-info" for="dinner">🌙 Dinner</label>
                                
                                <input type="radio" class="btn-check" name="meal_type" id="snack" value="snack">
                                <label class="btn btn-outline-success" for="snack">🍪 Snack</label>
                            </div>
                        </div>
                    </div>

                    <!-- Food search -->
                    <div class="mb-3">
                        <label for="food_search" class="form-label">Search for food</label>
                        <div class="position-relative">
                            <input type="text" class="form-control" id="food_search" 
                                   placeholder="Ex: apple, rice, chicken..." autocomplete="off">
                            <div class="position-absolute w-100" style="z-index: 1050;">
                                <div id="food_suggestions" class="list-group shadow-sm" style="display: none; max-height: 300px; overflow-y: auto;"></div>
                            </div>
                        </div>
                        <div class="form-text">Type at least 2 characters to see suggestions</div>
                    </div>

                    <!-- Selected food -->
                    <div id="selected_food" style="display: none;" class="mb-3">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <strong id="selected_food_name"></strong>
                                <br><small class="text-muted" id="selected_food_category"></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelectedFood()">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Amount and unit -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   min="0.1" step="0.1" value="100" required>
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label">Unit</label>
                            <select class="form-select" id="unit" name="unit" required>
                                <option value="g">Grams (g)</option>
                                <option value="ml">Milliliters (ml)</option>
                                <option value="piece">Piece(s)</option>
                                <option value="cup">Cup(s)</option>
                                <option value="slice">Slice(s)</option>
                                <option value="tbsp">Tablespoon</option>
                                <option value="tsp">Teaspoon</option>
                                <option value="portion">Portion(s)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nutritional preview -->
                    <div id="nutrition_preview" style="display: none;" class="mb-3">
                        <h6 class="text-muted mb-2">Nutritional preview</h6>
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <h5 class="mb-0 text-primary" id="preview_calories">0</h5>
                                        <small class="text-muted">kcal</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <h5 class="mb-0 text-success" id="preview_protein">0</h5>
                                        <small class="text-muted">g proteins</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <h5 class="mb-0 text-warning" id="preview_carbs">0</h5>
                                        <small class="text-muted">g carbs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <h5 class="mb-0 text-info" id="preview_fat">0</h5>
                                        <small class="text-muted">g fats</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted" id="api_source_info"></small>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes_food" class="form-label">Notes (optional)</label>
                        <textarea class="form-control" id="notes_food" name="notes" rows="2" 
                                  placeholder="Ex: With sauce, grilled, raw..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="calculate_nutrition">
                        <i class="mdi mdi-calculator me-1"></i> Calculate nutrition
                    </button>
                    <button type="button" class="btn btn-success" id="submit_food" disabled>
                        <i class="mdi mdi-plus me-1"></i> Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.progress-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.progress-circle-inner {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
    position: relative;
    z-index: 2;
}

.quick-water:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}

#food_suggestions .list-group-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

#selected_food .alert {
    margin-bottom: 0;
}
</style>



<script>
// Variables globales pour le modal d'aliment
let selectedFood = null;
let nutritionData = null;
let searchTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    // Code existant pour l'eau (fonctionne déjà)
    document.querySelectorAll('.quick-water').forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.dataset.amount;
            const originalText = this.innerHTML;
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding...';
            
            fetch('{{ route("nutrition.hydration.quick") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ amount: amount })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateWaterDisplay(data.newTotal);
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showNotification('Error during addition', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur lors de l\'ajout', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });

    // Boutons de quantité rapide dans le modal eau
    document.querySelectorAll('input[name="amount_preset"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('amount_ml').value = this.value;
        });
    });

    // NOUVEAU : Code pour le modal d'aliment avec vraies API
    const foodSearchInput = document.getElementById('food_search');
    
    if (foodSearchInput) {
        foodSearchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length < 2) {
                hideSuggestions();
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchFoods(query);
            }, 300);
        });

        // Calculer la nutrition
        document.getElementById('calculate_nutrition').addEventListener('click', function() {
            calculateNutrition();
        });

        // Soumettre le formulaire
        document.getElementById('submit_food').addEventListener('click', function() {
            submitFoodEntry();
        });

        // Fermer les suggestions si on clique ailleurs
        document.addEventListener('click', function(e) {
            const suggestionsContainer = document.getElementById('food_suggestions');
            if (!foodSearchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                hideSuggestions();
            }
        });

        // Réinitialiser le modal quand il se ferme
        document.getElementById('addFoodModal').addEventListener('hidden.bs.modal', function() {
            clearSelectedFood();
            document.getElementById('addFoodForm').reset();
            document.getElementById('quantity').value = '100';
            document.getElementById('unit').value = 'g';
            // Réactiver le bouton calculer
            document.getElementById('calculate_nutrition').disabled = false;
        });
    }
});

// CORRIGÉ : Recherche avec vraie API et fallback
function searchFoods(query) {
    // D'abord essayer la vraie API
    fetch(`{{ route('nutrition.food.search') }}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                displaySuggestions(data);
            } else {
                // Fallback avec suggestions locales si pas de résultats API
                const localSuggestions = getSuggestedFoods(query);
                displaySuggestions(localSuggestions);
            }
        })
        .catch(error => {
            console.error('Erreur API:', error);
            // Fallback avec suggestions locales si erreur API
            const localSuggestions = getSuggestedFoods(query);
            displaySuggestions(localSuggestions);
        });
}

// CORRIGÉ : Suggestions locales avec recherche libre
function getSuggestedFoods(query) {
    const foods = [
        { id: 'pomme', name: 'Pomme', category: '🍎 Fruits', unit_suggestions: ['piece', 'g'] },
        { id: 'banane', name: 'Banane', category: '🍌 Fruits', unit_suggestions: ['piece', 'g'] },
        { id: 'riz', name: 'Riz blanc', category: '🍚 Céréales', unit_suggestions: ['g', 'cup'] },
        { id: 'poulet', name: 'Blanc de poulet', category: '🐔 Viandes', unit_suggestions: ['g', 'piece'] },
        { id: 'pain', name: 'Pain blanc', category: '🍞 Céréales', unit_suggestions: ['slice', 'g'] },
        { id: 'lait', name: 'Lait', category: '🥛 Laitiers', unit_suggestions: ['ml', 'cup'] },
        { id: 'œuf', name: 'Œuf', category: '🥚 Protéines', unit_suggestions: ['piece', 'g'] },
        { id: 'tomate', name: 'Tomate', category: '🍅 Légumes', unit_suggestions: ['piece', 'g'] },
        { id: 'fromage', name: 'Fromage', category: '🧀 Laitiers', unit_suggestions: ['g', 'slice'] },
        { id: 'yaourt', name: 'Yaourt', category: '🥛 Laitiers', unit_suggestions: ['g', 'cup'] },
        { id: 'saumon', name: 'Saumon', category: '🐟 Poissons', unit_suggestions: ['g', 'piece'] },
        { id: 'avocat', name: 'Avocat', category: '🥑 Fruits', unit_suggestions: ['piece', 'g'] }
    ];

    let results = foods.filter(food => 
        food.name.toLowerCase().includes(query.toLowerCase()) ||
        food.id.toLowerCase().includes(query.toLowerCase())
    );

    // IMPORTANT : Ajouter une option de recherche libre pour les aliments non listés
    if (query.length >= 2) {
        results.unshift({
            id: 'search_' + Date.now(),
            name: query,
            category: '🔍 Recherche libre - ' + query,
            unit_suggestions: ['g', 'piece', 'cup', 'ml'],
            is_custom: true
        });
    }

    return results.slice(0, 10);
}

// CORRIGÉ : Affichage des suggestions avec support des aliments libres
function displaySuggestions(foods) {
    const container = document.getElementById('food_suggestions');
    
    if (foods.length === 0) {
        container.innerHTML = '<div class="list-group-item text-muted">No food found</div>';
    } else {
        container.innerHTML = foods.map(food => `
            <div class="list-group-item list-group-item-action" 
                 onclick="selectFood('${escapeHtml(food.id)}', '${escapeHtml(food.name)}', '${escapeHtml(food.category)}')">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${escapeHtml(food.name)}</strong>
                        <br><small class="text-muted">${escapeHtml(food.category)}</small>
                    </div>
                    <small class="text-muted">
                        ${food.unit_suggestions ? food.unit_suggestions.join(', ') : 'g, piece'}
                    </small>
                </div>
            </div>
        `).join('');
    }
    
    container.style.display = 'block';
}

function hideSuggestions() {
    document.getElementById('food_suggestions').style.display = 'none';
}

function selectFood(id, name, category) {
    selectedFood = { id, name, category };
    
    document.getElementById('food_search').value = name;
    document.getElementById('selected_food_name').textContent = name;
    document.getElementById('selected_food_category').textContent = category;
    document.getElementById('selected_food').style.display = 'block';
    
    hideSuggestions();
    
    // Activer le bouton calculer
    document.getElementById('calculate_nutrition').disabled = false;
}

function clearSelectedFood() {
    selectedFood = null;
    nutritionData = null;
    
    document.getElementById('food_search').value = '';
    document.getElementById('selected_food').style.display = 'none';
    document.getElementById('nutrition_preview').style.display = 'none';
    document.getElementById('calculate_nutrition').disabled = false;
    document.getElementById('submit_food').disabled = true;
}

// CORRIGÉ : Calcul avec vraie API et fallback
function calculateNutrition() {
    if (!selectedFood) {
        showNotification('Please select a food first', 'error');
        return;
    }
    
    const quantity = document.getElementById('quantity').value;
    const unit = document.getElementById('unit').value;
    
    if (!quantity || quantity <= 0) {
        showNotification('Please enter a valid amount', 'error');
        return;
    }
    
    const button = document.getElementById('calculate_nutrition');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Calculating...';
    
    // Utiliser la vraie API CalorieNinjas
    fetch('{{ route("nutrition.food.calculate-nutrition") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            food_name: selectedFood.name,
            quantity: quantity,
            unit: unit
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.nutrition) {
            nutritionData = data.nutrition;
            displayNutritionPreview(data.nutrition);
            document.getElementById('submit_food').disabled = false;
        } else {
            // Fallback avec calcul local si API échoue
            console.warn('API failed, using fallback calculation');
            const fallbackNutrition = calculateNutritionFallback(selectedFood.name, quantity, unit);
            nutritionData = fallbackNutrition;
            displayNutritionPreview(fallbackNutrition);
            document.getElementById('submit_food').disabled = false;
        }
    })
    .catch(error => {
        console.error('Erreur API:', error);
        // Fallback avec calcul local
        const fallbackNutrition = calculateNutritionFallback(selectedFood.name, quantity, unit);
        nutritionData = fallbackNutrition;
        displayNutritionPreview(fallbackNutrition);
        document.getElementById('submit_food').disabled = false;
        showNotification('⚠️ Utilisation de données approximatives', 'warning');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// NOUVEAU : Calcul nutritionnel de fallback
function calculateNutritionFallback(foodName, quantity, unit) {
    const baseCalories = getBaseCalories(foodName.toLowerCase());
    const factor = getQuantityFactor(quantity, unit);
    
    const calories = Math.round(baseCalories * factor);
    const protein = Math.round(baseCalories * 0.1 * factor * 10) / 10;  // ~10% protéines
    const carbs = Math.round(baseCalories * 0.6 * factor * 10) / 10;    // ~60% glucides  
    const fat = Math.round(baseCalories * 0.3 * factor * 10) / 10;      // ~30% lipides
    
    return {
        calories: calories,
        protein_g: protein,
        carbs_g: carbs,
        fat_g: fat,
        fiber_g: Math.round(factor * 2 * 10) / 10,
        sugar_g: Math.round(carbs * 0.3 * 10) / 10,
        sodium_mg: Math.round(factor * 100),
        success: false, // Indique que c'est une estimation
        per_100g: {
            calories: baseCalories,
            protein: baseCalories * 0.1,
            carbs: baseCalories * 0.6,
            fat: baseCalories * 0.3,
            fiber: 2,
            sugar: baseCalories * 0.18,
            sodium: 100
        }
    };
}

function getBaseCalories(foodName) {
    const calories = {
        'pomme': 52, 'apple': 52,
        'banane': 89, 'banana': 89,
        'riz': 130, 'rice': 130,
        'poulet': 165, 'chicken': 165,
        'pain': 265, 'bread': 265,
        'lait': 42, 'milk': 42,
        'œuf': 155, 'egg': 155,
        'tomate': 18, 'tomato': 18,
        'fromage': 300, 'cheese': 300,
        'yaourt': 60, 'yogurt': 60,
        'saumon': 180, 'salmon': 180,
        'avocat': 160, 'avocado': 160,
        'pâtes': 350, 'pasta': 350,
        'bœuf': 250, 'beef': 250,
        'carotte': 25, 'carrot': 25,
        'brocoli': 25, 'broccoli': 25,
        'orange': 47, 'orange': 47
    };
    
    // Recherche exacte puis partielle
    if (calories[foodName]) return calories[foodName];
    
    for (let key in calories) {
        if (foodName.includes(key) || key.includes(foodName)) {
            return calories[key];
        }
    }
    
    return 100; // Valeur par défaut
}

function getQuantityFactor(quantity, unit) {
    const q = parseFloat(quantity);
    switch (unit) {
        case 'g':
        case 'ml':
            return q / 100;
        case 'piece':
            return q * 0.8; // Pièce moyenne = 80g
        case 'cup':
            return q * 2.4; // 1 tasse = 240g
        case 'tbsp':
            return q * 0.15; // 1 c.à.s = 15g
        case 'tsp':
            return q * 0.05; // 1 c.à.t = 5g
        case 'slice':
            return q * 0.3; // 1 tranche = 30g
        case 'portion':
            return q * 1.5; // 1 portion = 150g
        default:
            return q / 100;
    }
}

// CORRIGÉ : Affichage de l'aperçu nutritionnel
function displayNutritionPreview(nutrition) {
    document.getElementById('preview_calories').textContent = Math.round(nutrition.calories);
    document.getElementById('preview_protein').textContent = nutrition.protein_g.toFixed(1);
    document.getElementById('preview_carbs').textContent = nutrition.carbs_g.toFixed(1);
    document.getElementById('preview_fat').textContent = nutrition.fat_g.toFixed(1);
    
    const sourceText = nutrition.success 
        ? '✅ Données de CalorieNinjas API' 
        : '⚠️ Estimation approximative (API indisponible)';
    document.getElementById('api_source_info').textContent = sourceText;
    
    document.getElementById('nutrition_preview').style.display = 'block';
}

// Remplace la fonction submitFoodEntry existante par celle-ci :

function submitFoodEntry() {
    console.log('=== DÉBUT SOUMISSION ===');
    console.log('selectedFood:', selectedFood);
    console.log('nutritionData:', nutritionData);
    
    if (!selectedFood || !nutritionData) {
        showNotification('Please calculate nutrition first', 'error');
        return;
    }
    
    const mealType = document.querySelector('input[name="meal_type"]:checked').value;
    const quantity = document.getElementById('quantity').value;
    const unit = document.getElementById('unit').value;
    const notes = document.getElementById('notes_food').value;
    
    const formData = {
        meal_type: mealType,
        food_name: selectedFood.name,
        quantity: parseFloat(quantity),
        unit: unit,
        calories_per_100g: nutritionData.per_100g.calories,
        protein_per_100g: nutritionData.per_100g.protein,
        carbs_per_100g: nutritionData.per_100g.carbs,
        fat_per_100g: nutritionData.per_100g.fat,
        fiber_per_100g: nutritionData.per_100g.fiber || 0,
        sugar_per_100g: nutritionData.per_100g.sugar || 0,
        sodium_per_100g: nutritionData.per_100g.sodium || 0,
        notes: notes
    };
    
    console.log('FormData à envoyer:', formData);
    
    const submitButton = document.getElementById('submit_food');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding...';
    
    fetch('{{ route("nutrition.food.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('=== RÉPONSE SERVEUR ===');
        console.log('Status:', response.status);
        console.log('Status OK:', response.ok);
        
        // ✅ CORRIGÉ : Vérifier d'abord si la réponse est OK
        if (!response.ok) {
            console.log('Response not OK, status:', response.status);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Cloner la réponse pour pouvoir la lire en texte si JSON échoue
        return response.clone().json().catch(() => {
            console.log('JSON parse failed, trying text...');
            return response.text().then(text => {
                console.log('Response as text:', text);
                throw new Error('Invalid JSON response: ' + text.substring(0, 200));
            });
        });
    })
    .then(data => {
        console.log('=== DONNÉES REÇUES ===');
        console.log('Response data:', data);
        console.log('data.success:', data.success);
        console.log('typeof data.success:', typeof data.success);
        
        // ✅ CORRIGÉ : Vérification plus robuste
        if (data && (data.success === true || data.success === 'true')) {
            console.log('✅ Succès confirmé');
            showNotification(data.message || '🍎 Aliment ajouté avec succès !', 'success');
            
            // Réinitialiser le formulaire sans fermer le modal
            resetFoodForm();
            
            // Mettre à jour l'affichage des calories
            if (data.today_total) {
                console.log('Mise à jour calories:', data.today_total);
                updateCaloriesDisplay(data.today_total);
            }
            
            // Recharger après 2 secondes pour voir le nouvel aliment
            setTimeout(() => location.reload(), 2000);
            
        } else if (data && data.success === false) {
            console.log('⚠️ Success false mais peut-être ajouté quand même');
            showNotification(data.message || '⚠️ Aliment probablement ajouté, vérification...', 'warning');
            
            // Recharger quand même pour vérifier
            setTimeout(() => location.reload(), 2000);
            
        } else {
            console.log('❓ Réponse inattendue:', data);
            showNotification('Réponse inattendue du serveur, vérification...', 'warning');
            setTimeout(() => location.reload(), 2000);
        }
    })
    .catch(error => {
        console.error('=== ERREUR FETCH ===');
        console.error('Error:', error);
        console.error('Error message:', error.message);
        
        // ✅ CORRIGÉ : Ne pas montrer d'erreur définitive, vérifier d'abord
        showNotification('⚠️ Réponse inattendue, rechargement pour vérifier...', 'warning');
        
        // Recharger pour vérifier si l'aliment a été ajouté malgré l'erreur
        setTimeout(() => location.reload(), 2000);
    })
    .finally(() => {
        console.log('=== NETTOYAGE ===');
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

// ✅ NOUVELLE : Fonction pour réinitialiser le formulaire sans fermer le modal
function resetFoodForm() {
    // Réinitialiser les champs
    clearSelectedFood();
    document.getElementById('quantity').value = '100';
    document.getElementById('unit').value = 'g';
    document.getElementById('notes_food').value = '';
    
    // Réactiver les boutons
    document.getElementById('calculate_nutrition').disabled = false;
    document.getElementById('submit_food').disabled = true;
    
    // Remettre le focus sur la recherche
    document.getElementById('food_search').focus();
}

// Fonction utilitaire pour échapper le HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Fonctions existantes pour l'eau (ne pas changer)
function updateWaterDisplay(newTotal) {
    const goal = {{ $summary->water_goal_ml }};
    const percentage = Math.min(100, Math.round((newTotal / goal) * 100));
    const remaining = Math.max(0, goal - newTotal);
    
    document.getElementById('water-total').textContent = newTotal.toLocaleString() + ' ml';
    document.getElementById('water-percentage').textContent = percentage + '%';
    document.getElementById('water-progress').style.width = percentage + '%';
    
    const waterCircle = document.getElementById('water-circle');
    if (waterCircle) {
        const degrees = Math.min(360, (percentage * 3.6));
        let color = '#17a2b8';
        
        if (percentage >= 100) color = '#28a745';
        else if (percentage >= 80) color = '#17a2b8';
        else if (percentage >= 50) color = '#ffc107';
        else color = '#dc3545';
        
        waterCircle.style.background = `conic-gradient(${color} 0deg ${degrees}deg, #e9ecef ${degrees}deg 360deg)`;
    }
    
    const statusElement = document.getElementById('water-status');
    if (statusElement) {
        if (percentage >= 100) {
            statusElement.innerHTML = '<small class="text-success"><i class="mdi mdi-check-circle me-1"></i>Goal achieved! 🎉</small>';
        } else if (percentage >= 80) {
            statusElement.innerHTML = `<small class="text-warning"><i class="mdi mdi-clock-outline me-1"></i>Almost there! ${remaining.toLocaleString()} ml remaining</small>`;
        } else {
            statusElement.innerHTML = `<small class="text-info"><i class="mdi mdi-information-outline me-1"></i>${remaining.toLocaleString()} ml remaining</small>`;
        }
    }
}

// AMÉLIORÉ : Notifications avec support warning
function showNotification(message, type) {
    let alertClass, icon;
    
    switch(type) {
        case 'success':
            alertClass = 'alert-success';
            icon = '✅';
            break;
        case 'warning':
            alertClass = 'alert-warning';
            icon = '⚠️';
            break;
        case 'error':
        default:
            alertClass = 'alert-danger';
            icon = '❌';
            break;
    }
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 80px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
    alert.innerHTML = `
        <div class="d-flex align-items-center">
            <span class="me-2">${icon}</span>
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => alert.classList.add('show'), 10);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.classList.remove('show');
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 150);
        }
    }, type === 'warning' ? 6000 : 4000); // Plus long pour les warnings
}
function updateCaloriesDisplay(newTotal) {
    // Cette fonction était appelée mais n'existait pas !
    console.log('Nouveau total calories:', newTotal);
    
    // Si tu veux mettre à jour l'affichage sans recharger :
    const calorieElement = document.querySelector('h3.mb-0');
    if (calorieElement && newTotal) {
        calorieElement.textContent = new Intl.NumberFormat().format(Math.round(newTotal));
    }
}

// AI Food Recognition Functions
let aiImageFile = null;
let aiAnalysisResults = null;

// Set up AI modal event listeners
document.addEventListener('DOMContentLoaded', function() {
    const aiDropZone = document.getElementById('aiDropZone');
    const aiFileInput = document.getElementById('aiFileInput');
    
    // Click to select file
    aiDropZone.addEventListener('click', function() {
        aiFileInput.click();
    });
    
    // File input change
    aiFileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleAiFile(e.target.files[0]);
        }
    });
    
    // Reset modal when closed
    document.getElementById('aiFoodModal').addEventListener('hidden.bs.modal', function() {
        resetAiModal();
    });
});

// Drag and drop handlers
function dragOverHandler(ev) {
    ev.preventDefault();
    document.getElementById('aiDropZone').classList.add('border-success');
}

function dragLeaveHandler(ev) {
    ev.preventDefault();
    document.getElementById('aiDropZone').classList.remove('border-success');
}

function dropHandler(ev) {
    ev.preventDefault();
    document.getElementById('aiDropZone').classList.remove('border-success');
    
    if (ev.dataTransfer.items) {
        for (let i = 0; i < ev.dataTransfer.items.length; i++) {
            if (ev.dataTransfer.items[i].kind === 'file') {
                const file = ev.dataTransfer.items[i].getAsFile();
                handleAiFile(file);
                break;
            }
        }
    }
}

// Handle file selection
function handleAiFile(file) {
    // Validate file
    if (!file.type.startsWith('image/')) {
        showNotification('Please select an image file (JPG, PNG, WebP)', 'error');
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) { // 5MB
        showNotification('Image file is too large. Please select a file smaller than 5MB', 'error');
        return;
    }
    
    aiImageFile = file;
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('aiPreviewImage').src = e.target.result;
        document.getElementById('aiImagePreview').style.display = 'block';
        document.getElementById('aiAnalyzeBtn').disabled = false;
    };
    reader.readAsDataURL(file);
}

// Clear selected image
function clearAiImage() {
    aiImageFile = null;
    document.getElementById('aiImagePreview').style.display = 'none';
    document.getElementById('aiAnalyzeBtn').disabled = true;
    document.getElementById('aiFileInput').value = '';
}

// Analyze food with AI
async function analyzeFood() {
    if (!aiImageFile) {
        showNotification('Please select an image first', 'error');
        return;
    }
    
    // Show loading state
    document.getElementById('aiUploadSection').style.display = 'none';
    document.getElementById('aiLoadingSection').style.display = 'block';
    
    try {
        // Convert image to base64
        const base64 = await fileToBase64(aiImageFile);
        
        // Call Laravel API
        console.log('Sending AI analysis request...');
        const response = await fetch('/nutrition/ai/analyze-food', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                image: base64,
                filename: aiImageFile.name
            })
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response not OK:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            console.error('Response was:', responseText);
            throw new Error('Invalid JSON response from server');
        }
        
        if (data.success) {
            aiAnalysisResults = data;
            displayAiResults(data);
        } else {
            throw new Error(data.message || 'Analysis failed');
        }
        
    } catch (error) {
        console.error('AI Analysis error:', error);
        showNotification('Failed to analyze image: ' + error.message, 'error');
        
        // Show upload section again
        document.getElementById('aiLoadingSection').style.display = 'none';
        document.getElementById('aiUploadSection').style.display = 'block';
    }
}

// Display AI results
function displayAiResults(results) {
    let html = '';
    
    if (results.foods && results.foods.length > 0) {
        html += '<div class="alert alert-success"><i class="mdi mdi-check-circle me-2"></i>Found ' + results.foods.length + ' food item(s)!</div>';
        
        results.foods.forEach((food, index) => {
            html += `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title">${escapeHtml(food.name)}</h6>
                                <small class="text-muted">Confidence: ${Math.round(food.confidence * 100)}%</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Per 100g:</small><br>
                                <strong>${food.nutrition.calories} cal</strong>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-3 text-center">
                                <small class="text-muted">Protein</small><br>
                                <strong>${food.nutrition.protein_g}g</strong>
                            </div>
                            <div class="col-3 text-center">
                                <small class="text-muted">Carbs</small><br>
                                <strong>${food.nutrition.carbs_g}g</strong>
                            </div>
                            <div class="col-3 text-center">
                                <small class="text-muted">Fat</small><br>
                                <strong>${food.nutrition.fat_g}g</strong>
                            </div>
                            <div class="col-3 text-center">
                                <small class="text-muted">Fiber</small><br>
                                <strong>${food.nutrition.fiber_g}g</strong>
                            </div>
                        </div>`;
                        
            if (food.freshness || food.dominant_colors || food.detection_method) {
                html += `
                        <div class="mt-2">
                            <small class="text-muted">
                                <strong>AI Analysis:</strong> 
                                Method: ${food.detection_method || 'N/A'}, 
                                Colors: ${food.dominant_colors ? food.dominant_colors.join(', ') : 'N/A'}, 
                                Freshness: ${food.freshness ? food.freshness.level : 'N/A'}
                            </small>
                        </div>`;
            }
            
            html += `
                    </div>
                </div>`;
        });
    } else {
        html += '<div class="alert alert-warning"><i class="mdi mdi-alert me-2"></i>No food items were detected in the image. Please try with a clearer image.</div>';
    }
    
    document.getElementById('aiResultsContent').innerHTML = html;
    document.getElementById('aiLoadingSection').style.display = 'none';
    document.getElementById('aiResultsSection').style.display = 'block';
}

// Save AI results to diary
async function saveAiResults() {
    if (!aiAnalysisResults || !aiAnalysisResults.foods) {
        showNotification('No results to save', 'error');
        return;
    }
    
    try {
        // Get selected meal type
        const mealType = document.querySelector('input[name="ai_meal_type"]:checked').value;
        
        const response = await fetch('/nutrition/ai/save-foods', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                foods: aiAnalysisResults.foods,
                meal_type: mealType
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Foods saved to your diary successfully!', 'success');
            
            // Close modal and refresh page
            document.getElementById('aiFoodModal').querySelector('[data-bs-dismiss="modal"]').click();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Failed to save foods');
        }
        
    } catch (error) {
        console.error('Save error:', error);
        showNotification('Failed to save foods: ' + error.message, 'error');
    }
}

// Start new AI analysis
function startNewAiAnalysis() {
    document.getElementById('aiResultsSection').style.display = 'none';
    document.getElementById('aiUploadSection').style.display = 'block';
    clearAiImage();
    aiAnalysisResults = null;
}

// Reset AI modal
function resetAiModal() {
    document.getElementById('aiUploadSection').style.display = 'block';
    document.getElementById('aiLoadingSection').style.display = 'none';
    document.getElementById('aiResultsSection').style.display = 'none';
    clearAiImage();
    aiAnalysisResults = null;
    
    // Reset meal type to breakfast
    document.getElementById('ai_breakfast').checked = true;
}

// Convert file to base64
function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}
</script>
@endsection
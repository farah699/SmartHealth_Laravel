<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Activity::where('user_id', Auth::id())
            ->with(['sessions' => function($query) {
                $query->latest('session_date');
            }]);

        // Filtres existants
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('activity_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('activity_date', '<=', $request->date_to);
        }

        $activities = $query->latest()->paginate(12);

        // Statistiques
        $allActivities = Activity::where('user_id', Auth::id())->get();
        $stats = [
            'total_activities' => $allActivities->count(),
            'this_week' => $allActivities->where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'total_duration' => $allActivities->sum('duration') + $allActivities->sum(function($activity) {
                return $activity->sessions->sum('duration');
            }),
            'total_distance' => $allActivities->sum('distance') + $allActivities->sum(function($activity) {
                return $activity->sessions->sum('distance');
            }),
            'total_calories' => $allActivities->sum('calories') + $allActivities->sum(function($activity) {
                return $activity->sessions->sum('calories');
            }),
        ];

        return view('activities.index', compact('activities', 'stats'));
    }

    public function create()
    {
        $activityTypes = Activity::getActivityTypes();
        $intensityLevels = Activity::getIntensityLevels();
        
        return view('activities.create', compact('activityTypes', 'intensityLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:course,marche,velo,fitness',
            'intensity' => 'required|in:faible,modere,intense',
            'activity_date' => 'required|date|before_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'duration' => 'nullable|integer|min:1|max:1440',
            'distance' => 'nullable|numeric|min:0|max:999.99',
            'calories' => 'nullable|integer|min:0|max:9999',
            'description' => 'nullable|string|max:1000',
            'heart_rate' => 'nullable|integer|min:40|max:220',
            'weather' => 'nullable|string|max:50',
            'is_recurring' => 'nullable|boolean',
            'target_sessions_per_week' => 'nullable|integer|min:1|max:7',
            'activity_description' => 'nullable|string|max:500'
        ]);

        // Préparer les données supplémentaires
        $additionalData = [];
        if ($request->filled('heart_rate')) {
            $additionalData['heart_rate'] = $request->heart_rate;
        }
        if ($request->filled('weather')) {
            $additionalData['weather'] = $request->weather;
        }

        // Calcul des calories si non fourni
        if (!$validated['calories'] && $validated['duration']) {
            $validated['calories'] = $this->calculateCalories($validated['type'], $validated['duration'], $validated['intensity']);
        }

        // Créer l'activité
        $activityData = [
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'intensity' => $validated['intensity'] ?? null,
            'activity_date' => $validated['activity_date'] ?? null,
            'start_time' => $validated['start_time'],
            'duration' => $validated['duration'] ?? null,
            'distance' => $validated['distance'],
            'calories' => $validated['calories'],
            'description' => $validated['description'],
            'additional_data' => $additionalData,
            'is_recurring' => $request->boolean('is_recurring'),
            'target_sessions_per_week' => $validated['target_sessions_per_week'],
            'activity_description' => $validated['activity_description']
        ];

        $activity = Activity::create($activityData);

        // Si c'est une activité récurrente et qu'on a des données de session, créer la première session
        if ($activity->is_recurring && ($validated['duration'] || $validated['distance'] || $validated['calories'])) {
            $activity->sessions()->create([
                'user_id' => Auth::id(),
                'session_date' => $validated['activity_date'],
                'start_time' => $validated['start_time'],
                'duration' => $validated['duration'] ?? 30,
                'distance' => $validated['distance'],
                'calories' => $validated['calories'],
                'intensity' => $validated['intensity'],
                'session_notes' => $validated['description'],
                'session_data' => $additionalData
            ]);
        }

        $message = $activity->is_recurring 
            ? 'Activité récurrente créée avec succès ! Vous pouvez maintenant y ajouter des sessions.'
            : 'Activité enregistrée avec succès !';

        return redirect()->route('activities.index')->with('success', $message);
    }

    public function show(Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403);
        }

        $activity->load(['sessions' => function($query) {
            $query->orderBy('session_date', 'desc');
        }]);

        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403);
        }

        $activityTypes = Activity::getActivityTypes();
        $intensityLevels = Activity::getIntensityLevels();
        
        return view('activities.edit', compact('activity', 'activityTypes', 'intensityLevels'));
    }

    public function update(Request $request, Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:course,marche,velo,fitness',
            'intensity' => 'nullable|in:faible,modere,intense',
            'activity_date' => 'nullable|date|before_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'duration' => 'nullable|integer|min:1|max:1440',
            'distance' => 'nullable|numeric|min:0|max:999.99',
            'calories' => 'nullable|integer|min:0|max:9999',
            'description' => 'nullable|string|max:1000',
            'heart_rate' => 'nullable|integer|min:40|max:220',
            'weather' => 'nullable|string|max:50',
            'target_sessions_per_week' => 'nullable|integer|min:1|max:7',
            'activity_description' => 'nullable|string|max:500',
            'heart_rate' => 'nullable|integer|min:40|max:220',
        'weather' => 'nullable|string|in:ensoleille,nuageux,pluvieux,venteux,froid,chaud',
        ]);

        // Préparer les données supplémentaires
         $additionalData = [];
    if (!empty($validated['heart_rate'])) {
        $additionalData['heart_rate'] = $validated['heart_rate'];
    }
    if (!empty($validated['weather'])) {
        $additionalData['weather'] = $validated['weather'];
    }

        // Recalcul des calories si nécessaire
        if (!$validated['calories'] && $validated['duration']) {
            $validated['calories'] = $this->calculateCalories($activity->type, $validated['duration'], $validated['intensity']);
        }

        $activity->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'intensity' => $validated['intensity'],
            'activity_date' => $validated['activity_date'],
            'start_time' => $validated['start_time'],
            'duration' => $validated['duration'],
            'distance' => $validated['distance'],
            'calories' => $validated['calories'],
            'description' => $validated['description'],
            'additional_data' => $additionalData,
    'target_sessions_per_week' => $validated['target_sessions_per_week'] ?? null, // CORRECTION ICI
    'activity_description' => $validated['activity_description'] ?? null    
        ]);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activité mise à jour avec succès !');
    }

    public function destroy(Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403);
        }

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activité supprimée avec succès !');
    }

    /**
     * Convertir une activité simple en activité récurrente
     */
    public function convertRecurring(Request $request, Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'target_sessions_per_week' => 'required|integer|min:1|max:7',
            'activity_description' => 'nullable|string|max:500'
        ]);

        // Convertir en récurrente
        $activity->update([
            'is_recurring' => true,
            'target_sessions_per_week' => $validated['target_sessions_per_week'],
            'activity_description' => $validated['activity_description']
        ]);

        // Créer une session avec les données de l'activité originale
        if ($activity->duration || $activity->distance || $activity->calories) {
            $activity->sessions()->create([
                'user_id' => Auth::id(),
                'session_date' => $activity->activity_date,
                'start_time' => $activity->start_time,
                'duration' => $activity->duration ?? 30,
                'distance' => $activity->distance,
                'calories' => $activity->calories,
                'intensity' => $activity->intensity,
                'session_notes' => $activity->description,
                'session_data' => $activity->additional_data ?? []
            ]);
        }

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activité convertie en récurrente avec succès ! Vous pouvez maintenant ajouter des sessions.');
    }

    private function calculateCalories($type, $duration, $intensity)
    {
        $caloriesPerMinute = [
            'course' => ['faible' => 8, 'modere' => 12, 'intense' => 16],
            'marche' => ['faible' => 4, 'modere' => 6, 'intense' => 8],
            'velo' => ['faible' => 6, 'modere' => 10, 'intense' => 14],
            'fitness' => ['faible' => 5, 'modere' => 8, 'intense' => 12]
        ];

        return $duration * ($caloriesPerMinute[$type][$intensity] ?? 8);
    }



public function statistics()
{
    $user = auth()->user();
    
    // Statistiques générales
    $totalActivities = $user->activities()->count();
    $totalSessions = $user->activities()->where('is_recurring', false)->count() + 
                    $user->activities()->where('is_recurring', true)->withCount('sessions')->get()->sum('sessions_count');
    
    $totalDuration = $user->activities()->sum('duration');
    $totalDistance = $user->activities()->sum('distance');
    $totalCalories = $user->activities()->sum('calories');

    // Statistiques par mois (6 derniers mois)
    $monthlyStats = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $month = $date->format('Y-m');
        $monthName = $date->format('M Y');
        
        $activities = $user->activities()->whereYear('activity_date', $date->year)
                          ->whereMonth('activity_date', $date->month)->get();
        
        $monthlyStats[] = [
            'month' => $monthName,
            'activities_count' => $activities->count(),
            'total_duration' => $activities->sum('duration'),
            'total_distance' => $activities->sum('distance'),
            'total_calories' => $activities->sum('calories'),
        ];
    }

    // Statistiques par type d'activité
    $typeStats = [];
    $activityTypes = Activity::getActivityTypes();
    
    foreach ($activityTypes as $key => $type) {
        $userActivities = $user->activities()->where('type', $key)->get();
        
        if ($userActivities->count() > 0) {
            $typeStats[] = [
                'type' => $type['name'],
                'icon' => $type['icon'],
                'color' => $type['color'],
                'count' => $userActivities->count(),
                'duration' => $userActivities->sum('duration'),
                'distance' => $userActivities->sum('distance'),
                'calories' => $userActivities->sum('calories'),
                'avg_duration' => round($userActivities->avg('duration')),
            ];
        }
    }

    // Activités récentes
    $recentActivities = $user->activities()->orderBy('activity_date', 'desc')->limit(10)->get();

    // Objectifs et performances
    $weeklyGoal = $user->activities()->where('is_recurring', true)->sum('target_sessions_per_week');
    $thisWeekSessions = $user->activities()
                            ->whereBetween('activity_date', [now()->startOfWeek(), now()->endOfWeek()])
                            ->count();

    return view('activities.statistics', compact(
        'totalActivities',
        'totalSessions', 
        'totalDuration',
        'totalDistance',
        'totalCalories',
        'monthlyStats',
        'typeStats',
        'recentActivities',
        'weeklyGoal',
        'thisWeekSessions'
    ));
}
}
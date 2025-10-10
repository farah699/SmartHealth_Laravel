<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WellnessEvent;
use App\Models\WellnessCategory;
use App\Models\WellnessStat;
use App\Services\AIRecommendationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WellnessCalendarController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher le calendrier principal
     */
    public function index()
    {
        try {
            // Initialiser le service IA
            $this->aiService = new AIRecommendationService();

            // Créer les catégories par défaut si elles n'existent pas
            $this->ensureDefaultCategories();

            // Récupérer les catégories actives
            $categories = WellnessCategory::where('is_active', true)
                ->orderBy('name')
                ->get();
            
            // Récupérer les statistiques du jour
            $todayStats = $this->getTodayStatsData();
            
            // Récupérer les recommandations IA
            $recommendations = $this->aiService->generateRecommendations();

            return view('apps-calendar', compact('categories', 'todayStats', 'recommendations'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du calendrier: ' . $e->getMessage());
            
            // Valeurs par défaut en cas d'erreur
            $categories = collect();
            $todayStats = [
                'completed_count' => 0,
                'planned_count' => 0,
                'completion_rate' => 0,
                'total_completed_minutes' => 0,
                'total_planned_minutes' => 0,
                'upcoming' => collect()
            ];
            $recommendations = [];

            return view('apps-calendar', compact('categories', 'todayStats', 'recommendations'));
        }
    }

    /**
     * Récupérer les événements pour le calendrier (AJAX)
     */
    public function getEvents(Request $request)
    {
        try {
            $query = WellnessEvent::where('user_id', Auth::id())
                ->with('category');

            // Filtrer par catégories si spécifié
            if ($request->has('categories') && !empty($request->categories)) {
                $query->whereIn('wellness_category_id', $request->categories);
            }

            $events = $query->get()->map(function ($event) {
    $color = $event->category->color ?? '#007bff';
    $textColor = $this->getContrastColor($color);

    // Normaliser les formats pour FullCalendar
    $eventDate = $event->event_date instanceof \Carbon\Carbon
        ? $event->event_date->format('Y-m-d')
        : (string) $event->event_date;

    $startTime = $event->start_time instanceof \Carbon\Carbon
        ? $event->start_time->format('H:i:s')
        : (strlen((string)$event->start_time) === 5 ? $event->start_time . ':00' : (string)$event->start_time);

    $endTime = $event->end_time instanceof \Carbon\Carbon
        ? $event->end_time->format('H:i:s')
        : (strlen((string)$event->end_time) === 5 ? $event->end_time . ':00' : (string)$event->end_time);

    return [
        'id' => $event->id,
        'title' => $event->title,
        'start' => "{$eventDate}T{$startTime}",
        'end' => "{$eventDate}T{$endTime}",
        'backgroundColor' => $color,
        'borderColor' => $color,
        'textColor' => $textColor,
        'className' => 'status-' . $event->status,
        'extendedProps' => [
            'description' => $event->description,
            'category' => $event->category->name ?? '',
            'status' => $event->status,
            'duration' => $event->duration_minutes,
            'mood_before' => $event->mood_before,
            'stress_before' => $event->stress_level_before,
        ]
    ];
});

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des événements: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des événements'], 500);
        }
    }

    /**
     * Créer un nouvel événement
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'wellness_category_id' => 'required|exists:wellness_categories,id',
                'event_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'description' => 'nullable|string|max:1000',
                'mood_before' => 'nullable|in:very_bad,bad,neutral,good,very_good',
                'stress_level_before' => 'nullable|integer|min:1|max:10',
            ], [
                'event_date.after_or_equal' => 'Vous ne pouvez pas planifier une activité dans le passé.',
                'end_time.after' => 'L\'heure de fin doit être après l\'heure de début.',
                'wellness_category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            ]);

            DB::beginTransaction();

            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);
            $durationMinutes = $endTime->diffInMinutes($startTime);

           $eventData = [
    'user_id' => Auth::id(),
    'title' => $request->title,
    'description' => $request->description,
    'wellness_category_id' => $request->wellness_category_id,
    'event_date' => $request->event_date,
    'start_time' => $request->start_time,
    'end_time' => $request->end_time,
    'mood_before' => $request->mood_before,
    'stress_level_before' => $request->stress_level_before,
    'status' => 'planned',
    'is_recurring' => $request->is_recurring ?? false,
    'recurring_config' => $request->is_recurring ? $request->recurring_config : null,
];

            $event = WellnessEvent::create($eventData);

            // Gérer la récurrence si nécessaire
            if ($request->is_recurring && $request->recurring_config) {
                $this->createRecurringEvents($event, $request->recurring_config);
            }

            // Mettre à jour les statistiques
            $this->updateDailyStats($request->event_date);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->is_recurring ? 
                    'Activités récurrentes créées avec succès' : 
                    'Activité créée avec succès',
                'event' => $event
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'événement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'activité'
            ], 500);
        }
    }

    /**
     * Afficher un événement spécifique
     */
    public function show($id)
    {
        try {
            $event = WellnessEvent::where('user_id', Auth::id())
                ->with('category')
                ->findOrFail($id);

            return response()->json($event);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'événement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Événement non trouvé'
            ], 404);
        }
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, $id)
    {
        try {
            $event = WellnessEvent::where('user_id', Auth::id())
                ->findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'wellness_category_id' => 'required|exists:wellness_categories,id',
                'event_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'description' => 'nullable|string|max:1000',
                'mood_before' => 'nullable|in:very_bad,bad,neutral,good,very_good',
                'stress_level_before' => 'nullable|integer|min:1|max:10',
            ], [
                'end_time.after' => 'L\'heure de fin doit être après l\'heure de début.',
                'wellness_category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            ]);

            // Vérifier si la date n'est pas dans le passé (sauf si déjà complété)
            if ($event->status !== 'completed' && Carbon::parse($request->event_date)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas déplacer une activité dans le passé.'
                ], 422);
            }

            DB::beginTransaction();

            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);
            $durationMinutes = $endTime->diffInMinutes($startTime);

            $event->update([
    'title' => $request->title,
    'description' => $request->description,
    'wellness_category_id' => $request->wellness_category_id,
    'event_date' => $request->event_date,
    'start_time' => $request->start_time,
    'end_time' => $request->end_time,
    'mood_before' => $request->mood_before,
    'stress_level_before' => $request->stress_level_before,
]);

            // Mettre à jour les statistiques
            $this->updateDailyStats($request->event_date);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Activité mise à jour avec succès'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de l\'événement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'activité'
            ], 500);
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy($id)
    {
        try {
            $event = WellnessEvent::where('user_id', Auth::id())
                ->findOrFail($id);

            DB::beginTransaction();

            $eventDate = $event->event_date;
            $event->delete();

            // Mettre à jour les statistiques
            $this->updateDailyStats($eventDate);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Activité supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de l\'événement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'activité'
            ], 500);
        }
    }

    /**
     * Marquer un événement comme complété
     */
    public function complete(Request $request, $id)
    {
        try {
            $event = WellnessEvent::where('user_id', Auth::id())
                ->with('category')
                ->findOrFail($id);

            if ($event->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette activité est déjà marquée comme terminée'
                ], 422);
            }

            $request->validate([
                'mood_after' => 'nullable|in:very_bad,bad,neutral,good,very_good',
                'stress_level_after' => 'nullable|integer|min:1|max:10',
                'notes' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            $event->update([
                'status' => 'completed',
                'mood_after' => $request->mood_after,
                'stress_level_after' => $request->stress_level_after,
                'notes' => $request->notes,
                'completed_at' => now(),
            ]);

            // Mettre à jour les statistiques
            $this->updateDailyStats($event->event_date);

            // Générer une recommandation IA personnalisée
            $this->aiService = new AIRecommendationService();
            $aiRecommendation = $this->aiService->generateCompletionRecommendation($event);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Activité terminée avec succès',
                'ai_recommendation' => $aiRecommendation
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la completion de l\'événement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la finalisation de l\'activité'
            ], 500);
        }
    }

    /**
     * Statistiques du jour (API)
     */
    public function getTodayStats()
    {
        try {
            $stats = $this->getTodayStatsData();
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des stats: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des statistiques'], 500);
        }
    }

    /**
     * Récupérer les recommandations IA
     */
   
// Dans WellnessCalendarController.php
public function getAIRecommendations()
{
    try {
        $this->aiService = new AIRecommendationService();
        $recommendations = $this->aiService->generateRecommendations();

        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Bienvenue dans votre calendrier de bien-être',
                'message' => 'Commencez par ajouter des activités pour recevoir des recommandations personnalisées.',
                'action' => 'add_activities',
                'priority' => 'medium'
            ];
        }

        return response()->json([
            'recommendations' => $recommendations,
            'generated_at' => now()->toISOString()
        ]);
    } catch (\Throwable $e) {
        Log::error('Erreur lors de la génération des recommandations IA: ' . $e->getMessage());
        Log::error('Détails de l\'erreur: ' . $e->getTraceAsString());

        return response()->json([
            'recommendations' => [
                [
                    'type' => 'warning',
                    'title' => 'Service temporairement indisponible',
                    'message' => 'Nos recommandations personnalisées seront bientôt de retour. En attendant, essayez de varier vos activités.',
                    'action' => 'try_later',
                    'priority' => 'low'
                ]
            ],
            'generated_at' => now()->toISOString()
        ]);
    }
}
    /**
     * Récupérer les catégories
     */
    public function getCategories()
    {
        try {
            $categories = WellnessCategory::where('is_active', true)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des catégories: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des catégories'], 500);
        }
    }

    /**
     * Créer une nouvelle catégorie
     */
    public function storeCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:wellness_categories',
                'color' => 'required|string|max:7',
                'icon' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:500',
            ]);

            $category = WellnessCategory::create([
                'name' => $request->name,
                'color' => $request->color,
                'icon' => $request->icon,
                'description' => $request->description,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la catégorie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la catégorie'
            ], 500);
        }
    }

    /**
     * Statistiques hebdomadaires
     */
    public function getWeeklyStats()
    {
        try {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $userId = Auth::id();

            $weeklyEvents = WellnessEvent::where('user_id', $userId)
                ->whereBetween('event_date', [$startOfWeek, $endOfWeek])
                ->with('category')
                ->get();

            $stats = [
                'total_events' => $weeklyEvents->count(),
                'completed_events' => $weeklyEvents->where('status', 'completed')->count(),
                'total_minutes' => $weeklyEvents->sum('duration_minutes'),
                'completed_minutes' => $weeklyEvents->where('status', 'completed')->sum('duration_minutes'),
                'by_category' => $weeklyEvents->groupBy('wellness_category_id')->map(function ($events) {
                    return [
                        'count' => $events->count(),
                        'completed' => $events->where('status', 'completed')->count(),
                        'minutes' => $events->sum('duration_minutes'),
                        'category_name' => $events->first()->category->name ?? 'Unknown'
                    ];
                }),
                'by_day' => $weeklyEvents->groupBy(function($event) {
                    return Carbon::parse($event->event_date)->format('Y-m-d');
                })->map(function ($events) {
                    return [
                        'count' => $events->count(),
                        'completed' => $events->where('status', 'completed')->count(),
                        'minutes' => $events->sum('duration_minutes'),
                    ];
                })
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des stats hebdomadaires: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des statistiques'], 500);
        }
    }

    /**
     * Rapport mensuel
     */
    public function monthlyReport()
    {
        try {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $userId = Auth::id();

            $monthlyEvents = WellnessEvent::where('user_id', $userId)
                ->whereBetween('event_date', [$startOfMonth, $endOfMonth])
                ->with('category')
                ->get();

            $report = [
                'period' => [
                    'start' => $startOfMonth->format('Y-m-d'),
                    'end' => $endOfMonth->format('Y-m-d'),
                    'month_name' => $startOfMonth->format('F Y')
                ],
                'summary' => [
                    'total_events' => $monthlyEvents->count(),
                    'completed_events' => $monthlyEvents->where('status', 'completed')->count(),
                    'completion_rate' => $monthlyEvents->count() > 0 ? 
                        round(($monthlyEvents->where('status', 'completed')->count() / $monthlyEvents->count()) * 100, 1) : 0,
                    'total_minutes' => $monthlyEvents->sum('duration_minutes'),
                    'completed_minutes' => $monthlyEvents->where('status', 'completed')->sum('duration_minutes'),
                ],
                'by_week' => [],
                'by_category' => $monthlyEvents->groupBy('wellness_category_id')->map(function ($events) {
                    return [
                        'category_name' => $events->first()->category->name ?? 'Unknown',
                        'count' => $events->count(),
                        'completed' => $events->where('status', 'completed')->count(),
                        'minutes' => $events->sum('duration_minutes'),
                        'avg_stress_reduction' => $this->calculateAvgStressReduction($events->where('status', 'completed'))
                    ];
                })
            ];

            return response()->json($report);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du rapport mensuel: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération du rapport'], 500);
        }
    }

    /**
     * Rapport hebdomadaire
     */
    public function weeklyReport()
    {
        try {
            $stats = $this->getWeeklyStats();
            return $stats; // Retourne la même structure que getWeeklyStats mais formatée pour un rapport
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du rapport hebdomadaire: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération du rapport'], 500);
        }
    }

    /**
     * Analyse du stress
     */
    public function stressAnalysis()
    {
        try {
            $userId = Auth::id();
            $last30Days = Carbon::now()->subDays(30);

            $events = WellnessEvent::where('user_id', $userId)
                ->where('event_date', '>=', $last30Days)
                ->where('status', 'completed')
                ->whereNotNull('stress_level_before')
                ->whereNotNull('stress_level_after')
                ->with('category')
                ->orderBy('event_date')
                ->get();

            $analysis = [
                'period' => [
                    'start' => $last30Days->format('Y-m-d'),
                    'end' => Carbon::now()->format('Y-m-d'),
                    'total_sessions' => $events->count()
                ],
                'overall_stats' => [
                    'avg_stress_before' => round($events->avg('stress_level_before'), 2),
                    'avg_stress_after' => round($events->avg('stress_level_after'), 2),
                    'avg_reduction' => round($events->avg(function($event) {
                        return $event->stress_level_before - $event->stress_level_after;
                    }), 2),
                    'total_reduction' => $events->sum(function($event) {
                        return $event->stress_level_before - $event->stress_level_after;
                    })
                ],
                'by_category' => $events->groupBy('wellness_category_id')->map(function ($categoryEvents) {
                    return [
                        'category_name' => $categoryEvents->first()->category->name,
                        'sessions_count' => $categoryEvents->count(),
                        'avg_stress_before' => round($categoryEvents->avg('stress_level_before'), 2),
                        'avg_stress_after' => round($categoryEvents->avg('stress_level_after'), 2),
                        'avg_reduction' => $this->calculateAvgStressReduction($categoryEvents),
                        'effectiveness_score' => $this->calculateEffectivenessScore($categoryEvents)
                    ];
                }),
                'daily_trend' => $events->groupBy(function($event) {
                    return Carbon::parse($event->event_date)->format('Y-m-d');
                })->map(function ($dayEvents) {
                    return [
                        'avg_stress_before' => round($dayEvents->avg('stress_level_before'), 2),
                        'avg_stress_after' => round($dayEvents->avg('stress_level_after'), 2),
                        'sessions_count' => $dayEvents->count()
                    ];
                })
            ];

            return response()->json($analysis);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse du stress: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'analyse du stress'], 500);
        }
    }

    /**
     * Export des données
     */
    public function exportData(Request $request)
    {
        try {
            $userId = Auth::id();
            $format = $request->get('format', 'json'); // json, csv
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            $events = WellnessEvent::where('user_id', $userId)
                ->whereBetween('event_date', [$startDate, $endDate])
                ->with('category')
                ->orderBy('event_date')
                ->get();

            if ($format === 'csv') {
                return $this->exportToCsv($events);
            }

            return response()->json([
                'export_info' => [
                    'user_id' => $userId,
                    'period' => ['start' => $startDate, 'end' => $endDate],
                    'total_events' => $events->count(),
                    'exported_at' => now()->toISOString()
                ],
                'events' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'export: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'export des données'], 500);
        }
    }

    // Méthodes privées helper

    private function getTodayStatsData()
    {
        $today = Carbon::today();
        $userId = Auth::id();

        $plannedEvents = WellnessEvent::where('user_id', $userId)
            ->where('event_date', $today)
            ->where('status', 'planned')
            ->get();

        $completedEvents = WellnessEvent::where('user_id', $userId)
            ->where('event_date', $today)
            ->where('status', 'completed')
            ->get();

        $upcomingToday = WellnessEvent::where('user_id', $userId)
            ->where('event_date', $today)
            ->where('status', 'planned')
            ->where('start_time', '>', now()->format('H:i:s'))
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $totalPlanned = $plannedEvents->sum('duration_minutes') + $completedEvents->sum('duration_minutes');
        $totalCompleted = $completedEvents->sum('duration_minutes');
        $completionRate = $totalPlanned > 0 ? round(($totalCompleted / $totalPlanned) * 100, 1) : 0;

        return [
            'planned_count' => $plannedEvents->count(),
            'completed_count' => $completedEvents->count(),
            'total_planned_minutes' => $totalPlanned,
            'total_completed_minutes' => $totalCompleted,
            'completion_rate' => $completionRate,
            'upcoming' => $upcomingToday,
        ];
    }

    private function ensureDefaultCategories()
    {
        if (WellnessCategory::count() == 0) {
            $defaultCategories = [
                ['name' => 'Méditation', 'color' => '#9b59b6', 'icon' => 'bi-flower1', 'description' => 'Méditation et mindfulness', 'is_active' => true],
                ['name' => 'Exercice', 'color' => '#e74c3c', 'icon' => 'bi-heart-pulse', 'description' => 'Activité physique', 'is_active' => true],
                ['name' => 'Pauses', 'color' => '#2ecc71', 'icon' => 'bi-pause-circle', 'description' => 'Pauses et temps de repos', 'is_active' => true],
                ['name' => 'Révisions', 'color' => '#3498db', 'icon' => 'bi-book', 'description' => 'Sessions d\'étude et révisions', 'is_active' => true],
                ['name' => 'Détente', 'color' => '#f39c12', 'icon' => 'bi-cup-hot', 'description' => 'Moments de détente', 'is_active' => true],
                ['name' => 'Sommeil', 'color' => '#34495e', 'icon' => 'bi-moon', 'description' => 'Planification du sommeil', 'is_active' => true],
                ['name' => 'Yoga', 'color' => '#8e44ad', 'icon' => 'bi-person-arms-up', 'description' => 'Séances de yoga', 'is_active' => true],
                ['name' => 'Respiration', 'color' => '#16a085', 'icon' => 'bi-wind', 'description' => 'Exercices de respiration', 'is_active' => true],
            ];

            foreach ($defaultCategories as $category) {
                WellnessCategory::create($category);
            }
        }
    }

    private function createRecurringEvents($originalEvent, $config)
    {
        try {
            $frequency = $config['frequency'] ?? 'weekly';
            $occurrences = min($config['occurrences'] ?? 4, 52); // Limiter à 52 occurrences max

            $currentDate = Carbon::parse($originalEvent->event_date);
            
            for ($i = 1; $i < $occurrences; $i++) {
                switch ($frequency) {
                    case 'daily':
                        $currentDate->addDay();
                        break;
                    case 'weekly':
                        $currentDate->addWeek();
                        break;
                    case 'monthly':
                        $currentDate->addMonth();
                        break;
                }

                // Ne pas créer d'événements dans le passé
                if ($currentDate->isPast()) {
                    continue;
                }

                $recurringEvent = $originalEvent->replicate();
                $recurringEvent->event_date = $currentDate->format('Y-m-d');
                $recurringEvent->is_recurring = false; // Les copies ne sont pas récurrentes
                $recurringEvent->recurring_config = null;
                $recurringEvent->parent_event_id = $originalEvent->id;
                $recurringEvent->save();
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création des événements récurrents: ' . $e->getMessage());
        }
    }

    private function updateDailyStats($date)
    {
        try {
            $userId = Auth::id();
            $statsDate = Carbon::parse($date);

            // Calculer les statistiques du jour
            $events = WellnessEvent::where('user_id', $userId)
                ->where('event_date', $date)
                ->get();

            $completedEvents = $events->where('status', 'completed');
            $plannedEvents = $events->where('status', 'planned');

            $totalMinutes = $events->sum('duration_minutes');
            $completedMinutes = $completedEvents->sum('duration_minutes');
            $completionRate = $totalMinutes > 0 ? ($completedMinutes / $totalMinutes) * 100 : 0;

            $avgStressBefore = $completedEvents->whereNotNull('stress_level_before')->avg('stress_level_before');
            $avgStressAfter = $completedEvents->whereNotNull('stress_level_after')->avg('stress_level_after');

            // Créer ou mettre à jour les statistiques
            WellnessStat::updateOrCreate(
                [
                    'user_id' => $userId,
                    'stat_date' => $date,
                ],
                [
                    'total_events' => $events->count(),
                    'completed_events' => $completedEvents->count(),
                    'total_minutes' => $totalMinutes,
                    'completed_minutes' => $completedMinutes,
                    'completion_rate' => round($completionRate, 2),
                    'avg_stress_before' => $avgStressBefore ? round($avgStressBefore, 2) : null,
                    'avg_stress_after' => $avgStressAfter ? round($avgStressAfter, 2) : null,
                    'stress_reduction' => ($avgStressBefore && $avgStressAfter) ? 
                        round($avgStressBefore - $avgStressAfter, 2) : null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des statistiques: ' . $e->getMessage());
        }
    }

    private function getContrastColor($hexColor)
    {
        // Retirer le # si présent
        $hexColor = ltrim($hexColor, '#');
        
        // Convertir en RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Calculer la luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        // Retourner blanc ou noir selon la luminance
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    private function calculateAvgStressReduction($events)
    {
        $validEvents = $events->filter(function($event) {
            return $event->stress_level_before !== null && $event->stress_level_after !== null;
        });

        if ($validEvents->isEmpty()) {
            return null;
        }

        $totalReduction = $validEvents->sum(function($event) {
            return $event->stress_level_before - $event->stress_level_after;
        });

        return round($totalReduction / $validEvents->count(), 2);
    }

    private function calculateEffectivenessScore($events)
    {
        $avgReduction = $this->calculateAvgStressReduction($events);
        
        if ($avgReduction === null) {
            return null;
        }

        // Score sur 100 basé sur la réduction moyenne du stress
        // 0 = aucune réduction, 100 = réduction maximale (théorique de 9 points)
        return round(($avgReduction / 9) * 100, 1);
    }

    private function exportToCsv($events)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="wellness_events_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($events) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'Date', 'Titre', 'Catégorie', 'Début', 'Fin', 'Durée (min)', 
                'Statut', 'Humeur avant', 'Humeur après', 'Stress avant', 
                'Stress après', 'Notes'
            ]);

            // Données
            foreach ($events as $event) {
                fputcsv($file, [
                    $event->event_date,
                    $event->title,
                    $event->category->name ?? '',
                    $event->start_time,
                    $event->end_time,
                    $event->duration_minutes,
                    $event->status,
                    $event->mood_before,
                    $event->mood_after,
                    $event->stress_level_before,
                    $event->stress_level_after,
                    $event->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Dashboard avec analytics
     */
    public function dashboard()
    {
        $userId = Auth::id();
        
        // Stats générales
        $totalEvents = WellnessEvent::where('user_id', $userId)->count();
        $completedEvents = WellnessEvent::where('user_id', $userId)->where('status', 'completed')->count();
        $totalMinutes = WellnessEvent::where('user_id', $userId)->where('status', 'completed')->sum('duration_minutes');
        
        // Stats par catégorie
        $categoryStats = WellnessEvent::where('user_id', $userId)
            ->where('status', 'completed')
            ->with('category')
            ->get()
            ->groupBy('wellness_category_id')
            ->map(function($events) {
                return [
                    'name' => $events->first()->category->name,
                    'color' => $events->first()->category->color,
                    'total_minutes' => $events->sum('duration_minutes'),
                    'count' => $events->count()
                ];
            });

        // Évolution sur 30 jours
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $dailyProgress = WellnessStat::where('user_id', $userId)
            ->where('stat_date', '>=', $thirtyDaysAgo)
            ->orderBy('stat_date')
            ->get()
            ->map(function($stat) {
                return [
                    'date' => $stat->stat_date->format('Y-m-d'),
                    'completion_rate' => $stat->completion_rate,
                    'total_minutes' => $stat->total_completed_minutes
                ];
            });

        return view('apps-calendar', compact(
            'totalEvents', 'completedEvents', 'totalMinutes', 
            'categoryStats', 'dailyProgress'
        ));
    }
}
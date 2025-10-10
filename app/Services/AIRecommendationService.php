<?php

namespace App\Services;

use App\Models\User;
use App\Models\WellnessEvent;
use App\Models\WellnessStat;
use App\Models\QuestionnaireSession;
use App\Models\DailySummary;
use App\Models\WellnessCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AIRecommendationService
{
    protected $user;
    protected $userProfile;
    protected $questionnaires;
    protected $recentEvents;
    protected $nutritionData;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->userProfile = $this->user->profile ?? null;
        $this->loadUserData();
    }

    protected function loadUserData()
    {
        // Charger les données des 30 derniers jours
        $startDate = Carbon::now()->subDays(30);
        
        // Questionnaires récents
        $this->questionnaires = QuestionnaireSession::where('user_id', $this->user->id)
            ->where('completed_at', '>=', $startDate)
            ->orderBy('completed_at', 'desc')
            ->first();

        // Événements récents
        $this->recentEvents = WellnessEvent::where('user_id', $this->user->id)
            ->where('event_date', '>=', $startDate)
            ->with('category')
            ->orderBy('event_date', 'desc')
            ->get();

        // Données nutritionnelles récentes
        $this->nutritionData = DailySummary::where('user_id', $this->user->id)
            ->where('summary_date', '>=', $startDate)
            ->orderBy('summary_date', 'desc')
            ->get();
    }

    public function generateRecommendations(): array
    {
        $recommendations = [];

        // 1. Analyser le stress et l'humeur
        $recommendations = array_merge($recommendations, $this->analyzeStressAndMood());

        // 2. Analyser les habitudes d'activité
        $recommendations = array_merge($recommendations, $this->analyzeActivityPatterns());

        // 3. Analyser les questionnaires
        $recommendations = array_merge($recommendations, $this->analyzeQuestionnaires());

        // 4. Analyser la nutrition
        $recommendations = array_merge($recommendations, $this->analyzeNutrition());

        // 5. Recommandations contextuelles (période d'examens, etc.)
        $recommendations = array_merge($recommendations, $this->generateContextualRecommendations());

        // 6. Recommandations basées sur la régularité
        $recommendations = array_merge($recommendations, $this->analyzeConsistency());

        return array_slice($recommendations, 0, 5); // Limiter à 5 recommandations max
    }

    protected function analyzeStressAndMood(): array
    {
        $recommendations = [];
        $recentCompletedEvents = $this->recentEvents->where('status', 'completed');
        
        if ($recentCompletedEvents->isEmpty()) {
            return $recommendations;
        }

        // Analyser le niveau de stress moyen
        $avgStressBefore = $recentCompletedEvents->whereNotNull('stress_level_before')->avg('stress_level_before');
        $avgStressAfter = $recentCompletedEvents->whereNotNull('stress_level_after')->avg('stress_level_after');
        
        if ($avgStressBefore >= 7) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Niveau de stress élevé détecté',
                'message' => 'Votre niveau de stress moyen est de ' . round($avgStressBefore, 1) . '/10. Je recommande d\'augmenter vos sessions de méditation et d\'ajouter des pauses plus fréquentes.',
                'action' => 'increase_relaxation',
                'priority' => 'high'
            ];
        }

        // Analyser l'efficacité des activités anti-stress
        if ($avgStressBefore && $avgStressAfter) {
            $stressReduction = $avgStressBefore - $avgStressAfter;
            if ($stressReduction < 1) {
                $recommendations[] = [
                    'type' => 'info',
                    'title' => 'Optimisation des activités anti-stress',
                    'message' => 'Vos activités actuelles réduisent peu votre stress. Essayez des sessions plus longues de yoga ou de respiration profonde.',
                    'action' => 'optimize_stress_activities',
                    'priority' => 'medium'
                ];
            }
        }

        // Analyser l'humeur
        $moodValues = ['very_bad' => 1, 'bad' => 2, 'neutral' => 3, 'good' => 4, 'very_good' => 5];
        $moodBefore = $recentCompletedEvents->whereNotNull('mood_before');
        
        if ($moodBefore->count() > 0) {
            $avgMoodBefore = $moodBefore->avg(function($event) use ($moodValues) {
                return $moodValues[$event->mood_before];
            });

            if ($avgMoodBefore <= 2.5) {
                $recommendations[] = [
                    'type' => 'warning',
                    'title' => 'Humeur préoccupante',
                    'message' => 'Votre humeur générale semble basse ces derniers temps. Planifiez plus d\'activités plaisantes et considérez parler à un professionnel.',
                    'action' => 'mood_support',
                    'priority' => 'high'
                ];
            }
        }

        return $recommendations;
    }

    protected function analyzeActivityPatterns(): array
    {
        $recommendations = [];
        $last7Days = $this->recentEvents->where('event_date', '>=', Carbon::now()->subDays(7));
        
        // Analyser la régularité
        $completionRate = $last7Days->count() > 0 ? 
            ($last7Days->where('status', 'completed')->count() / $last7Days->count()) * 100 : 0;

        if ($completionRate < 50) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Faible taux de réalisation',
                'message' => 'Vous ne réalisez que ' . round($completionRate) . '% de vos activités planifiées. Essayez de planifier des sessions plus courtes (10-15 min).',
                'action' => 'reduce_duration',
                'priority' => 'medium'
            ];
        }

        // Analyser les catégories manquantes
        $categoriesUsed = $last7Days->pluck('wellness_category_id')->unique();
        $allCategories = WellnessCategory::where('is_active', true)->pluck('id');
        $missingCategories = $allCategories->diff($categoriesUsed);

        if ($missingCategories->count() > 0) {
            $missingCategoryNames = WellnessCategory::whereIn('id', $missingCategories)->pluck('name');
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Diversifiez vos activités',
                'message' => 'Vous n\'avez pas pratiqué : ' . $missingCategoryNames->implode(', ') . ' cette semaine. Variez vos activités pour un bien-être optimal.',
                'action' => 'diversify_activities',
                'priority' => 'low'
            ];
        }

        return $recommendations;
    }

    protected function analyzeQuestionnaires(): array
    {
        $recommendations = [];
        
        if (!$this->questionnaires) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Évaluation recommandée',
                'message' => 'Effectuez une évaluation psychologique pour obtenir des recommandations personnalisées.',
                'action' => 'take_questionnaire',
                'priority' => 'medium'
            ];
            return $recommendations;
        }

        // Analyser les scores PHQ-9 (dépression)
        if ($this->questionnaires->phq9_score >= 10) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Signes dépressifs détectés',
                'message' => 'Vos résultats indiquent des symptômes dépressifs. Augmentez vos activités physiques et sociales. Consultez un professionnel si les symptômes persistent.',
                'action' => 'depression_support',
                'priority' => 'high'
            ];
        }

        // Analyser les scores GAD-7 (anxiété)
        if ($this->questionnaires->gad7_score >= 10) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Niveau d\'anxiété élevé',
                'message' => 'Vos résultats montrent un niveau d\'anxiété significatif. Pratiquez la méditation quotidienne et des exercices de respiration.',
                'action' => 'anxiety_management',
                'priority' => 'high'
            ];
        }

        return $recommendations;
    }

    protected function analyzeNutrition(): array
    {
        $recommendations = [];
        $recentNutrition = $this->nutritionData->take(7);
        
        if ($recentNutrition->isEmpty()) {
            return $recommendations;
        }

        // Analyser l'hydratation
        $avgWaterPercentage = $recentNutrition->avg('water_percentage');
        if ($avgWaterPercentage < 80) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Hydratation insuffisante',
                'message' => 'Vous n\'atteignez que ' . round($avgWaterPercentage) . '% de votre objectif d\'hydratation. Une bonne hydratation améliore l\'humeur et réduit le stress.',
                'action' => 'increase_hydration',
                'priority' => 'medium'
            ];
        }

        // Analyser les calories
        $avgCaloriePercentage = $recentNutrition->avg('calorie_percentage');
        if ($avgCaloriePercentage < 70) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Apport calorique insuffisant',
                'message' => 'Votre apport calorique est trop faible (' . round($avgCaloriePercentage) . '% de l\'objectif). Cela peut affecter votre énergie et votre humeur.',
                'action' => 'increase_calories',
                'priority' => 'medium'
            ];
        }

        return $recommendations;
    }

    protected function generateContextualRecommendations(): array
    {
        $recommendations = [];
        $currentMonth = Carbon::now()->month;
        $currentDay = Carbon::now()->day;

        // Détection de période d'examens (mai-juin, décembre-janvier)
        if (in_array($currentMonth, [5, 6, 12, 1])) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Période d\'examens détectée',
                'message' => 'C\'est la période des examens ! Planifiez des pauses régulières toutes les 2h, des sessions de yoga pour gérer le stress, et maintenez un sommeil régulier.',
                'action' => 'exam_period_support',
                'priority' => 'high'
            ];
        }

        // Recommandations saisonnières
        if (in_array($currentMonth, [12, 1, 2])) { // Hiver
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Bien-être hivernal',
                'message' => 'En hiver, augmentez vos activités intérieures et assurez-vous d\'avoir suffisamment de lumière. La méditation et le yoga sont particulièrement bénéfiques.',
                'action' => 'winter_wellness',
                'priority' => 'low'
            ];
        }

        // Recommandations en fonction du jour de la semaine
        $dayOfWeek = Carbon::now()->dayOfWeek;
        if ($dayOfWeek == Carbon::MONDAY) {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'Démarrez la semaine en douceur',
                'message' => 'C\'est lundi ! Planifiez une séance de méditation matinale pour bien commencer la semaine.',
                'action' => 'monday_motivation',
                'priority' => 'low'
            ];
        }

        return $recommendations;
    }

    protected function analyzeConsistency(): array
    {
        $recommendations = [];
        $last14Days = $this->recentEvents->where('event_date', '>=', Carbon::now()->subDays(14));
        
        // Calculer la streak
        $streak = $this->calculateCurrentStreak();
        
        if ($streak == 0) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Recommencer en douceur',
                'message' => 'Vous n\'avez pas d\'activité récente. Commencez par 10 minutes de méditation ou une courte promenade aujourd\'hui.',
                'action' => 'restart_gentle',
                'priority' => 'medium'
            ];
        } elseif ($streak >= 7) {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'Excellente régularité !',
                'message' => 'Bravo ! Vous maintenez une routine depuis ' . $streak . ' jours. Continuez ainsi pour maximiser les bénéfices.',
                'action' => 'maintain_streak',
                'priority' => 'low'
            ];
        }

        return $recommendations;
    }

    protected function calculateCurrentStreak(): int
    {
        $streak = 0;
        $date = Carbon::today();
        
        while (true) {
            $hasActivity = $this->recentEvents
                ->where('event_date', $date->format('Y-m-d'))
                ->where('status', 'completed')
                ->isNotEmpty();
                
            if ($hasActivity) {
                $streak++;
                $date->subDay();
            } else {
                break;
            }
        }
        
        return $streak;
    }

    public function generateCompletionRecommendation($event): ?string
    {
        $category = $event->category;
        $duration = $event->duration_minutes;
        $stressBefore = $event->stress_level_before;
        $stressAfter = $event->stress_level_after;
        $moodBefore = $event->mood_before;
        $moodAfter = $event->mood_after;

        $recommendations = [];

        // Analyser l'efficacité de l'activité
        if ($stressBefore && $stressAfter) {
            $stressReduction = $stressBefore - $stressAfter;
            
            if ($stressReduction < 1 && $category->name == 'Méditation') {
                $recommendations[] = "Votre niveau de stress n'a pas beaucoup diminué. Essayez d'augmenter la durée de méditation à " . ($duration + 10) . " minutes la prochaine fois.";
            }
            
            if ($stressReduction >= 3) {
                $recommendations[] = "Excellente réduction de stress ! Cette activité vous fait vraiment du bien. Pensez à la répéter régulièrement.";
            }
        }

        // Recommandations spécifiques par catégorie
        switch ($category->name) {
            case 'Méditation':
                if ($duration < 15) {
                    $recommendations[] = "Pour maximiser les bénéfices de la méditation, essayez progressivement d'atteindre 15-20 minutes par session.";
                }
                break;
                
            case 'Exercice':
                $recommendations[] = "Excellent ! L'exercice libère des endorphines. N'oubliez pas de vous hydrater et de bien récupérer.";
                break;
                
            case 'Pauses':
                if ($stressBefore >= 7) {
                    $recommendations[] = "Votre stress était élevé avant cette pause. Considérez prendre des pauses plus fréquentes (toutes les 90 minutes) pour prévenir l'accumulation de stress.";
                }
                break;
        }

        // Recommandations basées sur l'humeur
        if ($moodBefore && $moodAfter) {
            $moodValues = ['very_bad' => 1, 'bad' => 2, 'neutral' => 3, 'good' => 4, 'very_good' => 5];
            $moodImprovement = $moodValues[$moodAfter] - $moodValues[$moodBefore];
            
            if ($moodImprovement >= 2) {
                $recommendations[] = "Votre humeur s'est nettement améliorée ! Cette activité est particulièrement bénéfique pour vous.";
            } elseif ($moodImprovement <= 0 && $moodValues[$moodBefore] <= 2) {
                $recommendations[] = "Votre humeur reste difficile. Si cela persiste, n'hésitez pas à parler à un professionnel de santé.";
            }
        }

        return !empty($recommendations) ? implode(' ', $recommendations) : null;
    }
}
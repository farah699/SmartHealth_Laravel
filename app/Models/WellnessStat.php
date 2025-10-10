<?php
// filepath: app/Models/WellnessStat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WellnessStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stat_date',
        'total_planned_minutes',
        'total_completed_minutes',
        'completion_rate',
        'average_mood_before',
        'average_mood_after',
        'average_stress_before',
        'average_stress_after',
        'streak_days',
        'category_breakdown'
    ];

    protected $casts = [
        'stat_date' => 'date',
        'category_breakdown' => 'array'
    ];

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculer les statistiques pour un utilisateur et une date
     */
    public static function calculateForUserAndDate($userId, $date = null)
    {
        $date = $date ?? Carbon::today();
        
        $events = WellnessEvent::where('user_id', $userId)
            ->whereDate('event_date', $date)
            ->get();

        $totalPlanned = $events->sum('duration_minutes');
        $completedEvents = $events->where('status', 'completed');
        $totalCompleted = $completedEvents->sum('duration_minutes');
        
        $completionRate = $totalPlanned > 0 ? ($totalCompleted / $totalPlanned) * 100 : 0;
        
        // Calculer moyennes d'humeur et stress
        $moodValues = ['very_bad' => 1, 'bad' => 2, 'neutral' => 3, 'good' => 4, 'very_good' => 5];
        
        $avgMoodBefore = null;
        $avgMoodAfter = null;
        $avgStressBefore = null;
        $avgStressAfter = null;
        
        if ($completedEvents->count() > 0) {
            $moodBefore = $completedEvents->whereNotNull('mood_before');
            if ($moodBefore->count() > 0) {
                $avgMoodBefore = $moodBefore->avg(function($event) use ($moodValues) {
                    return $moodValues[$event->mood_before];
                });
            }
            
            $moodAfter = $completedEvents->whereNotNull('mood_after');
            if ($moodAfter->count() > 0) {
                $avgMoodAfter = $moodAfter->avg(function($event) use ($moodValues) {
                    return $moodValues[$event->mood_after];
                });
            }
            
            $avgStressBefore = $completedEvents->whereNotNull('stress_level_before')->avg('stress_level_before');
            $avgStressAfter = $completedEvents->whereNotNull('stress_level_after')->avg('stress_level_after');
        }

        // Breakdown par catégorie
        $categoryBreakdown = $completedEvents->groupBy('wellness_category_id')
            ->map(function($categoryEvents) {
                return $categoryEvents->sum('duration_minutes');
            })->toArray();

        // Calculer streak
        $streakDays = self::calculateStreak($userId, $date);

        return self::updateOrCreate(
            ['user_id' => $userId, 'stat_date' => $date],
            [
                'total_planned_minutes' => $totalPlanned,
                'total_completed_minutes' => $totalCompleted,
                'completion_rate' => round($completionRate, 2),
                'average_mood_before' => $avgMoodBefore ? round($avgMoodBefore, 2) : null,
                'average_mood_after' => $avgMoodAfter ? round($avgMoodAfter, 2) : null,
                'average_stress_before' => $avgStressBefore ? round($avgStressBefore, 2) : null,
                'average_stress_after' => $avgStressAfter ? round($avgStressAfter, 2) : null,
                'streak_days' => $streakDays,
                'category_breakdown' => $categoryBreakdown
            ]
        );
    }

    /**
     * Calculer la série de jours consécutifs
     */
    private static function calculateStreak($userId, $currentDate)
    {
        $streak = 0;
        $date = Carbon::parse($currentDate);
        
        while (true) {
            $dayEvents = WellnessEvent::where('user_id', $userId)
                ->whereDate('event_date', $date)
                ->where('status', 'completed')
                ->exists();
                
            if ($dayEvents) {
                $streak++;
                $date->subDay();
            } else {
                break;
            }
        }
        
        return $streak;
    }
}
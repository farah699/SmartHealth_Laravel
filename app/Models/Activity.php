<?php
// filepath: c:\Users\ferie\OneDrive\Bureau\projetLaravel\SmartHealth_Laravel\app\Models\Activity.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'type', 'duration', 'distance', 'calories', 'description',
        'activity_date', 'start_time', 'intensity', 'additional_data',
        'is_recurring', 'status', 'target_sessions_per_week', 'activity_description'
    ];

    protected $casts = [
        'activity_date' => 'date',
        'start_time' => 'datetime:H:i',
        'additional_data' => 'array',
        'is_recurring' => 'boolean'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les sessions
     */
    public function sessions()
    {
        return $this->hasMany(ActivitySession::class)->orderBy('session_date', 'desc');
    }

    public function thisWeekSessions()
    {
        return $this->sessions()->thisWeek();
    }

    public function lastSession()
    {
        return $this->sessions()->latest('session_date')->first();
    }

    public function getTotalSessionsAttribute()
    {
        return $this->sessions()->count();
    }

    public function getStatsAttribute()
    {
        $sessions = $this->sessions();
        
        return [
            'total_sessions' => $sessions->count(),
            'total_duration' => $sessions->sum('duration'),
            'total_distance' => $sessions->sum('distance') ?? 0,
            'total_calories' => $sessions->sum('calories') ?? 0,
            'average_duration' => $sessions->avg('duration') ?? 0,
            'average_distance' => $sessions->avg('distance') ?? 0,
            'average_rating' => $sessions->avg('rating') ?? 0,
            'this_week_sessions' => $sessions->thisWeek()->count(),
            'last_session_date' => $sessions->latest('session_date')->value('session_date')
        ];
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Accessor pour formater la durée
     */
    public function getFormattedDurationAttribute()
    {
        // Si c'est une activité récurrente, prendre la durée moyenne des sessions
        if ($this->is_recurring && $this->sessions->count() > 0) {
            $avgDuration = $this->sessions->avg('duration');
            $hours = floor($avgDuration / 60);
            $minutes = $avgDuration % 60;
        } else {
            $hours = floor($this->duration / 60);
            $minutes = $this->duration % 60;
        }
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}min";
        }
        return "{$minutes}min";
    }

    /**
     * Calculer la vitesse moyenne
     */
    public function getAverageSpeedAttribute()
    {
        // Si récurrente, calculer depuis les sessions
        if ($this->is_recurring && $this->sessions->count() > 0) {
            $totalDistance = $this->sessions->sum('distance');
            $totalDuration = $this->sessions->sum('duration');
        } else {
            $totalDistance = $this->distance;
            $totalDuration = $this->duration;
        }

        if (!$totalDistance || !$totalDuration) {
            return null;
        }
        
        return round(($totalDistance / ($totalDuration / 60)), 2);
    }

    


    /**
     * Types d'activités disponibles
     */
    public static function getActivityTypes()
    {
        
        return [
        'course' => [
            'name' => 'Course',
            'icon' => 'bi bi-person-running', // Correction: ajouter l'espace
            'color' => 'danger'
        ],
        'marche' => [
            'name' => 'Marche',
            'icon' => 'bi bi-person-walking', // Correction: ajouter l'espace
            'color' => 'success'
        ],
        'velo' => [
            'name' => 'Vélo',
            'icon' => 'bi bi-bicycle', // Correction: ajouter l'espace
            'color' => 'warning'
        ],
        'fitness' => [
            'name' => 'Fitness',
            'icon' => 'bi bi-heart-pulse', // Correction: ajouter l'espace
            'color' => 'info'
        ],
         'natation' => [
            'name' => 'Natation',
            'icon' => 'bi bi-water',
            'color' => 'primary'
        ],
        'yoga' => [
            'name' => 'Yoga',
            'icon' => 'bi bi-flower1',
            'color' => 'secondary'
        ],
        'autre' => [
            'name' => 'Autre',
            'icon' => 'bi bi-activity',
            'color' => 'dark'
        ]
    ];
    }

    /**
     * Niveaux d'intensité
     */
    public static function getIntensityLevels()
    {
        return [
            'faible' => [
                'name' => 'Faible',
                'color' => 'success',
                'description' => 'Effort léger, respiration normale'
            ],
            'modere' => [
                'name' => 'Modéré',
                'color' => 'warning',
                'description' => 'Effort moyen, respiration légèrement accélérée'
            ],
            'intense' => [
                'name' => 'Intense',
                'color' => 'danger',
                'description' => 'Effort soutenu, respiration rapide'
            ]
        ];
    }

    
}
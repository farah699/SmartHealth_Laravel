<?php
// filepath: app/Models/WellnessEvent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WellnessEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wellness_category_id',
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'status',
        'mood_before',
        'mood_after',
        'stress_level_before',
        'stress_level_after',
        'notes',
        'is_recurring',
        'recurring_config',
        'completed_at'
    ];

    protected $casts = [
    // Force une date en chaîne "Y-m-d" pour éviter la concat invalide
    'event_date' => 'date:Y-m-d',
    // Laisser les heures en string
    // (ne pas caster start_time / end_time en datetime)
    'completed_at' => 'datetime',
    'is_recurring' => 'boolean',
    'recurring_config' => 'array'
];

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(WellnessCategory::class, 'wellness_category_id');
    }

    /**
     * Calculer la durée automatiquement
     */
    protected static function boot()
{
    parent::boot();
    
    static::saving(function ($event) {
        if ($event->start_time && $event->end_time) {
            $start = Carbon::parse($event->start_time);
            $end = Carbon::parse($event->end_time);

            // Gérer le cas où l'heure de fin passe après minuit
            if ($end->lessThanOrEqualTo($start)) {
                $end = $end->copy()->addDay();
            }

            // Forcer un résultat positif
            $event->duration_minutes = $start->diffInMinutes($end, true);
        }
    });
}

    /**
     * Scopes
     */
    public function scopeToday($query)
    {
        return $query->whereDate('event_date', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('event_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Accesseurs
     */
    public function getMoodImprovementAttribute()
    {
        if (!$this->mood_before || !$this->mood_after) return null;
        
        $moodValues = [
            'very_bad' => 1, 'bad' => 2, 'neutral' => 3, 'good' => 4, 'very_good' => 5
        ];
        
        return $moodValues[$this->mood_after] - $moodValues[$this->mood_before];
    }

    public function getStressReductionAttribute()
    {
        if (!$this->stress_level_before || !$this->stress_level_after) return null;
        return $this->stress_level_before - $this->stress_level_after;
    }

    /**
     * Marquer comme complété
     */
    public function markAsCompleted($moodAfter = null, $stressAfter = null, $notes = null)
    {
        $this->update([
            'status' => 'completed',
            'mood_after' => $moodAfter,
            'stress_level_after' => $stressAfter,
            'notes' => $notes,
            'completed_at' => now()
        ]);
    }

    /**
     * Types d'humeur
     */
    public static function getMoodTypes()
    {
        return [
            'very_bad' => ['label' => 'Très mauvaise', 'emoji' => '😢', 'value' => 1],
            'bad' => ['label' => 'Mauvaise', 'emoji' => '😞', 'value' => 2],
            'neutral' => ['label' => 'Neutre', 'emoji' => '😐', 'value' => 3],
            'good' => ['label' => 'Bonne', 'emoji' => '😊', 'value' => 4],
            'very_good' => ['label' => 'Très bonne', 'emoji' => '😄', 'value' => 5],
        ];
    }
}
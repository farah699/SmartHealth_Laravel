<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'exercise_name',
        'exercise_type',
        'duration_minutes',
        'intensity_level',
        'calories_burned',
        'description',
        'instructions',
        'equipment_needed',
        'target_muscle_groups',
        'user_age',
        'user_imc',
        'user_fitness_level',
        'recommended_score',
        'is_watched',
        'is_completed',
        'watch_duration'
    ];

    protected $casts = [
        'target_muscle_groups' => 'array',
        'equipment_needed' => 'array',
        'is_watched' => 'boolean',
        'is_completed' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function getScorePercentageAttribute()
    {
        return round($this->recommended_score, 1) . '%';
    }

    public function getScoreColorAttribute()
    {
        if ($this->recommended_score >= 80) return 'success';
        if ($this->recommended_score >= 60) return 'warning';
        return 'danger';
    }


    
}
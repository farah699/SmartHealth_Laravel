<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'category',
        'difficulty_level',
        'duration_min',
        'duration_max',
        'calories_per_minute',
        'description',
        'instructions',
        'equipment_needed',
        'target_muscle_groups',
        'age_min',
        'age_max',
        'imc_min',
        'imc_max',
        'contraindications',
        'video_url',
        'video_thumbnail',
        'video_duration'
    ];

    protected $casts = [
        'equipment_needed' => 'array',
        'target_muscle_groups' => 'array',
        'contraindications' => 'array'
    ];

    public function recommendations()
    {
        return $this->hasMany(ExerciseRecommendation::class);
    }

    public function videos()
    {
        return $this->hasMany(ExerciseVideo::class);
    }

    public function isAvailableForUser($user)
    {
        $age = $user->age;
        $imc = $user->imc;

        return $age >= $this->age_min && 
               $age <= $this->age_max && 
               $imc >= $this->imc_min && 
               $imc <= $this->imc_max;
    }

    public function getMainVideo()
    {
        return $this->videos()->where('is_active', true)->first();
    }

    public static function getExerciseTypes()
    {
        return [
            'cardio' => 'Cardio',
            'strength' => 'Musculation',
            'flexibility' => 'Flexibilité',
            'balance' => 'Équilibre',
            'yoga' => 'Yoga',
            'pilates' => 'Pilates'
        ];
    }

    public static function getDifficultyLevels()
    {
        return [
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé'
        ];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivitySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id', 'user_id', 'session_date', 'start_time', 'duration',
        'distance', 'calories', 'intensity', 'session_notes', 'session_data',
        'rating', 'difficulty'
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime:H:i',
        'session_data' => 'array',
        'rating' => 'decimal:1'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        return $hours > 0 ? "{$hours}h {$minutes}min" : "{$minutes}min";
    }

    public function getAverageSpeedAttribute()
    {
        if (!$this->distance || !$this->duration) return null;
        return round(($this->distance / ($this->duration / 60)), 2);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('session_date', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->where('session_date', '>=', Carbon::now()->startOfWeek());
    }

    public static function getDifficultyLevels()
    {
        return [
            'tres_facile' => ['name' => 'Très facile', 'color' => 'success', 'icon' => 'bi-emoji-smile'],
            'facile' => ['name' => 'Facile', 'color' => 'info', 'icon' => 'bi-emoji-neutral'],
            'normal' => ['name' => 'Normal', 'color' => 'warning', 'icon' => 'bi-emoji-neutral'],
            'difficile' => ['name' => 'Difficile', 'color' => 'danger', 'icon' => 'bi-emoji-frown'],
            'tres_difficile' => ['name' => 'Très difficile', 'color' => 'dark', 'icon' => 'bi-emoji-dizzy']
        ];
    }
}
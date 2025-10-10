<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'duration',
        'quality',
        'language',
        'subtitles',
        'views_count',
        'rating',
        'is_active'
    ];

    protected $casts = [
        'subtitles' => 'array',
        'is_active' => 'boolean'
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
<?php
// app/Models/YogaUserStats.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YogaUserStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'total_sessions',
        'total_duration',
        'current_streak',
        'best_streak',
        'last_practice_date',
        'level',
        'pose_mastery'
    ];

    protected $casts = [
        'last_practice_date' => 'date',
        'pose_mastery' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
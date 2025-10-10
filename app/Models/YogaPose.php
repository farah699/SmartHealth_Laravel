<?php
// app/Models/YogaPose.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YogaPose extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'pose_name',
        'correct_count',
        'total_attempts',
        'accuracy_percentage',
        'points_earned',
        'detected_at'
    ];

    protected $casts = [
        'detected_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(YogaSession::class, 'session_id');
    }
}
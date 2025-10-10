<?php
// app/Models/YogaSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YogaSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'duration',
        'total_points',
        'poses_data',
        'is_completed'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'poses_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function poses()
    {
        return $this->hasMany(YogaPose::class, 'session_id');
    }
}
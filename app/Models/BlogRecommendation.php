<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'title',
        'category',
        'content_type',
        'description',
        'target_audience',
        'difficulty_level',
        'estimated_time',
        'url',
        'email_sent',
        'email_sent_at',
         'is_new' // Ajout du champ

    ];

    protected $casts = [
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
        'is_new' => 'boolean',  // Ajout du cast

    ];

    /**
     * Relation with Blog model
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
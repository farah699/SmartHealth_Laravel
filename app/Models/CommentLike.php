<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment_id',
        'is_like', // true = like, false = dislike
    ];

    /**
     * Relation avec le modèle User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le modèle Comment
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
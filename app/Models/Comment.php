<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'blog_id',
        'parent_id'  // ✅ Ajouter parent_id
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le blog
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Relation avec le commentaire parent
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Relation avec les réponses (commentaires enfants)
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    /**
     * Relation avec les likes
     */
    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Vérifier si l'utilisateur a liké
     */
    public function isLikedBy($userId)
    {
        return $this->likes()
            ->where('user_id', $userId)
            ->where('is_like', true)
            ->exists();
    }

    /**
     * Vérifier si l'utilisateur a disliké
     */
    public function isDislikedBy($userId)
    {
        return $this->likes()
            ->where('user_id', $userId)
            ->where('is_like', false)
            ->exists();
    }

    /**
     * Compter les likes
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->where('is_like', true)->count();
    }

    /**
     * Compter les dislikes
     */
    public function getDislikesCountAttribute()
    {
        return $this->likes()->where('is_like', false)->count();
    }
}
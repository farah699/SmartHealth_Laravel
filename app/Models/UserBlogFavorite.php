<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBlogFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blog_id',
        'type',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // Types constants
    const TYPE_FAVORITE = 'favorite';
    const TYPE_READ_LATER = 'read_later';

    /**
     * Relation avec User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec Blog
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Scopes pour filtrer par type
     */
    public function scopeFavorites($query)
    {
        return $query->where('type', self::TYPE_FAVORITE);
    }

    public function scopeReadLater($query)
    {
        return $query->where('type', self::TYPE_READ_LATER);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
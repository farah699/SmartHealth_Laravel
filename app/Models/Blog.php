<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'category',
        'content',
        'user_id',
        'image_url',
        'audio_url',
        'audio_generated',
        'audio_generated_at',
        'estimated_duration'  // ✅ Ajouté
    ];

    protected $casts = [
        'audio_generated' => 'boolean',
        'audio_generated_at' => 'datetime',
    ];

    // ✅ Ajouté pour exposer les attributs calculés
    protected $appends = ['audio_full_url', 'has_audio', 'favorites_count', 'read_later_count'];

    /**
     * Relation avec le modèle User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les commentaires
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    /**
     * Nombre de commentaires (méthode correcte)
     */
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    /**
     * URL complète de l'audio - ✅ CORRIGÉ
     */
     public function getAudioFullUrlAttribute()
    {
        if ($this->has_audio) {
            return asset('storage/' . $this->audio_url);
        }
        return null;
    }

    /**
     * Vérifier si l'audio est disponible - ✅ CORRIGÉ
     */
    public function getHasAudioAttribute()
    {
               return $this->audio_url && \Storage::disk('public')->exists($this->audio_url);

    }

    /**
     * Obtenir la durée estimée de lecture - ✅ CORRIGÉ
     */
   public function getEstimatedReadingDurationAttribute()
{
    // Si la durée est stockée en DB (en secondes), la retourner
    if ($this->estimated_duration) {
        return $this->estimated_duration; // ✅ En secondes
    }
    
    // Sinon calculer
    $wordCount = str_word_count(strip_tags($this->content));
    $wordsPerMinute = 150;
    $durationInMinutes = $wordCount / $wordsPerMinute;
    return (int) ceil($durationInMinutes * 60); // ✅ Retourner en secondes
}

    /**
     * Relation avec les favoris
     */
    public function favorites()
    {
        return $this->hasMany(UserBlogFavorite::class);
    }

    /**
     * Utilisateurs qui ont mis en favori
     */
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_blog_favorites')
            ->wherePivot('type', UserBlogFavorite::TYPE_FAVORITE)
            ->withPivot(['type', 'read_at', 'created_at'])
            ->withTimestamps();
    }

    /**
     * Nombre de favoris - ✅ CORRIGÉ (éviter N+1)
     */
    public function getFavoritesCountAttribute()
    {
        // Si le count est déjà chargé via withCount
        if ($this->relationLoaded('favorites')) {
            return $this->favorites->where('type', UserBlogFavorite::TYPE_FAVORITE)->count();
        }
        
        return $this->favorites()->where('type', UserBlogFavorite::TYPE_FAVORITE)->count();
    }

    /**
     * Nombre de "lire plus tard" - ✅ CORRIGÉ (éviter N+1)
     */
    public function getReadLaterCountAttribute()
    {
        // Si le count est déjà chargé via withCount
        if ($this->relationLoaded('favorites')) {
            return $this->favorites->where('type', UserBlogFavorite::TYPE_READ_LATER)->count();
        }
        
        return $this->favorites()->where('type', UserBlogFavorite::TYPE_READ_LATER)->count();
    }

    /**
     * Relation avec la recommandation
     */
    public function recommendation()
    {
        return $this->hasOne(BlogRecommendation::class);
    }
    
    /**
     * Vérifier si le blog a une recommandation
     */
    public function hasRecommendation()
    {
        return $this->recommendation()->exists();
    }

    /**
     * ✅ NOUVEAU: Scope pour charger efficacement les relations
     */
    public function scopeWithAllRelations($query)
    {
        return $query->with([
            'user:id,name,email',
            'recommendation',
            'comments' => function($q) {
                $q->latest()->limit(5);
            }
        ])->withCount([
            'favorites as favorites_count' => function($q) {
                $q->where('type', UserBlogFavorite::TYPE_FAVORITE);
            },
            'favorites as read_later_count' => function($q) {
                $q->where('type', UserBlogFavorite::TYPE_READ_LATER);
            },
            'comments'
        ]);
    }

    /**
     * ✅ NOUVEAU: Vérifier si l'utilisateur a mis en favori
     */
    public function isFavoritedBy($userId)
    {
        return $this->favorites()
            ->where('user_id', $userId)
            ->where('type', UserBlogFavorite::TYPE_FAVORITE)
            ->exists();
    }

    /**
     * ✅ NOUVEAU: Vérifier si l'utilisateur a mis en "lire plus tard"
     */
    public function isReadLaterBy($userId)
    {
        return $this->favorites()
            ->where('user_id', $userId)
            ->where('type', UserBlogFavorite::TYPE_READ_LATER)
            ->exists();
    }
}
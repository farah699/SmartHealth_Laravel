<?php



namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserBlogFavorite;
use App\Models\Blog;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\UserProfile;
use Carbon\Carbon;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'age',
        'password',
        'role',
        'enabled',
        'phone',           // AJOUTÉ
        'address',         // AJOUTÉ
        'avatar',          // AJOUTÉ
        'birth_date',      // AJOUTÉ
        'gender',          // AJOUTÉ
        'bio',             // AJOUTÉ
        'phone',           // AJOUTÉ
        'address',         // AJOUTÉ
        'avatar',          // AJOUTÉ
        'birth_date',      // AJOUTÉ
        'gender',          // AJOUTÉ
        'bio',
        'height', 'weight', 'imc', 'imc_category', 'imc_calculated_at'             // AJOUTÉ
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<int, string>
     * @var list<int, string>
     * @var list<int, string>
     * @var list<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'enabled' => 'boolean',
            'birth_date' => 'date',  // AJOUTÉ

            'birth_date' => 'date', 
             'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'imc' => 'decimal:1',
        'imc_calculated_at' => 'datetime', // AJOUTÉ

        ];
    }
public function getImcAttribute()
{
    if ($this->weight && $this->height) {
        $heightInMeters = $this->height / 100;
        return round($this->weight / ($heightInMeters * $heightInMeters), 1);
    }
    
    return 26.1; // Valeur par défaut
}
    

    /**
     * Accessor pour l'avatar avec URL complète
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

        /**
     * Accessor pour les initiales (si pas d'avatar)
     */
    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';
        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    /**
     * Relation avec les notifications reçues
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relation avec les notifications envoyées
     */
    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id')->orderBy('created_at', 'desc');
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->where('is_read', false)->count();
    }
 /**
     * Obtenir les notifications non lues
     */
    public function getUnreadNotificationsAttribute()
    {
        return $this->notifications()
            ->where('is_read', false)
            ->with('sender')
            ->take(10)
            ->get();
    }

    


    /**
     * Roles disponibles
     */
    const ROLE_STUDENT = 'Student';
    const ROLE_TEACHER = 'Teacher';

    /**
     * Obtenir tous les rôles disponibles
     */
    public static function getRoles()
    {
        return [
            self::ROLE_STUDENT,
            self::ROLE_TEACHER,
        ];
    }

    /**
     * Vérifier si l'utilisateur est activé
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
  /**
     * Relation avec le profil utilisateur
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Obtenir ou créer le profil utilisateur - VERSION CORRIGÉE
     */
  // Créer ou récupérer le profil
    public function getOrCreateProfile(): UserProfile
    {
        return $this->profile ?? $this->profile()->create([
            'gender' => 'male',
            'activity_level' => 'sedentary',
            'goal' => 'maintain'
        ]);
    }

    /**
     * Vérifier si l'utilisateur a un profil complet
     */
    public function hasCompleteProfile(): bool
    {
        $profile = $this->profile;
        return $profile && 
               $profile->gender && 
               $this->age && 
               $profile->height && 
               $profile->weight && 
               $profile->activity_level && 
               $profile->goal;
    }


      /**
 * Relation avec les favoris
 */
  public function blogFavorites()
    {
        return $this->hasMany(UserBlogFavorite::class);
    }

/**
 * Blogs favoris
 */
   public function favoriteBlogs()
    {
        return $this->belongsToMany(Blog::class, 'user_blog_favorites')
            ->wherePivot('type', UserBlogFavorite::TYPE_FAVORITE)
            ->withPivot(['type', 'read_at', 'created_at'])
            ->withTimestamps();
    }
/**
 * Blogs à lire plus tard
 */
   public function readLaterBlogs()
    {
        return $this->belongsToMany(Blog::class, 'user_blog_favorites')
            ->wherePivot('type', UserBlogFavorite::TYPE_READ_LATER)
            ->withPivot(['type', 'read_at', 'created_at'])
            ->withTimestamps();
    }
/**
 * Vérifier si un blog est en favori
 */
    public function hasFavorite($blogId, $type = UserBlogFavorite::TYPE_FAVORITE)
    {
        return $this->blogFavorites()
            ->where('blog_id', $blogId)
            ->where('type', $type)
            ->exists();
    }

    public function activities()
{
    return $this->hasMany(Activity::class);
}

    /**
     * Obtenir les objectifs nutritionnels de l'utilisateur
     */
    public function getNutritionalGoals(): array
    {
        $profile = $this->profile;
        
        if (!$profile || !$this->hasCompleteProfile()) {
            return [
                'calories' => 2000,
                'water_ml' => 2000,
                'complete' => false
            ];
        }

        return [
            'calories' => $profile->daily_calories ?? 2000,
            'water_ml' => $profile->daily_water_ml ?? 2000,
            'complete' => true
        ];
    }

    // Calculer et sauvegarder l'IMC
    public function calculateAndSaveImc(): array
    {
        if (!$this->height || !$this->weight) {
            return [
                'success' => false,
                'message' => 'Taille et poids requis pour calculer l\'IMC'
            ];
        }
        
        // Calcul de l'IMC
        $heightInMeters = $this->height / 100;
        $imc = round($this->weight / ($heightInMeters * $heightInMeters), 1);
        
        // Déterminer la catégorie
        $category = $this->getImcCategoryFromValue($imc);
        
        // Sauvegarder dans la base de données
        $this->update([
            'imc' => $imc,
            'imc_category' => $category,
            'imc_calculated_at' => now()
        ]);

        return [
            'success' => true,
            'imc' => $imc,
            'category' => $category,
            'calculated_at' => $this->imc_calculated_at->format('Y-m-d H:i:s')
        ];

 }

  // Obtenir la catégorie IMC
    private function getImcCategoryFromValue(float $imc): string
    {
        if ($imc < 16.5) {
            return 'Dénutrition';
        } elseif ($imc < 18.5) {
            return 'Maigreur';
        } elseif ($imc < 25) {
            return 'Corpulence normale';
        } elseif ($imc < 30) {
            return 'Surpoids';
        } elseif ($imc < 35) {
            return 'Obésité modérée';
        } elseif ($imc < 40) {
            return 'Obésité sévère';
        } else {
            return 'Obésité morbide';
        }
    }

    
    // Obtenir les données IMC complètes
    public function getImcData(): ?array
    {
        if (!$this->imc) {
            return null;
        }

        $advice = $this->getImcAdvice($this->imc);
        $heightInMeters = $this->height / 100;
        $idealWeightMin = round(18.5 * $heightInMeters * $heightInMeters, 1);
        $idealWeightMax = round(24.9 * $heightInMeters * $heightInMeters, 1);

        return [
            'imc' => $this->imc,
            'category' => $this->imc_category,
            'advice' => $advice,
            'weight' => $this->weight,
            'height' => $this->height,
            'ideal_weight_min' => $idealWeightMin,
            'ideal_weight_max' => $idealWeightMax,
            'calculated_at' => $this->imc_calculated_at ? $this->imc_calculated_at->format('Y-m-d H:i:s') : null
        ];
    }


     // Obtenir les conseils IMC
    private function getImcAdvice(float $imc): array
    {
        if ($imc < 16.5) {
            return [
                'message' => 'Votre IMC indique une dénutrition. Consultez un professionnel de santé.',
                'color' => 'danger',
                'recommendations' => [
                    'Consulter un médecin rapidement',
                    'Augmenter l\'apport calorique progressivement',
                    'Privilégier les aliments riches en nutriments',
                    'Envisager un suivi nutritionnel personnalisé'
                ]
            ];
        } elseif ($imc < 18.5) {
            return [
                'message' => 'Vous êtes en dessous du poids normal.',
                'color' => 'warning',
                'recommendations' => [
                    'Augmenter progressivement les calories',
                    'Intégrer des exercices de musculation',
                    'Consulter un nutritionniste',
                    'Privilégier les collations nutritives'
                ]
                     ];
        } elseif ($imc < 25) {
            return [
                'message' => 'Félicitations ! Votre poids est dans la norme.',
                'color' => 'success',
                'recommendations' => [
                    'Maintenir une alimentation équilibrée',
                    'Pratiquer une activité physique régulière',
                    'Surveiller votre poids régulièrement',
                    'Adopter un mode de vie sain'
                ]
            ];
        } elseif ($imc < 30) {
            return [
                'message' => 'Vous êtes en surpoids. Une perte de poids serait bénéfique.',
                'color' => 'warning',
                'recommendations' => [
                    'Réduire l\'apport calorique progressivement',
                    'Augmenter l\'activité physique',
                    'Privilégier les aliments peu caloriques',
                    'Adopter de bonnes habitudes alimentaires'
                ]
            ];
               } elseif ($imc < 35) {
            return [
                'message' => 'Vous présentez une obésité modérée.',
                'color' => 'danger',
                'recommendations' => [
                    'Consulter un professionnel de santé',
                    'Suivre un programme de perte de poids structuré',
                    'Modifier durablement vos habitudes',
                    'Envisager un suivi psychologique si nécessaire'
                ]
            ];
        } elseif ($imc < 40) {
            return [
                'message' => 'Vous présentez une obésité sévère.',
                'color' => 'danger',
                'recommendations' => [
                    'Consulter un médecin spécialisé en obésité',
                    'Envisager un suivi multidisciplinaire',
                    'Considérer toutes les options thérapeutiques',
                    'Rejoindre un programme de perte de poids médicalisé'
                ]
            ];
             } else {
            return [
                'message' => 'Vous présentez une obésité morbide.',
                'color' => 'danger',
                'recommendations' => [
                    'Consulter urgentement un médecin spécialisé',
                    'Envisager une chirurgie bariatrique',
                    'Suivre un protocole médical strict',
                    'Bénéficier d\'un suivi psychologique adapté'
                ]
            ];
        }
    }


    

    
}
<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RegistrationOtp extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'registration_otps';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Vérifier si l'OTP est expiré
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Créer un nouvel OTP pour un email
     */
    public static function createForEmail($email)
    {
        // Supprimer les anciens OTP pour cet email
        self::where('email', $email)->delete();
        
        // Générer un nouveau code OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Créer le nouvel enregistrement
        return self::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
    }

    /**
     * Vérifier un OTP pour un email donné
     */
    public static function verifyOtp($email, $otp)
    {
        return self::where('email', $email)
                   ->where('otp', $otp)
                   ->where('expires_at', '>', Carbon::now())
                   ->first();
    }

    /**
     * Supprimer tous les OTP expirés
     */
    public static function cleanExpired()
    {
        return self::where('expires_at', '<', Carbon::now())->delete();
    }
}
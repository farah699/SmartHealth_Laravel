<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordOtp extends Model
{
     protected $table = 'password_otps'; // nom exact de la table
    protected $fillable = ['email', 'otp', 'expires_at']; // champs modifiables
    public $timestamps = true; // si tu veux conserver created_at / updated_at
}

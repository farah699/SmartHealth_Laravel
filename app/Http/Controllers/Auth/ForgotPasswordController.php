<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{


// Envoi de l'OTP
  public function sendOtp(Request $request)
    {
        try {
            // Validation email
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            // Générer un OTP (6 chiffres)
            $otp = rand(100000, 999999);

            // Sauvegarde en base
            PasswordOtp::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(10),
                ]
            );

            // Envoi par mail
            Mail::raw("Votre code OTP est : $otp (valide 10 minutes)", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Réinitialisation du mot de passe');
            });

            return response()->json(['message' => 'OTP envoyé à votre email']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'envoi: ' . $e->getMessage()], 500);
        }
    }

    // Vérification de l'OTP
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|string',
            ]);

            $otpRecord = PasswordOtp::where('email', $request->email)
                        ->where('otp', $request->otp)
                        ->first();

            if (!$otpRecord) {
                return response()->json(['error' => 'OTP invalide'], 400);
            }

            if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
                return response()->json(['error' => 'OTP expiré'], 400);
            }

            return response()->json(['message' => 'OTP valide, vous pouvez changer votre mot de passe']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la vérification: ' . $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $otpRecord = PasswordOtp::where('email', $request->email)
                        ->where('otp', $request->otp)
                        ->first();

            if (!$otpRecord || Carbon::now()->greaterThan($otpRecord->expires_at)) {
                return response()->json(['error' => 'OTP invalide ou expiré'], 400);
            }

            // Mettre à jour le mot de passe
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Supprimer l'OTP après usage
            $otpRecord->delete();

            return response()->json(['message' => 'Mot de passe réinitialisé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la réinitialisation: ' . $e->getMessage()], 500);
        }
    }



public function showEmailForm()
{
    return view('auth-reset-password');
}

// Afficher formulaire OTP
public function showOtpForm()
{
    return view('auth-two-step-verify');
}

// Afficher formulaire reset password
public function showResetForm()
{
    return view('auth-create-password');
}


}

<?php


namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\RegistrationOtp; // Ajouter l'import du modèle
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegisterForm()
    {
        return view('auth-signup');
    }

    /**
     * Traiter l'inscription et envoyer OTP
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Student,Teacher',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // ÉTAPE 1: Créer l'utilisateur avec enabled = false
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'enabled' => false, // Sera activé après vérification OTP
        ]);

        // ÉTAPE 2: Générer et envoyer l'OTP
        $this->sendRegistrationOtp($request->email);

        // ÉTAPE 3: Rediriger vers la page de vérification OTP
        return redirect()->route('register.verify.otp', ['email' => $request->email])
            ->with('success', 'Account created! A verification code has been sent to your email.
');
    }

    /**
     * Afficher le formulaire de vérification OTP
     */
    public function showVerifyOtpForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('register')->withErrors(['email' => 'Session expirée. Veuillez vous réinscrire.']);
        }

        return view('auth-verify-registration', compact('email'));
    }

    /**
     * Vérifier l'OTP de registration
     */
    public function verifyRegistrationOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // Vérifier l'OTP avec le modèle
        $otpRecord = RegistrationOtp::verifyOtp($request->email, $request->otp);

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or expired OTP code.
'
            ], 400);
        }

        // Activer l'utilisateur
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update(['enabled' => true]);
            
            // Supprimer l'OTP utilisé
            $otpRecord->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Your account has been successfully verified! You can now log in.
',
                'redirect_url' => route('login')
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Error activating account.'
        ], 500);
    }

    /**
     * Renvoyer l'OTP
     */
    public function resendRegistrationOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $this->sendRegistrationOtp($request->email);
        
        return response()->json([
            'success' => true,
            'message' => 'A new code has been sent to your email.'
        ]);
    }

    /**
     * Envoyer l'OTP par email
     */
private function sendRegistrationOtp($email)
{
    // Créer un nouvel OTP avec le modèle
    $otpRecord = RegistrationOtp::createForEmail($email);

    // TEMPORAIRE : Toujours afficher l'OTP dans les logs pour debug
    Log::info('=== OTP GÉNÉRÉ ===');
    Log::info('Email: ' . $email);
    Log::info('Code OTP: ' . $otpRecord->otp);
    Log::info('Expire à: ' . $otpRecord->expires_at);
    Log::info('=================');

    // Envoyer un email simple sans template
    try {
        Mail::raw("Your  verification code is: {$otpRecord->otp}\n\nThis code expires in 10 minutes.
\n\nPlease do not reply to this email.
", function ($message) use ($email, $otpRecord) {

            $message->to($email)
                    ->subject(' Verification Code - ' . $otpRecord->otp)
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });

        Log::info('Email sent successfully to: ' . $email);
    } catch (\Exception $e) {
        Log::error('Error sending OTP email: ' . $e->getMessage());
    }
}
}
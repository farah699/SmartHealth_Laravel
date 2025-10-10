<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Afficher le formulaire de login
     * Utilise la vue existante auth-signin.blade.php
     */
    public function showLoginForm()
    { 
        // Si déjà connecté, rediriger vers dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth-signin');
    }

    /**
     * Traiter la tentative de connexion
     * Étapes : 1) Valider, 2) Vérifier utilisateur, 3) Vérifier si activé, 4) Comparer mot de passe, 5) Créer session
     */
    public function login(Request $request)
    {
        // ÉTAPE 1 : Validation des données d'entrée
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez entrer un email valide.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        // ÉTAPE 2 : Vérifier que l'utilisateur existe
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'Aucun utilisateur trouvé avec cet email.',
            ])->onlyInput('email');
        }

        // ÉTAPE 3 : Vérifier si le compte est activé (NOUVEAU)
        if (!$user->enabled) {
            return back()->withErrors([
                'email' => 'Your account is not yet activated. Please contact the administrator to activate your account.',
            ])->onlyInput('email');
        }

        // ÉTAPE 4 : Comparer le mot de passe saisi avec celui hashé en base
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Le mot de passe est incorrect.',
            ])->onlyInput('email');
        }

        // ÉTAPE 5 : Créer une session utilisateur
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Régénérer l'ID de session pour la sécurité
            $request->session()->regenerate();
            
            // ÉTAPE 6 : Rediriger vers le dashboard principal
            return redirect()->route('dashboard')->with('success', 'Bienvenue dans SmartHealth, ' . $user->name . ' !');
        }

        // Si Auth::attempt échoue
        return back()->withErrors([
            'email' => 'Une erreur est survenue lors de la connexion.',
        ])->onlyInput('email');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Vous êtes déconnecté avec succès.');
    }

    /**
     * Méthode alternative avec gestion détaillée
     */
    public function loginDetailed(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Vérification manuelle étape par étape
        $user = User::where('email', $validated['email'])->first();

        // Vérifier existence utilisateur
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé',
                'error_code' => 'USER_NOT_FOUND'
            ], 401);
        }

        // Vérifier si le compte est activé (NOUVEAU)
        if (!$user->enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte n\'est pas encore activé. Contactez l\'administrateur.',
                'error_code' => 'ACCOUNT_NOT_ENABLED'
            ], 403);
        }

        // Vérifier mot de passe
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect',
                'error_code' => 'INVALID_PASSWORD'
            ], 401);
        }

        // Créer session
        Auth::login($user, $request->has('remember'));

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'redirect_url' => '/dashboard'
        ], 200);
    }
}
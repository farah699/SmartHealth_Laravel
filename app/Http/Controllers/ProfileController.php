<?php
// filepath: c:\Users\Lenovo\Desktop\laravel\SmartHealth_Laravel\app\Http\Controllers\ProfileController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur
     */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Plus besoin de récupérer les blogs
        return view('pages-profile', compact('user'));
    }

    /**
     * Afficher le formulaire de modification du profil
     */
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('pages-profile-edit', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil (SANS avatar)
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
        ]);

        // Mettre à jour les informations (SANS avatar)
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'bio' => $request->bio,
        ]);

        return redirect()->route('profile.show')->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Mettre à jour seulement l'avatar
     */
    public function updateAvatar(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Supprimer l'ancien avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Sauvegarder le nouvel avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $avatarPath]);

        return redirect()->route('profile.show')->with('success', 'Photo de profil mise à jour avec succès !');
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Vérifier l'ancien mot de passe
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Mettre à jour le mot de passe
        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('profile.show')->with('success', 'Mot de passe modifié avec succès !');
    }

    /**
     * Supprimer l'avatar
     */
    public function deleteAvatar()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Photo de profil supprimée avec succès !');
    }
}
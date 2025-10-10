<?php
// filepath: app/Http/Controllers/ImcController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImcController extends Controller
{
    /**
     * Afficher la page IMC
     */
    public function index()
    {
        $userImcData = null;
        $userProfileData = null;
        
        if (Auth::check()) {
            $user = Auth::user();
            
            // Préparer les données utilisateur
            $userProfileData = [
                'weight' => $user->weight,
                'height' => $user->height,
                'age' => $user->age ?? 'N/A',
                'gender' => optional($user->profile)->gender ?? 'not_specified',
                'activity_level' => optional($user->profile)->activity_level_label ?? 'Non défini',
                'goal' => optional($user->profile)->goal_label ?? 'Non défini',
                'bmr' => optional($user->profile)->bmr ?? 0,
                'tdee' => optional($user->profile)->tdee ?? 0,
                'daily_calories' => optional($user->profile)->daily_calories ?? 0,
                'daily_water_ml' => optional($user->profile)->daily_water_ml ?? 0,
            ];

            // Récupérer l'IMC depuis la table users
            if ($user->imc) {
                $userImcData = $user->getImcData();
            }
        }
        
        return view('activities.imc', compact('userImcData', 'userProfileData'));
    }

    /**
     * Calculer l'IMC simple
     */
    public function calculate(Request $request)
    {
        try {
            $request->validate([
                'weight' => 'required|numeric|min:20|max:300',
                'height' => 'required|numeric|min:100|max:250'
            ]);

            $weight = $request->weight;
            $height = $request->height;
            $heightInMeters = $height / 100;
            $imc = round($weight / ($heightInMeters * $heightInMeters), 1);
            
            // Déterminer la catégorie
            $category = $this->getImcCategory($imc);
            $advice = $this->getImcAdvice($imc);
            
            $idealWeightMin = round(18.5 * $heightInMeters * $heightInMeters, 1);
            $idealWeightMax = round(24.9 * $heightInMeters * $heightInMeters, 1);
            
            return response()->json([
                'imc' => $imc,
                'category' => $category,
                'advice' => $advice,
                'weight' => $weight,
                'height' => $height,
                'ideal_weight_min' => $idealWeightMin,
                'ideal_weight_max' => $idealWeightMax,
                'calculated_at' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du calcul: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Sauvegarder l'IMC dans la table users
     */
    public function saveToProfile(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        try {
            $request->validate([
                'weight' => 'required|numeric|min:20|max:300',
                'height' => 'required|numeric|min:100|max:250'
            ]);

            $user = Auth::user();

            // Mettre à jour poids et taille dans la table users
            $user->weight = $request->weight;
            $user->height = $request->height;
            $user->save();

            // Calculer et sauvegarder l'IMC
            $imcResult = $user->calculateAndSaveImc();
            
            if (!$imcResult['success']) {
                return response()->json(['error' => $imcResult['message']], 400);
            }

            // Récupérer les données IMC complètes
            $imcData = $user->getImcData();

            return response()->json([
                'success' => true,
                'message' => 'Profil et IMC mis à jour avec succès',
                'imc_data' => $imcData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtenir l'IMC d'un utilisateur
     */
    public function getUserImc($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'imc_data' => $user->getImcData()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }
    }

    /**
     * Obtenir l'IMC de tous les utilisateurs
     */
    public function getAllUsersImc()
    {
        try {
            $users = User::whereNotNull('imc')->get();
            $results = [];
            
            foreach ($users as $user) {
                $imcData = $user->getImcData();
                if ($imcData) {
                    $results[] = [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'imc_data' => $imcData
                    ];
                }
            }
            
            return response()->json([
                'total_users' => count($results),
                'users_with_imc' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des données'], 500);
        }
    }

    /**
     * Méthodes privées pour le calcul IMC
     */
    private function getImcCategory(float $imc): string
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
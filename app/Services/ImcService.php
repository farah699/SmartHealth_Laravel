<?php
// filepath: app/Services/ImcService.php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;

class ImcService
{
    /**
     * Calculer l'IMC pour un utilisateur avec son profil
     */
    public function calculateImc(User $user): ?array
    {
        $profile = $user->profile;
        
        if (!$profile || !$profile->height || !$profile->weight) {
            return null;
        }

        return $this->calculateSimpleImc($profile->weight, $profile->height);
    }

    /**
     * Calculer l'IMC simple avec poids et taille
     */
    public function calculateSimpleImc(float $weight, float $height): array
    {
        // Convertir la taille en mètres
        $heightInMeters = $height / 100;
        
        // Calculer l'IMC
        $imc = round($weight / ($heightInMeters * $heightInMeters), 1);
        
        // Déterminer la catégorie et les conseils
        $category = $this->getImcCategory($imc);
        $advice = $this->getImcAdvice($imc);
        
        // Calculer le poids idéal
        $idealWeightMin = round(18.5 * $heightInMeters * $heightInMeters, 1);
        $idealWeightMax = round(24.9 * $heightInMeters * $heightInMeters, 1);
        
        return [
            'imc' => $imc,
            'category' => $category,
            'advice' => $advice,
            'weight' => $weight,
            'height' => $height,
            'ideal_weight_min' => $idealWeightMin,
            'ideal_weight_max' => $idealWeightMax,
            'calculated_at' => now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Déterminer la catégorie IMC
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

    /**
     * Obtenir les conseils selon l'IMC
     */
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

    /**
     * Calculer l'IMC pour tous les utilisateurs
     */
    public function calculateImcForAllUsers(): array
    {
        $users = User::with('profile')
            ->whereHas('profile', function($query) {
                $query->whereNotNull('height')
                      ->whereNotNull('weight')
                      ->where('height', '>', 0)
                      ->where('weight', '>', 0);
            })
            ->get();

        $results = [];
        
        foreach ($users as $user) {
            $imcData = $this->calculateImc($user);
            
            if ($imcData) {
                $results[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'imc_data' => $imcData
                ];
            }
        }

        return $results;
    }
}
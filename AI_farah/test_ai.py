import sys
import os
from exercise_recommendation_ai import ExerciseRecommendationAI

def test_ai_model():
    """Test complet du modèle IA"""
    
    print("=" * 60)
    print("SYSTÈME DE TEST DU MODÈLE IA - RECOMMANDATIONS D'EXERCICES")
    print("=" * 60)
    
    try:
        # 1. Test de chargement des datasets
        print("\n1. TEST DE CHARGEMENT DES DATASETS")
        print("-" * 30)
        
        ai = ExerciseRecommendationAI()
        
        if not ai.load_datasets_from_files():
            print("❌ Échec du chargement des datasets")
            return False
        
        # 2. Test d'entraînement du modèle
        print("\n2. ENTRAÎNEMENT DU MODÈLE")
        print("-" * 30)
        
        performance = ai.train_model_from_files()
        
        if not performance:
            print("❌ Échec de l'entraînement du modèle")
            return False
        
        print("✅ Modèle entraîné avec succès!")
        print(f"   - MSE: {performance['mse']:.2f}")
        print(f"   - R² Score: {performance['r2']:.3f}")
        
        # Évaluation de la performance
        if performance['r2'] > 0.7:
            print("🎉 Excellente performance du modèle!")
        elif performance['r2'] > 0.5:
            print("✅ Bonne performance du modèle")
        else:
            print("⚠️ Performance moyenne du modèle")
        
        # 3. Test de génération de recommandations
        print("\n3. TEST DE GÉNÉRATION DE RECOMMANDATIONS")
        print("-" * 45)
        
        # Profils de test variés
        test_profiles = [
            {
                'name': 'Débutant jeune',
                'profile': {
                    'age': 25,
                    'imc': 22.0,
                    'fitness_level': 'beginner',
                    'activity_frequency': 2,
                    'experience_months': 1,
                    'has_medical_constraints': False
                }
            },
            {
                'name': 'Intermédiaire actif',
                'profile': {
                    'age': 35,
                    'imc': 24.5,
                    'fitness_level': 'intermediate',
                    'activity_frequency': 4,
                    'experience_months': 12,
                    'has_medical_constraints': False
                }
            },
            {
                'name': 'Senior avec contraintes',
                'profile': {
                    'age': 55,
                    'imc': 27.0,
                    'fitness_level': 'beginner',
                    'activity_frequency': 2,
                    'experience_months': 3,
                    'has_medical_constraints': True
                }
            },
            {
                'name': 'Sportif avancé',
                'profile': {
                    'age': 28,
                    'imc': 21.5,
                    'fitness_level': 'advanced',
                    'activity_frequency': 6,
                    'experience_months': 36,
                    'has_medical_constraints': False
                }
            }
        ]
        
        all_tests_passed = True
        
        for i, test_case in enumerate(test_profiles, 1):
            print(f"\n🧪 Test {i}: {test_case['name']}")
            profile = test_case['profile']
            print(f"   👤 Âge: {profile['age']}, IMC: {profile['imc']}, Niveau: {profile['fitness_level']}")
            
            try:
                recommendations = ai.get_recommendations(profile, limit=5)
                
                if recommendations and len(recommendations) > 0:
                    print(f"   ✅ {len(recommendations)} recommandations générées")
                    
                    # Afficher le top 3
                    print("   🏆 Top 3 des recommandations:")
                    for j, rec in enumerate(recommendations[:3], 1):
                        print(f"      {j}. {rec['exercise_name']} (Score: {rec['predicted_score']:.1f}%)")
                    
                    # Vérifications de cohérence
                    scores = [r['predicted_score'] for r in recommendations]
                    if all(scores[i] >= scores[i+1] for i in range(len(scores)-1)):
                        print("   ✅ Recommandations bien triées par score")
                    else:
                        print("   ⚠️ Problème de tri des recommandations")
                        all_tests_passed = False
                    
                    # Vérifier que les scores sont dans la plage attendue
                    if all(0 <= score <= 100 for score in scores):
                        print("   ✅ Scores dans la plage valide (0-100)")
                    else:
                        print("   ❌ Scores hors de la plage valide")
                        all_tests_passed = False
                    
                else:
                    print("   ❌ Aucune recommandation générée")
                    all_tests_passed = False
                    
            except Exception as e:
                print(f"   ❌ Erreur lors de la génération: {e}")
                all_tests_passed = False
        
        # 4. Test de sauvegarde/chargement du modèle
        print("\n4. TEST DE PERSISTANCE DU MODÈLE")
        print("-" * 33)
        
        # Test de sauvegarde
        if ai.save_model():
            print("✅ Sauvegarde du modèle réussie")
        else:
            print("❌ Échec de la sauvegarde")
            all_tests_passed = False
        
        # Test de chargement
        new_ai = ExerciseRecommendationAI()
        if new_ai.load_model():
            print("✅ Chargement du modèle réussi")
            
            # Test rapide du modèle chargé
            test_profile = {
                'age': 30,
                'imc': 25.0,
                'fitness_level': 'intermediate',
                'activity_frequency': 3,
                'experience_months': 6,
                'has_medical_constraints': False
            }
            
            loaded_recommendations = new_ai.get_recommendations(test_profile, limit=3)
            if loaded_recommendations:
                print("✅ Modèle chargé fonctionne correctement")
            else:
                print("❌ Problème avec le modèle chargé")
                all_tests_passed = False
        else:
            print("❌ Échec du chargement")
            all_tests_passed = False
        
        # 5. Résumé final
        print("\n" + "=" * 60)
        print("RÉSUMÉ DES TESTS")
        print("=" * 60)
        
        if all_tests_passed:
            print("🎉 TOUS LES TESTS SONT PASSÉS AVEC SUCCÈS!")
            print("✅ Le modèle IA est prêt pour la production")
            print(f"📊 Performance globale: R² = {performance['r2']:.3f}")
            
            print("\n💡 PROCHAINES ÉTAPES:")
            print("1. Testez l'intégration Laravel:")
            print("   http://127.0.0.1:8000/exercise/recommendations")
            print("2. Cliquez sur 'IA en cours...' pour voir les recommandations")
            print("3. Vérifiez les logs Laravel si nécessaire")
            
        else:
            print("⚠️ CERTAINS TESTS ONT ÉCHOUÉ")
            print("🔧 Vérifiez les erreurs ci-dessus et corrigez avant la production")
        
        return all_tests_passed
        
    except Exception as e:
        print(f"\n💥 ERREUR CRITIQUE LORS DES TESTS: {e}")
        import traceback
        traceback.print_exc()
        return False

def test_specific_user_profile():
    """Test avec un profil utilisateur spécifique (comme l'utilisateur connecté)"""
    
    print("\n" + "🎯" * 20)
    print("TEST AVEC PROFIL UTILISATEUR SPÉCIFIQUE")
    print("🎯" * 20)
    
    # Profil basé sur l'utilisateur connecté de votre capture d'écran
    user_profile = {
        'age': 28,
        'imc': 26.1,
        'fitness_level': 'beginner',  # Débutant comme affiché
        'activity_frequency': 1,      # 1 activité comme affiché
        'experience_months': 2,
        'has_medical_constraints': False
    }
    
    print(f"👤 PROFIL UTILISATEUR:")
    print(f"   🎂 Âge: {user_profile['age']} ans")
    print(f"   ⚖️ IMC: {user_profile['imc']}")
    print(f"   🏃 Niveau: {user_profile['fitness_level']}")
    print(f"   📅 Fréquence: {user_profile['activity_frequency']} activité(s)/semaine")
    
    ai = ExerciseRecommendationAI()
    
    # Charger le modèle pré-entraîné
    if not ai.load_model():
        print("❌ Impossible de charger le modèle. Assurez-vous qu'il est entraîné.")
        return False
    
    print("\n🤖 Génération des recommandations personnalisées...")
    recommendations = ai.get_recommendations(user_profile, limit=10)
    
    if recommendations:
        print(f"\n🏆 TOP {len(recommendations)} RECOMMANDATIONS POUR VOUS:")
        print("=" * 50)
        
        for i, rec in enumerate(recommendations, 1):
            # Émojis selon le type
            emoji_map = {
                'cardio': '❤️',
                'strength': '💪',
                'flexibility': '🧘',
                'balance': '⚖️'
            }
            emoji = emoji_map.get(rec['exercise_type'], '🏃')
            
            # Badge de qualité selon le score
            score = rec['predicted_score']
            if score >= 85:
                badge = "🥇 EXCELLENT"
            elif score >= 75:
                badge = "🥈 TRÈS BON"
            elif score >= 65:
                badge = "🥉 BON"
            else:
                badge = "✅ CORRECT"
            
            print(f"\n{i:2d}. {emoji} {rec['exercise_name']}")
            print(f"     {badge} - Score IA: {score:.1f}%")
            print(f"     ⏱️ Durée recommandée: {rec['recommended_duration']} minutes")
            print(f"     📊 Difficulté: {rec['difficulty_level']}/10")
            print(f"     🔥 Calories/minute: {rec['calories_per_minute']}")
            print(f"     🏷️ Catégorie: {rec['exercise_category']}")
            print(f"     💡 Pourquoi: {rec['recommendation_reason']}")
        
        print(f"\n📈 ANALYSE DE VOS RECOMMANDATIONS:")
        print("-" * 35)
        
        # Analyser les types d'exercices recommandés
        types_count = {}
        for rec in recommendations:
            ex_type = rec['exercise_type']
            types_count[ex_type] = types_count.get(ex_type, 0) + 1
        
        for ex_type, count in types_count.items():
            emoji = emoji_map.get(ex_type, '🏃')
            percentage = (count / len(recommendations)) * 100
            print(f"{emoji} {ex_type.capitalize()}: {count} exercices ({percentage:.1f}%)")
        
        avg_score = sum(r['predicted_score'] for r in recommendations) / len(recommendations)
        avg_duration = sum(r['recommended_duration'] for r in recommendations) / len(recommendations)
        
        print(f"\n📊 Score moyen de compatibilité: {avg_score:.1f}%")
        print(f"⏱️ Durée moyenne recommandée: {avg_duration:.1f} minutes")
        
        return True
    else:
        print("❌ Aucune recommandation générée pour ce profil")
        return False

if __name__ == "__main__":
    print("🚀 LANCEMENT DES TESTS DU MODÈLE IA")
    
    # Test général du modèle
    success = test_ai_model()
    
    if success:
        # Test avec profil utilisateur spécifique
        test_specific_user_profile()
    
    print(f"\n{'='*60}")
    print("FIN DES TESTS")
    print(f"{'='*60}")
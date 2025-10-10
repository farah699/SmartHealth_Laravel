import os
import sys
import pandas as pd
import numpy as np
import json
import traceback
from datetime import datetime

def test_datasets_existence():
    """Test 1: Vérifier l'existence des datasets"""
    print("🔍 TEST 1: Vérification des datasets")
    print("-" * 40)
    
    datasets_dir = 'datasets'
    required_files = [
        'exercises_catalog.csv',
        'training_data.csv',
        'users_profiles.csv'  # ⚠️ Nom actuel généré par votre script
    ]
    
    results = {}
    for filename in required_files:
        filepath = os.path.join(datasets_dir, filename)
        if os.path.exists(filepath):
            size_mb = os.path.getsize(filepath) / 1024 / 1024
            try:
                df = pd.read_csv(filepath)
                results[filename] = {
                    'exists': True,
                    'size_mb': round(size_mb, 2),
                    'rows': len(df),
                    'columns': len(df.columns)
                }
                print(f"✅ {filename}: {len(df):,} lignes ({size_mb:.1f} MB)")
            except Exception as e:
                results[filename] = {'exists': True, 'error': str(e)}
                print(f"❌ {filename}: Erreur lecture - {e}")
        else:
            results[filename] = {'exists': False}
            print(f"❌ {filename}: Fichier manquant")
    
    return results

def test_model_loading():
    """Test 2: Chargement du modèle IA"""
    print("\n🤖 TEST 2: Chargement du modèle IA")
    print("-" * 40)
    
    try:
        from exercise_recommendation_ai import ExerciseRecommendationAI
        
        # Initialiser le modèle
        ai = ExerciseRecommendationAI()
        print("✅ Classe ExerciseRecommendationAI importée")
        
        # Tenter de charger le modèle pré-entraîné
        model_loaded = ai.load_model()
        
        if model_loaded:
            print("✅ Modèle pré-entraîné chargé avec succès")
            return {'status': 'loaded', 'ai_instance': ai}
        else:
            print("⚠️  Modèle non trouvé - tentative d'entraînement...")
            
            # Tenter d'entraîner le modèle
            try:
                training_success = ai.train_model()
                if training_success:
                    print("✅ Modèle entraîné avec succès")
                    return {'status': 'trained', 'ai_instance': ai}
                else:
                    print("❌ Échec de l'entraînement")
                    return {'status': 'failed', 'error': 'Training failed'}
            except Exception as e:
                print(f"❌ Erreur durant l'entraînement: {e}")
                return {'status': 'error', 'error': str(e)}
                
    except ImportError as e:
        print(f"❌ Erreur d'import: {e}")
        return {'status': 'import_error', 'error': str(e)}
    except Exception as e:
        print(f"❌ Erreur inattendue: {e}")
        return {'status': 'error', 'error': str(e)}

def test_predictions():
    """Test 3: Test des prédictions"""
    print("\n🎯 TEST 3: Test des prédictions")
    print("-" * 40)
    
    try:
        from exercise_recommendation_ai import ExerciseRecommendationAI
        
        ai = ExerciseRecommendationAI()
        
        # Charger ou entraîner le modèle
        if not ai.load_model():
            print("📚 Entraînement du modèle...")
            if not ai.train_model():
                return {'status': 'failed', 'error': 'Cannot train model'}
        
        # Profils de test diversifiés
        test_profiles = [
            {
                'name': 'Jeune sportif',
                'age': 25,
                'imc': 22.0,
                'fitness_level': 'advanced',
                'activity_frequency': 8,
                'has_medical_constraints': False,
                'experience_months': 36
            },
            {
                'name': 'Débutant senior',
                'age': 65,
                'imc': 28.5,
                'fitness_level': 'beginner',
                'activity_frequency': 2,
                'has_medical_constraints': True,
                'experience_months': 2
            },
            {
                'name': 'Adulte intermédiaire',
                'age': 35,
                'imc': 24.0,
                'fitness_level': 'intermediate',
                'activity_frequency': 4,
                'has_medical_constraints': False,
                'experience_months': 12
            },
            {
                'name': 'Personne en surpoids',
                'age': 45,
                'imc': 32.0,
                'fitness_level': 'beginner',
                'activity_frequency': 1,
                'has_medical_constraints': False,
                'experience_months': 0
            }
        ]
        
        results = []
        
        for profile in test_profiles:
            print(f"\n👤 Test: {profile['name']}")
            try:
                recommendations = ai.get_recommendations(profile, limit=5)
                
                if recommendations and len(recommendations) > 0:
                    print(f"✅ {len(recommendations)} recommandations générées")
                    
                    # Afficher les 3 meilleures recommandations
                    for i, rec in enumerate(recommendations[:3], 1):
                        print(f"   {i}. {rec.get('exercise_name', 'N/A')} "
                              f"(Score: {rec.get('recommendation_score', 0):.1f})")
                    
                    results.append({
                        'profile': profile['name'],
                        'status': 'success',
                        'count': len(recommendations),
                        'avg_score': np.mean([r.get('recommendation_score', 0) for r in recommendations])
                    })
                else:
                    print("❌ Aucune recommandation générée")
                    results.append({
                        'profile': profile['name'],
                        'status': 'no_recommendations'
                    })
                    
            except Exception as e:
                print(f"❌ Erreur: {e}")
                results.append({
                    'profile': profile['name'],
                    'status': 'error',
                    'error': str(e)
                })
        
        return {'status': 'completed', 'results': results}
        
    except Exception as e:
        print(f"❌ Erreur globale: {e}")
        traceback.print_exc()
        return {'status': 'error', 'error': str(e)}

def test_laravel_integration():
    """Test 4: Test d'intégration Laravel"""
    print("\n🔗 TEST 4: Test d'intégration Laravel")
    print("-" * 40)
    
    # Créer un fichier temporaire de profil utilisateur
    test_profile = {
        'age': 30,
        'imc': 23.5,
        'fitness_level': 'intermediate',
        'activity_frequency': 5,
        'has_medical_constraints': False,
        'experience_months': 18
    }
    
    temp_file = 'temp_profile.json'
    
    try:
        # Sauvegarder le profil de test
        with open(temp_file, 'w', encoding='utf-8') as f:
            json.dump(test_profile, f)
        
        print("✅ Fichier profil temporaire créé")
        
        # Tester le script de prédiction
        import subprocess
        
        cmd = [sys.executable, 'exercise_recommendation_predict.py', temp_file]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
            
            if result.returncode == 0:
                # Tenter de parser la sortie JSON
                try:
                    output = json.loads(result.stdout)
                    if 'recommendations' in output:
                        print(f"✅ Script Laravel fonctionnel - {len(output['recommendations'])} recommandations")
                        return {'status': 'success', 'recommendations_count': len(output['recommendations'])}
                    else:
                        print("⚠️  Script fonctionne mais format de sortie inattendu")
                        return {'status': 'warning', 'output': result.stdout}
                except json.JSONDecodeError:
                    print("⚠️  Script fonctionne mais sortie non-JSON")
                    return {'status': 'warning', 'raw_output': result.stdout}
            else:
                print(f"❌ Script échoué (code: {result.returncode})")
                print(f"Erreur: {result.stderr}")
                return {'status': 'failed', 'error': result.stderr}
                
        except subprocess.TimeoutExpired:
            print("❌ Timeout du script")
            return {'status': 'timeout'}
        except FileNotFoundError:
            print("❌ Script exercise_recommendation_predict.py non trouvé")
            return {'status': 'script_not_found'}
        
    except Exception as e:
        print(f"❌ Erreur: {e}")
        return {'status': 'error', 'error': str(e)}
    finally:
        # Nettoyer le fichier temporaire
        if os.path.exists(temp_file):
            os.remove(temp_file)

def test_performance():
    """Test 5: Test de performance"""
    print("\n⚡ TEST 5: Test de performance")
    print("-" * 40)
    
    try:
        from exercise_recommendation_ai import ExerciseRecommendationAI
        import time
        
        ai = ExerciseRecommendationAI()
        
        # Charger le modèle
        start_time = time.time()
        model_loaded = ai.load_model()
        load_time = time.time() - start_time
        
        if not model_loaded:
            print("📚 Entraînement nécessaire...")
            train_start = time.time()
            ai.train_model()
            train_time = time.time() - train_start
            print(f"⏱️  Temps d'entraînement: {train_time:.2f}s")
        
        print(f"⏱️  Temps de chargement: {load_time:.3f}s")
        
        # Test de vitesse de prédiction
        test_profile = {
            'age': 30,
            'imc': 24.0,
            'fitness_level': 'intermediate',
            'activity_frequency': 4,
            'has_medical_constraints': False,
            'experience_months': 12
        }
        
        # Test multiple pour mesurer la performance moyenne
        times = []
        for i in range(10):
            start = time.time()
            recommendations = ai.get_recommendations(test_profile, limit=10)
            times.append(time.time() - start)
        
        avg_time = np.mean(times)
        min_time = np.min(times)
        max_time = np.max(times)
        
        print(f"⚡ Temps de prédiction moyen: {avg_time:.3f}s")
        print(f"⚡ Temps min/max: {min_time:.3f}s / {max_time:.3f}s")
        
        # Évaluation de la performance
        if avg_time < 0.1:
            performance_grade = "🏆 EXCELLENT"
        elif avg_time < 0.5:
            performance_grade = "✅ BON"
        elif avg_time < 1.0:
            performance_grade = "⚠️  ACCEPTABLE"
        else:
            performance_grade = "❌ LENT"
        
        print(f"📊 Performance: {performance_grade}")
        
        return {
            'status': 'success',
            'load_time': load_time,
            'avg_prediction_time': avg_time,
            'min_time': min_time,
            'max_time': max_time,
            'grade': performance_grade
        }
        
    except Exception as e:
        print(f"❌ Erreur: {e}")
        return {'status': 'error', 'error': str(e)}

def run_complete_test_suite():
    """Exécute tous les tests"""
    print("🚀 SUITE DE TESTS COMPLÈTE - MODÈLE IA SMARTHEALTH")
    print("=" * 60)
    print(f"📅 Date: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 60)
    
    results = {}
    
    # Test 1: Datasets
    results['datasets'] = test_datasets_existence()
    
    # Test 2: Modèle
    results['model'] = test_model_loading()
    
    # Test 3: Prédictions (seulement si le modèle est OK)
    if results['model']['status'] in ['loaded', 'trained']:
        results['predictions'] = test_predictions()
    else:
        print("\n⏭️  SKIP TEST 3: Modèle non disponible")
        results['predictions'] = {'status': 'skipped', 'reason': 'Model not available'}
    
    # Test 4: Intégration Laravel
    results['laravel'] = test_laravel_integration()
    
    # Test 5: Performance (seulement si le modèle fonctionne)
    if results['model']['status'] in ['loaded', 'trained']:
        results['performance'] = test_performance()
    else:
        print("\n⏭️  SKIP TEST 5: Modèle non disponible")
        results['performance'] = {'status': 'skipped', 'reason': 'Model not available'}
    
    # Résumé final
    print("\n" + "=" * 60)
    print("📊 RÉSUMÉ DES TESTS")
    print("=" * 60)
    
    total_tests = 5
    passed_tests = 0
    
    # Évaluation de chaque test
    test_results = [
        ("Datasets", results['datasets']),
        ("Modèle IA", results['model']),
        ("Prédictions", results['predictions']),
        ("Intégration Laravel", results['laravel']),
        ("Performance", results['performance'])
    ]
    
    for test_name, test_result in test_results:
        if isinstance(test_result, dict):
            status = test_result.get('status', 'unknown')
            if status in ['success', 'loaded', 'trained', 'completed']:
                print(f"✅ {test_name}: RÉUSSI")
                passed_tests += 1
            elif status == 'skipped':
                print(f"⏭️  {test_name}: IGNORÉ")
            else:
                print(f"❌ {test_name}: ÉCHEC")
        else:
            # Pour le test datasets qui retourne un dict différent
            if test_name == "Datasets":
                all_exist = all(info.get('exists', False) for info in test_result.values())
                if all_exist:
                    print(f"✅ {test_name}: RÉUSSI")
                    passed_tests += 1
                else:
                    print(f"❌ {test_name}: ÉCHEC")
    
    print(f"\n🎯 Score final: {passed_tests}/{total_tests} tests réussis")
    
    if passed_tests == total_tests:
        print("🎉 TOUS LES TESTS SONT RÉUSSIS ! Votre modèle IA est opérationnel.")
    elif passed_tests >= 3:
        print("✅ La plupart des tests sont réussis. Quelques ajustements peuvent être nécessaires.")
    else:
        print("❌ Plusieurs problèmes détectés. Vérifiez la configuration.")
    
    # Recommandations
    print("\n💡 RECOMMANDATIONS:")
    if results['datasets'].get('users_profiles.csv', {}).get('exists', False):
        if not os.path.exists('datasets/usersprofiles.csv'):
            print("⚠️  Renommez 'users_profiles.csv' en 'usersprofiles.csv' pour la compatibilité")
    
    if results['model']['status'] == 'import_error':
        print("🔧 Vérifiez que exercise_recommendation_ai.py est présent et correct")
    
    if results['laravel']['status'] == 'script_not_found':
        print("🔧 Créez/corrigez le script exercise_recommendation_predict.py")
    
    return results

if __name__ == "__main__":
    results = run_complete_test_suite()
    
    # Optionnel: sauvegarder les résultats
    with open('test_results.json', 'w', encoding='utf-8') as f:
        json.dump(results, f, indent=2, ensure_ascii=False, default=str)
    print(f"\n📄 Résultats sauvegardés dans 'test_results.json'")
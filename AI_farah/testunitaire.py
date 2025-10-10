import subprocess
import sys
import os

def run_all_tests():
    """Exécute tous les tests du système IA"""
    print("🚀 EXÉCUTION DE TOUS LES TESTS")
    print("=" * 50)
    
    tests = [
        {
            'name': 'Test du modèle IA',
            'file': 'test_ai.py',
            'description': 'Test complet d\'entraînement et prédictions'
        },
        {
            'name': 'Test de prédiction simple',
            'file': 'quick_test.py',
            'description': 'Test rapide de prédiction'
        }
    ]
    
    results = []
    
    for test in tests:
        print(f"\n📋 {test['name']}")
        print(f"   {test['description']}")
        print("-" * 30)
        
        try:
            result = subprocess.run([sys.executable, test['file']], 
                                  capture_output=True, text=True, timeout=300)
            
            if result.returncode == 0:
                print("✅ SUCCÈS")
                results.append(True)
            else:
                print("❌ ÉCHEC")
                print(result.stderr)
                results.append(False)
                
        except subprocess.TimeoutExpired:
            print("⏱️ TIMEOUT")
            results.append(False)
        except FileNotFoundError:
            print(f"⚠️ Fichier non trouvé : {test['file']}")
            results.append(False)
    
    print("\n" + "=" * 50)
    print("RÉSULTATS FINAUX")
    print("=" * 50)
    
    success_count = sum(results)
    total_tests = len(tests)
    
    print(f"✅ Tests réussis : {success_count}/{total_tests}")
    
    if success_count == total_tests:
        print("🎉 TOUS LES TESTS SONT PASSÉS !")
        return True
    else:
        print("❌ Certains tests ont échoué")
        return False

if __name__ == "__main__":
    run_all_tests()
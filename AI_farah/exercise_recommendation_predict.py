import sys
import json
import os
import traceback
from exercise_recommendation_ai import ExerciseRecommendationAI

def main():
    try:
        # Forcer l'encodage UTF-8
        if sys.stdout.encoding != 'utf-8':
            sys.stdout.reconfigure(encoding='utf-8')
        if sys.stderr.encoding != 'utf-8':
            sys.stderr.reconfigure(encoding='utf-8')
            
        print("🚀 Script démarré", file=sys.stderr)
        
        if len(sys.argv) < 2:
            result = {"error": "Profile file path required"}
            print(json.dumps(result, ensure_ascii=False))
            sys.exit(1)
        
        profile_file = sys.argv[1]
        print(f"📄 Lecture du fichier: {profile_file}", file=sys.stderr)
        
        # Vérifier que le fichier existe
        if not os.path.exists(profile_file):
            result = {"error": "Profile file not found"}
            print(json.dumps(result, ensure_ascii=False))
            sys.exit(1)
        
        # Lire le profil utilisateur avec encodage UTF-8
        with open(profile_file, 'r', encoding='utf-8') as f:
            user_profile = json.load(f)
        
        print(f"✅ Profil chargé: {user_profile}", file=sys.stderr)
        
        # Initialiser l'IA
        ai = ExerciseRecommendationAI()
        
        # Charger ou entraîner le modèle
        if not ai.load_model():
            print("🚀 Entraînement du modèle...", file=sys.stderr)
            performance = ai.train_model_from_files()
            if not performance:
                result = {"error": "Échec de l'entraînement du modèle"}
                print(json.dumps(result, ensure_ascii=False))
                sys.exit(1)
            print(f"✅ Modèle entraîné (R²: {performance['r2']:.3f})", file=sys.stderr)
        else:
            print("✅ Modèle chargé", file=sys.stderr)
        
        # Générer les recommandations
        print("🎯 Génération des recommandations...", file=sys.stderr)
        recommendations = ai.get_recommendations(user_profile, limit=15)
        
        if recommendations:
            print(f"✅ {len(recommendations)} recommandations générées", file=sys.stderr)
            
            # Sortie JSON propre pour Laravel
            output = json.dumps(recommendations, ensure_ascii=False, indent=None)
            print(output)
        else:
            result = {"error": "Aucune recommandation générée"}
            print(json.dumps(result, ensure_ascii=False))
            sys.exit(1)
        
    except Exception as e:
        error_msg = f"Erreur inattendue: {str(e)}"
        print(f"💥 {error_msg}", file=sys.stderr)
        print(f"💥 Traceback: {traceback.format_exc()}", file=sys.stderr)
        
        result = {"error": error_msg}
        print(json.dumps(result, ensure_ascii=False))
        sys.exit(1)

if __name__ == "__main__":
    main()
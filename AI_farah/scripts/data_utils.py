import pandas as pd
import json

class DataUtils:
    """Utilitaires pour la manipulation des datasets"""
    
    @staticmethod
    def load_dataset(filepath):
        """Charge un dataset depuis un fichier CSV"""
        try:
            return pd.read_csv(filepath)
        except Exception as e:
            print(f"Erreur lors du chargement de {filepath}: {e}")
            return None
    
    @staticmethod
    def validate_user_profile(profile):
        """Valide un profil utilisateur"""
        required_fields = ['age', 'imc', 'fitness_level']
        
        # Vérifier les champs requis
        for field in required_fields:
            if field not in profile:
                return False, f"Champ manquant: {field}"
        
        # Valider l'âge
        age = profile.get('age', 0)
        if not isinstance(age, (int, float)) or age < 16 or age > 90:
            return False, "Âge invalide (doit être entre 16 et 90)"
        
        # Valider l'IMC
        imc = profile.get('imc', 0)
        if not isinstance(imc, (int, float)) or imc < 15 or imc > 50:
            return False, "IMC invalide (doit être entre 15 et 50)"
        
        # Valider le niveau de fitness
        fitness_level = profile.get('fitness_level', '')
        valid_levels = ['beginner', 'intermediate', 'advanced']
        if fitness_level not in valid_levels:
            return False, f"Niveau de fitness invalide (doit être: {', '.join(valid_levels)})"
        
        return True, "Profil valide"
    
    @staticmethod
    def format_recommendations_for_laravel(recommendations):
        """Formate les recommandations pour Laravel"""
        formatted = []
        
        for rec in recommendations:
            formatted_rec = {
                'exercise_id': rec.get('exercise_id'),
                'exercise_name': rec.get('exercise_name'),
                'exercise_type': rec.get('exercise_type'),
                'exercise_category': rec.get('exercise_category'),
                'predicted_score': round(rec.get('predicted_score', 0), 1),
                'recommended_duration': rec.get('recommended_duration'),
                'difficulty_level': rec.get('difficulty_level'),
                'calories_per_minute': rec.get('calories_per_minute'),
                'recommendation_reason': DataUtils._generate_recommendation_reason(rec)
            }
            formatted.append(formatted_rec)
        
        return formatted
    
    @staticmethod
    def _generate_recommendation_reason(rec):
        """Génère une raison pour la recommandation"""
        score = rec.get('predicted_score', 0)
        exercise_type = rec.get('exercise_type', '')
        difficulty = rec.get('difficulty_level', 1)
        
        if score >= 85:
            reason = f"Excellent match pour votre profil"
        elif score >= 75:
            reason = f"Très recommandé pour vos objectifs"
        elif score >= 65:
            reason = f"Bon exercice de {exercise_type}"
        else:
            reason = f"Exercice adapté à votre niveau"
        
        if difficulty <= 3:
            reason += " (niveau débutant)"
        elif difficulty <= 7:
            reason += " (niveau intermédiaire)"
        else:
            reason += " (niveau avancé)"
        
        return reason
    
    @staticmethod
    def save_dataset(dataframe, filepath):
        """Sauvegarde un dataset"""
        try:
            dataframe.to_csv(filepath, index=False, encoding='utf-8')
            return True
        except Exception as e:
            print(f"Erreur lors de la sauvegarde de {filepath}: {e}")
            return False
import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.metrics import mean_squared_error, r2_score
import joblib
import json
import os
import warnings
warnings.filterwarnings('ignore')

class ExerciseRecommendationAI:
    def __init__(self):
        self.model = RandomForestRegressor(
            n_estimators=300,
            max_depth=20,
            random_state=42,
            min_samples_split=5,
            min_samples_leaf=2,
            n_jobs=-1
        )
        self.scaler = StandardScaler()
        self.label_encoders = {}
        self.is_trained = False
        
        # Chemins des fichiers
        self.ai_dir = os.path.dirname(__file__)
        self.datasets_dir = os.path.join(self.ai_dir, 'datasets')
        self.models_dir = os.path.join(self.ai_dir, 'models')
        
        os.makedirs(self.models_dir, exist_ok=True)
        
    def load_datasets_from_files(self):
        """Charge les datasets depuis les fichiers CSV"""
        print("📁 Chargement des datasets depuis les fichiers...")
        
        try:
            files_mapping = {
                'users': 'users_profiles.csv',
                'exercises': 'exercises_catalog.csv',
                'training': 'training_data.csv'
            }
            
            for file_type, filename in files_mapping.items():
                filepath = os.path.join(self.datasets_dir, filename)
                if not os.path.exists(filepath):
                    print(f"❌ Fichier manquant : {filepath}")
                    print("💡 Exécutez d'abord : python fix_datasets_structure.py")
                    return False
            
            self.df_users = pd.read_csv(os.path.join(self.datasets_dir, files_mapping['users']))
            self.df_exercises = pd.read_csv(os.path.join(self.datasets_dir, files_mapping['exercises']))
            self.training_data = pd.read_csv(os.path.join(self.datasets_dir, files_mapping['training']))
            
            print(f"✅ Datasets chargés :")
            print(f"   - Utilisateurs : {len(self.df_users):,}")
            print(f"   - Exercices : {len(self.df_exercises)}")
            print(f"   - Données d'entraînement : {len(self.training_data):,}")
            
            return True
            
        except Exception as e:
            print(f"❌ Erreur : {e}")
            return False
    
    def prepare_training_features(self):
        """Préparer les features pour l'entraînement"""
        try:
            # Vérifier quelles colonnes existent réellement
            available_columns = self.training_data.columns.tolist()
            print(f"📋 Colonnes disponibles: {available_columns}")
            
            # Features catégorielles (ajustées selon vos datasets)
            categorical_features = []
            possible_categorical = [
                'user_fitness_level', 'fitness_level', 'niveau_fitness',
                'exercise_type', 'type_exercice', 'exercice_type',
                'exercise_category', 'categorie_exercice', 'exercice_categorie'
            ]
            
            # Vérifier quelles features catégorielles existent
            for feature in possible_categorical:
                if feature in available_columns:
                    categorical_features.append(feature)
            
            print(f"🏷️ Features catégorielles trouvées: {categorical_features}")
            
            # Features numériques (ajustées selon vos datasets)
            numerical_features = []
            possible_numerical = [
                'user_age', 'age', 'age_utilisateur',
                'user_imc', 'imc', 'bmi',
                'exercise_duration', 'duree_exercice', 'duree',
                'exercise_difficulty', 'difficulte_exercice', 'difficulte',
                'user_activity_frequency', 'frequence_activite', 'frequence'
            ]
            
            # Vérifier quelles features numériques existent
            for feature in possible_numerical:
                if feature in available_columns:
                    numerical_features.append(feature)
            
            print(f"🔢 Features numériques trouvées: {numerical_features}")
            
            # Créer une copie des données
            data = self.training_data.copy()
            
            # Encoder les features catégorielles
            for feature in categorical_features:
                if feature not in self.label_encoders:
                    self.label_encoders[feature] = LabelEncoder()
                data[f'{feature}_encoded'] = self.label_encoders[feature].fit_transform(data[feature])
            
            # Préparer les features finales
            feature_columns = []
            feature_columns.extend([f'{f}_encoded' for f in categorical_features])
            feature_columns.extend(numerical_features)
            
            print(f"🎯 Features finales: {feature_columns}")
            
            # Créer X et y
            X = data[feature_columns]
            
            # Target variable (score ou rating)
            target_columns = ['score', 'rating', 'note', 'score_exercice']
            y_column = None
            for col in target_columns:
                if col in available_columns:
                    y_column = col
                    break
            
            if y_column is None:
                print("⚠️ Aucune colonne target trouvée, création d'un score synthétique")
                # Créer un score synthétique basé sur la difficulté et la durée
                data['synthetic_score'] = 100 - (data.get('exercise_difficulty', 5) * 10) + (data.get('exercise_duration', 30) / 10)
                y_column = 'synthetic_score'
            
            y = data[y_column]
            
            print(f"✅ Dataset préparé: {X.shape[0]} échantillons, {X.shape[1]} features")
            print(f"🎯 Target: {y_column}")
            
            return X, y
            
        except Exception as e:
            print(f"❌ Erreur dans prepare_training_features: {e}")
            import traceback
            traceback.print_exc()
            return None, None
        
    def train_model_from_files(self):
        """Entraîne le modèle depuis les fichiers CSV"""
        print("🚀 ENTRAÎNEMENT DU MODÈLE IA")
        print("=" * 40)
        
        # Charger les données
        if not self.load_datasets_from_files():
            return False
        
        # Préparer les features
        print("🔄 Préparation des features...")
        X, y = self.prepare_training_features()
        
        if X is None or y is None:
            print("❌ Impossible de préparer les features")
            return False
        
        # Division train/test
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42
        )
        
        # Normalisation
        print("🔄 Normalisation des données...")
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        print("📚 Entraînement du modèle...")
        self.model.fit(X_train_scaled, y_train)
        
        # Évaluation
        y_pred = self.model.predict(X_test_scaled)
        mse = mean_squared_error(y_test, y_pred)
        r2 = r2_score(y_test, y_pred)
        
        print(f"📊 Performance du modèle :")
        print(f"   - MSE: {mse:.2f}")
        print(f"   - R²: {r2:.3f}")
        
        self.is_trained = True
        self.save_model()
        
        return {'mse': mse, 'r2': r2}

    def get_recommendations(self, user_profile, limit=15):
        """Génère des recommandations pour un utilisateur"""
        if not self.is_trained:
            print("❌ Modèle non entraîné")
            return []
        
        # Charger les exercices si nécessaire
        if not hasattr(self, 'df_exercises'):
            if not self.load_datasets_from_files():
                return []
        
        recommendations = []
        
        # Pour chaque exercice, calculer le score de compatibilité
        for _, exercise in self.df_exercises.iterrows():
            try:
                # Créer les features pour la prédiction
                features = self._create_prediction_features(user_profile, exercise)
                
                # Encoder les features
                X_pred = self._encode_features_for_prediction(features)
                
                # Faire la prédiction
                X_pred_scaled = self.scaler.transform([X_pred])
                predicted_score = self.model.predict(X_pred_scaled)[0]
                
                # Ajuster le score selon des règles métier
                adjusted_score = self._adjust_score_with_rules(predicted_score, user_profile, exercise)
                
                recommendation = {
                    'exercise_id': int(exercise['exercise_id']),
                    'exercise_name': exercise['exercise_name'],
                    'exercise_type': exercise['exercise_type'],
                    'exercise_category': exercise['exercise_category'],
                    'predicted_score': float(adjusted_score),
                    'recommended_duration': self._calculate_duration(exercise, user_profile),
                    'difficulty_level': int(exercise['difficulty']),
                    'calories_per_minute': float(exercise['calories_per_minute']),
                    'recommendation_reason': self._generate_reason(user_profile, exercise, adjusted_score)
                }
                
                recommendations.append(recommendation)
                
            except Exception as e:
                print(f"⚠️ Erreur pour exercice {exercise.get('exercise_name', 'Inconnu')}: {e}")
                continue
        
        # Trier par score décroissant
        recommendations.sort(key=lambda x: x['predicted_score'], reverse=True)
        
        return recommendations[:limit]

    def _create_prediction_features(self, user_profile, exercise):
        """Créer les features pour une prédiction"""
        features = {}
        
        # Features utilisateur
        features['user_age'] = user_profile.get('age', 30)
        features['user_imc'] = user_profile.get('imc', 25.0)
        features['user_fitness_level'] = user_profile.get('fitness_level', 'beginner')
        features['user_activity_frequency'] = user_profile.get('activity_frequency', 2)
        
        # Features exercice
        features['exercise_type'] = exercise['exercise_type']
        features['exercise_category'] = exercise['exercise_category']
        features['exercise_duration'] = exercise['duration']
        features['exercise_difficulty'] = exercise['difficulty']
        
        return features

    def _encode_features_for_prediction(self, features):
        """Encoder les features pour la prédiction"""
        encoded = []
        
        # Features catégorielles
        categorical_features = ['user_fitness_level', 'exercise_type', 'exercise_category']
        for feature in categorical_features:
            if feature in self.label_encoders and feature in features:
                try:
                    encoded_val = self.label_encoders[feature].transform([features[feature]])[0]
                    encoded.append(encoded_val)
                except ValueError:
                    # Valeur inconnue, utiliser la première classe connue
                    encoded.append(0)
            else:
                encoded.append(0)
        
        # Features numériques
        numerical_features = ['user_age', 'user_imc', 'exercise_duration', 'exercise_difficulty', 'user_activity_frequency']
        for feature in numerical_features:
            encoded.append(features.get(feature, 0))
        
        return encoded

    def _adjust_score_with_rules(self, predicted_score, user_profile, exercise):
        """Ajuster le score avec des règles métier"""
        score = predicted_score
        
        # Ajustements selon l'âge
        age = user_profile.get('age', 30)
        if age > 50:
            if exercise['exercise_type'] == 'flexibility':
                score += 10  # Bonus pour flexibilité chez seniors
            elif exercise['difficulty'] > 7:
                score -= 15  # Malus pour haute difficulté chez seniors
        
        # Ajustements selon l'IMC
        imc = user_profile.get('imc', 25)
        if imc > 30:
            if exercise['exercise_type'] == 'cardio':
                score += 15  # Bonus cardio pour surpoids
            elif exercise['difficulty'] > 6:
                score -= 10  # Malus difficulté élevée pour surpoids
        
        # Ajustements selon le niveau de fitness
        fitness_level = user_profile.get('fitness_level', 'beginner')
        if fitness_level == 'beginner' and exercise['difficulty'] > 5:
            score -= 20
        elif fitness_level == 'advanced' and exercise['difficulty'] < 4:
            score -= 10
        
        # S'assurer que le score reste dans les limites
        return max(0, min(100, score))

    def _calculate_duration(self, exercise, user_profile):
        """Calculer la durée recommandée"""
        base_duration = exercise['duration']
        fitness_level = user_profile.get('fitness_level', 'beginner')
        
        multipliers = {
            'beginner': 0.7,
            'intermediate': 1.0,
            'advanced': 1.2
        }
        
        return int(base_duration * multipliers.get(fitness_level, 1.0))

    def _generate_reason(self, user_profile, exercise, score):
        """Générer une raison pour la recommandation"""
        reasons = []
        
        if score > 85:
            reasons.append("Excellent match pour votre profil")
        elif score > 75:
            reasons.append("Très bien adapté à vos objectifs")
        elif score > 65:
            reasons.append("Bonne option pour votre niveau")
        else:
            reasons.append("Option intéressante à considérer")
        
        # Ajouter des raisons spécifiques
        fitness_level = user_profile.get('fitness_level', 'beginner')
        if fitness_level == 'beginner' and exercise['difficulty'] <= 3:
            reasons.append("parfait pour débuter")
        elif fitness_level == 'advanced' and exercise['difficulty'] >= 7:
            reasons.append("challenge stimulant")
        
        if exercise['exercise_type'] == 'cardio':
            reasons.append("excellent pour le système cardiovasculaire")
        elif exercise['exercise_type'] == 'strength':
            reasons.append("renforce efficacement les muscles")
        elif exercise['exercise_type'] == 'flexibility':
            reasons.append("améliore la mobilité et la souplesse")
        
        return " - ".join(reasons).capitalize()

    def save_model(self):
        """Sauvegarde le modèle"""
        if not self.is_trained:
            return False
        
        try:
            joblib.dump(self.model, os.path.join(self.models_dir, 'exercise_recommendation_model.pkl'))
            joblib.dump(self.scaler, os.path.join(self.models_dir, 'exercise_scaler.pkl'))
            joblib.dump(self.label_encoders, os.path.join(self.models_dir, 'exercise_label_encoders.pkl'))
            
            print("✅ Modèle sauvegardé")
            return True
        except Exception as e:
            print(f"❌ Erreur sauvegarde: {e}")
            return False
    
    def load_model(self):
        """Charge un modèle pré-entraîné"""
        try:
            self.model = joblib.load(os.path.join(self.models_dir, 'exercise_recommendation_model.pkl'))
            self.scaler = joblib.load(os.path.join(self.models_dir, 'exercise_scaler.pkl'))
            self.label_encoders = joblib.load(os.path.join(self.models_dir, 'exercise_label_encoders.pkl'))
            self.is_trained = True
            print("✅ Modèle chargé")
            return True
        except FileNotFoundError:
            print("❌ Aucun modèle sauvegardé")
            return False
        except Exception as e:
            print(f"❌ Erreur chargement: {e}")
            return False

    # Alias pour compatibilité
    def train_model(self):
        """Alias pour train_model_from_files"""
        return self.train_model_from_files()

if __name__ == "__main__":
    # Test du modèle
    ai = ExerciseRecommendationAI()
    
    print("🧪 TEST DU MODÈLE IA")
    print("=" * 30)
    
    # Entraîner le modèle
    performance = ai.train_model_from_files()
    
    if performance:
        print(f"\n✅ Modèle entraîné avec succès!")
        print(f"   Performance R²: {performance['r2']:.3f}")
        
        # Test avec un profil utilisateur
        test_user = {
            'age': 28,
            'imc': 26.1,
            'fitness_level': 'beginner',
            'activity_frequency': 1
        }
        
        print(f"\n🎯 Test avec profil utilisateur:")
        print(f"   Âge: {test_user['age']}, IMC: {test_user['imc']}")
        print(f"   Niveau: {test_user['fitness_level']}")
        
        recommendations = ai.get_recommendations(test_user, limit=5)
        
        if recommendations:
            print(f"\n📋 Top 5 des recommandations:")
            for i, rec in enumerate(recommendations, 1):
                print(f"   {i}. {rec['exercise_name']}")
                print(f"      Score: {rec['predicted_score']:.1f}%")
                print(f"      Type: {rec['exercise_type']}")
                print(f"      Durée: {rec['recommended_duration']} min")
                print(f"      Raison: {rec['recommendation_reason']}")
                print()
        else:
            print("❌ Aucune recommandation générée")
    else:
        print("❌ Échec de l'entraînement")
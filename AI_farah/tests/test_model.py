import pytest
import pandas as pd
import numpy as np
import os
import sys

# Ajouter le dossier parent au path
sys.path.insert(0, os.path.dirname(os.path.dirname(__file__)))

from exercise_recommendation_ai import ExerciseRecommendationAI

class TestExerciseRecommendationAI:
    
    @pytest.fixture
    def ai_model(self):
        """Fixture pour créer une instance du modèle"""
        return ExerciseRecommendationAI()
    
    @pytest.fixture
    def sample_user_profile(self):
        """Fixture pour un profil utilisateur de test"""
        return {
            'age': 30,
            'imc': 24.0,
            'fitness_level': 'intermediate',
            'activity_frequency': 4,
            'has_medical_constraints': False,
            'experience_months': 12
        }
    
    def test_model_initialization(self, ai_model):
        """Test d'initialisation du modèle"""
        assert ai_model.model is not None
        assert ai_model.scaler is not None
        assert ai_model.label_encoders == {}
        assert ai_model.is_trained == False
    
    def test_datasets_exist(self):
        """Test de l'existence des datasets"""
        datasets_dir = 'datasets'
        required_files = ['users_profiles.csv', 'exercises_catalog.csv', 'training_data.csv']
        
        for filename in required_files:
            filepath = os.path.join(datasets_dir, filename)
            assert os.path.exists(filepath), f"Dataset manquant: {filename}"
            assert os.path.getsize(filepath) > 0, f"Dataset vide: {filename}"
    
    def test_dataset_loading(self, ai_model):
        """Test du chargement des datasets"""
        result = ai_model.load_datasets_from_files()
        assert result == True, "Échec du chargement des datasets"
        
        assert hasattr(ai_model, 'df_users')
        assert hasattr(ai_model, 'df_exercises')
        assert hasattr(ai_model, 'df_training')
        
        assert len(ai_model.df_users) > 0
        assert len(ai_model.df_exercises) > 0
        assert len(ai_model.df_training) > 0
    
    def test_model_training(self, ai_model):
        """Test d'entraînement du modèle"""
        performance = ai_model.train_model_from_files()
        
        assert performance is not False
        assert ai_model.is_trained == True
        assert 'mse' in performance
        assert 'r2' in performance
        assert performance['r2'] > 0
    
    def test_predictions(self, ai_model, sample_user_profile):
        """Test des prédictions"""
        if not ai_model.load_model():
            ai_model.train_model_from_files()
        
        recommendations = ai_model.get_recommendations(sample_user_profile, limit=5)
        
        assert isinstance(recommendations, list)
        assert len(recommendations) > 0
        assert len(recommendations) <= 5
        
        for rec in recommendations:
            assert 'exercise_id' in rec
            assert 'exercise_name' in rec
            assert 'predicted_score' in rec
            assert isinstance(rec['predicted_score'], (int, float))

if __name__ == "__main__":
    pytest.main([__file__, "-v"])
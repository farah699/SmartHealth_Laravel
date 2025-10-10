import pytest
import pandas as pd
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.dirname(__file__)))

class TestDataValidation:
    
    def test_users_dataset_structure(self):
        """Test de la structure du dataset utilisateurs"""
        df = pd.read_csv('datasets/users_profiles.csv')
        
        required_columns = ['user_id', 'age', 'imc', 'fitness_level', 'activity_frequency']
        for col in required_columns:
            assert col in df.columns, f"Colonne manquante: {col}"
        
        assert len(df) > 0, "Dataset utilisateurs vide"
        assert df['age'].min() >= 16, "Âge minimum invalide"
        assert df['age'].max() <= 90, "Âge maximum invalide"
        assert df['imc'].min() > 0, "IMC minimum invalide"
    
    def test_exercises_dataset_structure(self):
        """Test de la structure du dataset exercices"""
        df = pd.read_csv('datasets/exercises_catalog.csv')
        
        required_columns = ['id', 'name', 'type', 'difficulty_level']
        for col in required_columns:
            assert col in df.columns, f"Colonne manquante: {col}"
        
        assert len(df) > 0, "Dataset exercices vide"
        assert df['difficulty_level'].min() >= 1, "Difficulté minimum invalide"
        assert df['difficulty_level'].max() <= 10, "Difficulté maximum invalide"
    
    def test_training_dataset_structure(self):
        """Test de la structure du dataset d'entraînement"""
        df = pd.read_csv('datasets/training_data.csv')
        
        required_columns = ['user_id', 'exercise_id', 'recommendation_score']
        for col in required_columns:
            assert col in df.columns, f"Colonne manquante: {col}"
        
        assert len(df) > 0, "Dataset d'entraînement vide"
        assert df['recommendation_score'].min() >= 0, "Score minimum invalide"
        assert df['recommendation_score'].max() <= 100, "Score maximum invalide"

if __name__ == "__main__":
    pytest.main([__file__, "-v"])
import pytest
import json
import os
import subprocess
import sys
import tempfile

class TestLaravelIntegration:
    
    def test_prediction_script_exists(self):
        """Test de l'existence du script de prédiction"""
        script_path = 'exercise_recommendation_predict.py'
        assert os.path.exists(script_path), "Script de prédiction manquant"
    
    def test_prediction_script_execution(self):
        """Test d'exécution du script de prédiction"""
        test_profile = {
            'age': 30,
            'imc': 24.0,
            'fitness_level': 'intermediate',
            'activity_frequency': 4,
            'has_medical_constraints': False,
            'experience_months': 12
        }
        
        with tempfile.NamedTemporaryFile(mode='w', suffix='.json', delete=False) as f:
            json.dump(test_profile, f)
            temp_file = f.name
        
        try:
            result = subprocess.run(
                [sys.executable, 'exercise_recommendation_predict.py', temp_file],
                capture_output=True,
                text=True,
                timeout=60
            )
            
            assert result.returncode == 0, f"Script échoué: {result.stderr}"
            
            try:
                output = json.loads(result.stdout)
                assert isinstance(output, list), "La sortie doit être une liste"
                
                if len(output) > 0:
                    rec = output[0]
                    required_fields = ['exercise_id', 'exercise_name', 'exercise_type', 'predicted_score']
                    for field in required_fields:
                        assert field in rec, f"Champ manquant: {field}"
                        
            except json.JSONDecodeError:
                pytest.fail(f"Sortie JSON invalide: {result.stdout}")
        
        finally:
            os.unlink(temp_file)
    
    def test_data_utils_validation(self):
        """Test des utilitaires de validation"""
        from scripts.data_utils import DataUtils
        
        valid_profile = {
            'age': 30,
            'imc': 24.0,
            'fitness_level': 'intermediate'
        }
        
        is_valid, message = DataUtils.validate_user_profile(valid_profile)
        assert is_valid == True
        
        invalid_profile = {
            'age': 150,
            'imc': 24.0,
            'fitness_level': 'intermediate'
        }
        
        is_valid, message = DataUtils.validate_user_profile(invalid_profile)
        assert is_valid == False

if __name__ == "__main__":
    pytest.main([__file__, "-v"])
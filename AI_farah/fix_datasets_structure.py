import pandas as pd
import numpy as np
import os

def fix_datasets():
    """Corriger et standardiser la structure des datasets"""
    
    print("🔧 CORRECTION DES DATASETS")
    print("=" * 40)
    
    # Créer le dossier datasets s'il n'existe pas
    datasets_dir = "datasets"
    os.makedirs(datasets_dir, exist_ok=True)
    
    # 1. Générer users_profiles.csv
    print("👥 Génération du profil utilisateurs...")
    users_data = []
    
    for i in range(1, 40001):  # 40,000 utilisateurs
        user = {
            'user_id': i,
            'age': np.random.randint(18, 70),
            'imc': round(np.random.normal(25, 5), 1),
            'fitness_level': np.random.choice(['beginner', 'intermediate', 'advanced'], 
                                            p=[0.4, 0.4, 0.2]),
            'activity_frequency': np.random.randint(1, 8),
            'experience_months': np.random.randint(1, 60),
            'has_medical_constraints': np.random.choice([True, False], p=[0.2, 0.8])
        }
        users_data.append(user)
    
    users_df = pd.DataFrame(users_data)
    users_df.to_csv(os.path.join(datasets_dir, 'users_profiles.csv'), index=False)
    print(f"✅ users_profiles.csv créé: {len(users_df)} utilisateurs")
    
    # 2. Générer exercises_catalog.csv
    print("💪 Génération du catalogue d'exercices...")
    exercises = [
        # Cardio
        {'exercise_id': 1, 'exercise_name': 'Marche lente', 'exercise_type': 'cardio', 'exercise_category': 'endurance', 'duration': 30, 'difficulty': 2, 'calories_per_minute': 4},
        {'exercise_id': 2, 'exercise_name': 'Marche rapide', 'exercise_type': 'cardio', 'exercise_category': 'endurance', 'duration': 25, 'difficulty': 3, 'calories_per_minute': 6},
        {'exercise_id': 3, 'exercise_name': 'Course lente', 'exercise_type': 'cardio', 'exercise_category': 'endurance', 'duration': 30, 'difficulty': 5, 'calories_per_minute': 8},
        {'exercise_id': 4, 'exercise_name': 'Course modérée', 'exercise_type': 'cardio', 'exercise_category': 'endurance', 'duration': 25, 'difficulty': 6, 'calories_per_minute': 10},
        {'exercise_id': 5, 'exercise_name': 'Course rapide', 'exercise_type': 'cardio', 'exercise_category': 'endurance', 'duration': 20, 'difficulty': 8, 'calories_per_minute': 12},
        {'exercise_id': 6, 'exercise_name': 'Vélo stationnaire', 'exercise_type': 'cardio', 'exercise_category': 'endurance', 'duration': 35, 'difficulty': 4, 'calories_per_minute': 6},
        {'exercise_id': 7, 'exercise_name': 'Elliptique', 'exercise_type': 'cardio', 'exercise_category': 'endurance', 'duration': 30, 'difficulty': 5, 'calories_per_minute': 8},
        {'exercise_id': 8, 'exercise_name': 'HIIT débutant', 'exercise_type': 'cardio', 'exercise_category': 'haute intensité', 'duration': 15, 'difficulty': 6, 'calories_per_minute': 10},
        {'exercise_id': 9, 'exercise_name': 'HIIT avancé', 'exercise_type': 'cardio', 'exercise_category': 'haute intensité', 'duration': 20, 'difficulty': 9, 'calories_per_minute': 15},
        {'exercise_id': 10, 'exercise_name': 'Danse cardio', 'exercise_type': 'cardio', 'exercise_category': 'fun', 'duration': 30, 'difficulty': 4, 'calories_per_minute': 7},
        
        # Strength
        {'exercise_id': 11, 'exercise_name': 'Pompes genoux', 'exercise_type': 'strength', 'exercise_category': 'haut du corps', 'duration': 15, 'difficulty': 3, 'calories_per_minute': 4},
        {'exercise_id': 12, 'exercise_name': 'Pompes classiques', 'exercise_type': 'strength', 'exercise_category': 'haut du corps', 'duration': 15, 'difficulty': 5, 'calories_per_minute': 6},
        {'exercise_id': 13, 'exercise_name': 'Squats air', 'exercise_type': 'strength', 'exercise_category': 'bas du corps', 'duration': 15, 'difficulty': 3, 'calories_per_minute': 5},
        {'exercise_id': 14, 'exercise_name': 'Squats avec poids', 'exercise_type': 'strength', 'exercise_category': 'bas du corps', 'duration': 20, 'difficulty': 6, 'calories_per_minute': 7},
        {'exercise_id': 15, 'exercise_name': 'Planche', 'exercise_type': 'strength', 'exercise_category': 'core', 'duration': 10, 'difficulty': 4, 'calories_per_minute': 3},
        {'exercise_id': 16, 'exercise_name': 'Burpees', 'exercise_type': 'strength', 'exercise_category': 'full body', 'duration': 15, 'difficulty': 8, 'calories_per_minute': 12},
        {'exercise_id': 17, 'exercise_name': 'Jumping jacks', 'exercise_type': 'strength', 'exercise_category': 'full body', 'duration': 15, 'difficulty': 4, 'calories_per_minute': 6},
        {'exercise_id': 18, 'exercise_name': 'Mountain climbers', 'exercise_type': 'strength', 'exercise_category': 'core', 'duration': 15, 'difficulty': 6, 'calories_per_minute': 8},
        {'exercise_id': 19, 'exercise_name': 'Fentes', 'exercise_type': 'strength', 'exercise_category': 'bas du corps', 'duration': 15, 'difficulty': 5, 'calories_per_minute': 6},
        {'exercise_id': 20, 'exercise_name': 'Tractions assistées', 'exercise_type': 'strength', 'exercise_category': 'haut du corps', 'duration': 15, 'difficulty': 7, 'calories_per_minute': 7},
        
        # Flexibility
        {'exercise_id': 21, 'exercise_name': 'Étirements doux', 'exercise_type': 'flexibility', 'exercise_category': 'mobilité', 'duration': 15, 'difficulty': 1, 'calories_per_minute': 2},
        {'exercise_id': 22, 'exercise_name': 'Yoga débutant', 'exercise_type': 'flexibility', 'exercise_category': 'bien-être', 'duration': 30, 'difficulty': 2, 'calories_per_minute': 3},
        {'exercise_id': 23, 'exercise_name': 'Yoga intermédiaire', 'exercise_type': 'flexibility', 'exercise_category': 'bien-être', 'duration': 45, 'difficulty': 4, 'calories_per_minute': 4},
        {'exercise_id': 24, 'exercise_name': 'Yoga avancé', 'exercise_type': 'flexibility', 'exercise_category': 'bien-être', 'duration': 60, 'difficulty': 6, 'calories_per_minute': 5},
        {'exercise_id': 25, 'exercise_name': 'Pilates débutant', 'exercise_type': 'flexibility', 'exercise_category': 'core', 'duration': 30, 'difficulty': 3, 'calories_per_minute': 4},
        {'exercise_id': 26, 'exercise_name': 'Pilates avancé', 'exercise_type': 'flexibility', 'exercise_category': 'core', 'duration': 45, 'difficulty': 6, 'calories_per_minute': 6},
        {'exercise_id': 27, 'exercise_name': 'Tai Chi', 'exercise_type': 'flexibility', 'exercise_category': 'bien-être', 'duration': 30, 'difficulty': 2, 'calories_per_minute': 3},
        {'exercise_id': 28, 'exercise_name': 'Étirements sportifs', 'exercise_type': 'flexibility', 'exercise_category': 'récupération', 'duration': 20, 'difficulty': 3, 'calories_per_minute': 2},
        
        # Balance
        {'exercise_id': 29, 'exercise_name': 'Équilibre sur une jambe', 'exercise_type': 'balance', 'exercise_category': 'stabilité', 'duration': 10, 'difficulty': 2, 'calories_per_minute': 2},
        {'exercise_id': 30, 'exercise_name': 'Yoga équilibre', 'exercise_type': 'balance', 'exercise_category': 'stabilité', 'duration': 20, 'difficulty': 4, 'calories_per_minute': 3},
    ]
    
    # Compléter avec plus d'exercices pour atteindre 50
    for i in range(31, 51):
        exercise_types = ['cardio', 'strength', 'flexibility', 'balance']
        categories = {
            'cardio': ['endurance', 'haute intensité', 'fun'],
            'strength': ['haut du corps', 'bas du corps', 'core', 'full body'],
            'flexibility': ['mobilité', 'bien-être', 'récupération'],
            'balance': ['stabilité', 'coordination']
        }
        
        ex_type = np.random.choice(exercise_types)
        ex_category = np.random.choice(categories[ex_type])
        
        exercises.append({
            'exercise_id': i,
            'exercise_name': f'{ex_type.capitalize()} {i}',
            'exercise_type': ex_type,
            'exercise_category': ex_category,
            'duration': np.random.randint(15, 60),
            'difficulty': np.random.randint(1, 10),
            'calories_per_minute': np.random.randint(2, 15)
        })
    
    exercises_df = pd.DataFrame(exercises)
    exercises_df.to_csv(os.path.join(datasets_dir, 'exercises_catalog.csv'), index=False)
    print(f"✅ exercises_catalog.csv créé: {len(exercises_df)} exercices")
    
    # 3. Générer training_data.csv
    print("📊 Génération des données d'entraînement...")
    training_data = []
    
    print("   Génération en cours...")
    for i, (_, user) in enumerate(users_df.iterrows()):
        if i % 5000 == 0:
            print(f"   Progression: {i:,} / {len(users_df):,} utilisateurs")
        
        # Chaque utilisateur a testé plusieurs exercices
        num_exercises = np.random.randint(5, 15)
        tested_exercises = np.random.choice(exercises_df['exercise_id'], num_exercises, replace=False)
        
        for exercise_id in tested_exercises:
            exercise = exercises_df[exercises_df['exercise_id'] == exercise_id].iloc[0]
            
            # Calculer un score basé sur la compatibilité utilisateur-exercice
            base_score = 50
            
            # Ajustements selon le niveau fitness
            if user['fitness_level'] == 'beginner' and exercise['difficulty'] <= 3:
                base_score += 20
            elif user['fitness_level'] == 'intermediate' and 3 <= exercise['difficulty'] <= 7:
                base_score += 20
            elif user['fitness_level'] == 'advanced' and exercise['difficulty'] >= 6:
                base_score += 20
            else:
                base_score -= 10
            
            # Ajustements selon l'âge
            if user['age'] > 50:
                if exercise['exercise_type'] == 'flexibility':
                    base_score += 15
                elif exercise['difficulty'] > 7:
                    base_score -= 20
            elif user['age'] < 30:
                if exercise['exercise_type'] == 'strength':
                    base_score += 10
            
            # Ajustements selon l'IMC
            if user['imc'] > 30:
                if exercise['exercise_type'] == 'cardio':
                    base_score += 15
                elif exercise['difficulty'] > 6:
                    base_score -= 15
            elif user['imc'] < 20:
                if exercise['exercise_type'] == 'strength':
                    base_score += 10
            
            # Ajustements selon la fréquence d'activité
            if user['activity_frequency'] > 5:
                base_score += 10
            elif user['activity_frequency'] < 2:
                if exercise['difficulty'] > 5:
                    base_score -= 15
            
            # Contraintes médicales
            if user['has_medical_constraints']:
                if exercise['difficulty'] > 6:
                    base_score -= 20
                if exercise['exercise_type'] == 'flexibility':
                    base_score += 10
            
            # Ajouter du bruit aléatoire pour rendre les données plus réalistes
            noise = np.random.normal(0, 10)
            score = base_score + noise
            
            # S'assurer que le score reste entre 0 et 100
            score = max(0, min(100, score))
            
            training_data.append({
                'user_id': user['user_id'],
                'exercise_id': exercise_id,
                'user_age': user['age'],
                'user_imc': user['imc'],
                'user_fitness_level': user['fitness_level'],
                'user_activity_frequency': user['activity_frequency'],
                'exercise_type': exercise['exercise_type'],
                'exercise_category': exercise['exercise_category'],
                'exercise_duration': exercise['duration'],
                'exercise_difficulty': exercise['difficulty'],
                'score': round(score, 1)
            })
    
    training_df = pd.DataFrame(training_data)
    training_df.to_csv(os.path.join(datasets_dir, 'training_data.csv'), index=False)
    print(f"✅ training_data.csv créé: {len(training_df):,} enregistrements")
    
    # 4. Afficher un résumé des datasets
    print("\n📋 RÉSUMÉ DES DATASETS CRÉÉS")
    print("=" * 40)
    
    print(f"👥 Users Profiles ({len(users_df):,} lignes):")
    print(f"   - Âge moyen: {users_df['age'].mean():.1f} ans")
    print(f"   - IMC moyen: {users_df['imc'].mean():.1f}")
    print(f"   - Niveaux fitness: {dict(users_df['fitness_level'].value_counts())}")
    
    print(f"\n💪 Exercises Catalog ({len(exercises_df)} lignes):")
    print(f"   - Types: {dict(exercises_df['exercise_type'].value_counts())}")
    print(f"   - Difficulté moyenne: {exercises_df['difficulty'].mean():.1f}/10")
    
    print(f"\n📊 Training Data ({len(training_df):,} lignes):")
    print(f"   - Score moyen: {training_df['score'].mean():.1f}/100")
    print(f"   - Utilisateurs uniques: {training_df['user_id'].nunique():,}")
    print(f"   - Exercices uniques: {training_df['exercise_id'].nunique()}")
    
    print("\n🎉 DATASETS CRÉÉS AVEC SUCCÈS!")
    print("=" * 40)
    print("💡 Vous pouvez maintenant lancer:")
    print("   python exercise_recommendation_ai.py")
    print("   python test_ai.py")

if __name__ == "__main__":
    # Fixer la graine aléatoire pour la reproductibilité
    np.random.seed(42)
    fix_datasets()
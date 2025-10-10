import pandas as pd
import numpy as np
from faker import Faker
import json
import os
from datetime import datetime, timedelta
import random

class MassiveDatasetGenerator:
    def __init__(self):
        self.faker = Faker('fr_FR')
        self.output_dir = '../datasets'
        
        # Créer le dossier datasets
        os.makedirs(self.output_dir, exist_ok=True)
        
        # Configuration pour 40K utilisateurs
        self.n_users = 40000
        self.n_exercises = 50  # Catalogue étendu
        
    def generate_comprehensive_exercises_catalog(self):
        """Génère un catalogue complet de 50 exercices"""
        print("🏃‍♂️ Génération du catalogue d'exercices complet...")
        
        exercises = []
        exercise_id = 1
        
        # CARDIO - 20 exercices
        cardio_exercises = [
            # Débutant (8 exercices)
            {'name': 'Marche lente', 'difficulty': 1, 'duration_min': 10, 'duration_max': 60, 'calories_per_min': 3.5, 'age_min': 16, 'age_max': 90, 'imc_min': 15, 'imc_max': 45},
            {'name': 'Marche rapide', 'difficulty': 3, 'duration_min': 15, 'duration_max': 90, 'calories_per_min': 5.0, 'age_min': 16, 'age_max': 85, 'imc_min': 16, 'imc_max': 40},
            {'name': 'Vélo stationnaire léger', 'difficulty': 2, 'duration_min': 10, 'duration_max': 45, 'calories_per_min': 6.0, 'age_min': 18, 'age_max': 80, 'imc_min': 16, 'imc_max': 38},
            {'name': 'Aquagym débutant', 'difficulty': 2, 'duration_min': 20, 'duration_max': 45, 'calories_per_min': 7.0, 'age_min': 20, 'age_max': 75, 'imc_min': 18, 'imc_max': 45},
            {'name': 'Danse douce', 'difficulty': 3, 'duration_min': 15, 'duration_max': 40, 'calories_per_min': 5.5, 'age_min': 18, 'age_max': 70, 'imc_min': 17, 'imc_max': 35},
            {'name': 'Elliptique facile', 'difficulty': 3, 'duration_min': 10, 'duration_max': 30, 'calories_per_min': 8.0, 'age_min': 20, 'age_max': 65, 'imc_min': 18, 'imc_max': 35},
            {'name': 'Stepper débutant', 'difficulty': 3, 'duration_min': 8, 'duration_max': 25, 'calories_per_min': 7.5, 'age_min': 18, 'age_max': 60, 'imc_min': 18, 'imc_max': 32},
            {'name': 'Rameur léger', 'difficulty': 4, 'duration_min': 10, 'duration_max': 30, 'calories_per_min': 9.0, 'age_min': 20, 'age_max': 65, 'imc_min': 18, 'imc_max': 35},
            
            # Intermédiaire (8 exercices)
            {'name': 'Course modérée', 'difficulty': 5, 'duration_min': 15, 'duration_max': 60, 'calories_per_min': 12.0, 'age_min': 18, 'age_max': 65, 'imc_min': 18, 'imc_max': 32},
            {'name': 'Vélo elliptique', 'difficulty': 5, 'duration_min': 15, 'duration_max': 45, 'calories_per_min': 10.0, 'age_min': 20, 'age_max': 60, 'imc_min': 18, 'imc_max': 30},
            {'name': 'HIIT débutant', 'difficulty': 6, 'duration_min': 15, 'duration_max': 30, 'calories_per_min': 15.0, 'age_min': 18, 'age_max': 50, 'imc_min': 18, 'imc_max': 28},
            {'name': 'Boxe cardio', 'difficulty': 6, 'duration_min': 20, 'duration_max': 45, 'calories_per_min': 13.0, 'age_min': 18, 'age_max': 55, 'imc_min': 18, 'imc_max': 30},
            {'name': 'Corde à sauter', 'difficulty': 6, 'duration_min': 5, 'duration_max': 20, 'calories_per_min': 14.0, 'age_min': 16, 'age_max': 50, 'imc_min': 18, 'imc_max': 28},
            {'name': 'Spinning', 'difficulty': 7, 'duration_min': 20, 'duration_max': 60, 'calories_per_min': 16.0, 'age_min': 20, 'age_max': 55, 'imc_min': 18, 'imc_max': 30},
            {'name': 'Step aérobic', 'difficulty': 5, 'duration_min': 15, 'duration_max': 45, 'calories_per_min': 11.0, 'age_min': 18, 'age_max': 55, 'imc_min': 18, 'imc_max': 30},
            {'name': 'Rameur modéré', 'difficulty': 6, 'duration_min': 15, 'duration_max': 40, 'calories_per_min': 12.0, 'age_min': 20, 'age_max': 60, 'imc_min': 18, 'imc_max': 32},
            
            # Avancé (4 exercices)
            {'name': 'Course intense', 'difficulty': 8, 'duration_min': 20, 'duration_max': 90, 'calories_per_min': 18.0, 'age_min': 18, 'age_max': 50, 'imc_min': 18, 'imc_max': 26},
            {'name': 'HIIT avancé', 'difficulty': 9, 'duration_min': 20, 'duration_max': 45, 'calories_per_min': 20.0, 'age_min': 18, 'age_max': 45, 'imc_min': 18, 'imc_max': 25},
            {'name': 'CrossFit cardio', 'difficulty': 9, 'duration_min': 15, 'duration_max': 40, 'calories_per_min': 22.0, 'age_min': 20, 'age_max': 40, 'imc_min': 18, 'imc_max': 25},
            {'name': 'Sprint intervals', 'difficulty': 10, 'duration_min': 10, 'duration_max': 30, 'calories_per_min': 25.0, 'age_min': 18, 'age_max': 35, 'imc_min': 18, 'imc_max': 24}
        ]
        
        for exercise in cardio_exercises:
            exercises.append({
                'id': exercise_id,
                'name': exercise['name'],
                'type': 'cardio',
                'category': self._get_category_from_difficulty(exercise['difficulty']),
                'difficulty_level': exercise['difficulty'],
                'duration_min': exercise['duration_min'],
                'duration_max': exercise['duration_max'],
                'calories_per_minute': exercise['calories_per_min'],
                'age_min': exercise['age_min'],
                'age_max': exercise['age_max'],
                'imc_min': exercise['imc_min'],
                'imc_max': exercise['imc_max']
            })
            exercise_id += 1
        
        # STRENGTH - 15 exercices
        strength_exercises = [
            # Débutant (6 exercices)
            {'name': 'Squats assistés', 'difficulty': 2, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 6.0, 'age_min': 16, 'age_max': 75, 'imc_min': 16, 'imc_max': 40},
            {'name': 'Pompes genoux', 'difficulty': 3, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 7.0, 'age_min': 16, 'age_max': 70, 'imc_min': 16, 'imc_max': 35},
            {'name': 'Planche genoux', 'difficulty': 3, 'duration_min': 3, 'duration_max': 10, 'calories_per_min': 5.0, 'age_min': 16, 'age_max': 70, 'imc_min': 16, 'imc_max': 35},
            {'name': 'Wall sits', 'difficulty': 4, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 6.5, 'age_min': 18, 'age_max': 65, 'imc_min': 18, 'imc_max': 32},
            {'name': 'Abdos crunch', 'difficulty': 3, 'duration_min': 5, 'duration_max': 20, 'calories_per_min': 5.5, 'age_min': 16, 'age_max': 70, 'imc_min': 16, 'imc_max': 35},
            {'name': 'Fentes assistées', 'difficulty': 4, 'duration_min': 8, 'duration_max': 20, 'calories_per_min': 7.5, 'age_min': 18, 'age_max': 65, 'imc_min': 18, 'imc_max': 32},
            
            # Intermédiaire (6 exercices)
            {'name': 'Squats classiques', 'difficulty': 5, 'duration_min': 10, 'duration_max': 25, 'calories_per_min': 8.0, 'age_min': 18, 'age_max': 65, 'imc_min': 18, 'imc_max': 30},
            {'name': 'Pompes classiques', 'difficulty': 6, 'duration_min': 8, 'duration_max': 20, 'calories_per_min': 9.0, 'age_min': 18, 'age_max': 60, 'imc_min': 18, 'imc_max': 28},
            {'name': 'Planche', 'difficulty': 6, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 7.0, 'age_min': 18, 'age_max': 60, 'imc_min': 18, 'imc_max': 28},
            {'name': 'Burpees modifiés', 'difficulty': 7, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 12.0, 'age_min': 20, 'age_max': 50, 'imc_min': 18, 'imc_max': 26},
            {'name': 'Mountain climbers', 'difficulty': 6, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 10.0, 'age_min': 18, 'age_max': 55, 'imc_min': 18, 'imc_max': 28},
            {'name': 'Dips chaise', 'difficulty': 5, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 8.5, 'age_min': 18, 'age_max': 60, 'imc_min': 18, 'imc_max': 30},
            
            # Avancé (3 exercices)
            {'name': 'Burpees complets', 'difficulty': 8, 'duration_min': 8, 'duration_max': 20, 'calories_per_min': 15.0, 'age_min': 20, 'age_max': 45, 'imc_min': 18, 'imc_max': 25},
            {'name': 'Pompes diamant', 'difficulty': 8, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 11.0, 'age_min': 20, 'age_max': 50, 'imc_min': 18, 'imc_max': 26},
            {'name': 'Pistol squats', 'difficulty': 9, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 12.0, 'age_min': 20, 'age_max': 40, 'imc_min': 18, 'imc_max': 24}
        ]
        
        for exercise in strength_exercises:
            exercises.append({
                'id': exercise_id,
                'name': exercise['name'],
                'type': 'strength',
                'category': self._get_category_from_difficulty(exercise['difficulty']),
                'difficulty_level': exercise['difficulty'],
                'duration_min': exercise['duration_min'],
                'duration_max': exercise['duration_max'],
                'calories_per_minute': exercise['calories_per_min'],
                'age_min': exercise['age_min'],
                'age_max': exercise['age_max'],
                'imc_min': exercise['imc_min'],
                'imc_max': exercise['imc_max']
            })
            exercise_id += 1
        
        # FLEXIBILITY - 10 exercices
        flexibility_exercises = [
            {'name': 'Étirements doux', 'difficulty': 1, 'duration_min': 5, 'duration_max': 20, 'calories_per_min': 2.0, 'age_min': 16, 'age_max': 90, 'imc_min': 15, 'imc_max': 45},
            {'name': 'Yoga débutant', 'difficulty': 3, 'duration_min': 15, 'duration_max': 45, 'calories_per_min': 3.0, 'age_min': 18, 'age_max': 80, 'imc_min': 16, 'imc_max': 40},
            {'name': 'Pilates débutant', 'difficulty': 4, 'duration_min': 15, 'duration_max': 45, 'calories_per_min': 4.0, 'age_min': 18, 'age_max': 70, 'imc_min': 16, 'imc_max': 35},
            {'name': 'Yoga Hatha', 'difficulty': 5, 'duration_min': 20, 'duration_max': 60, 'calories_per_min': 3.5, 'age_min': 18, 'age_max': 75, 'imc_min': 16, 'imc_max': 35},
            {'name': 'Yoga Vinyasa', 'difficulty': 6, 'duration_min': 25, 'duration_max': 75, 'calories_per_min': 5.0, 'age_min': 20, 'age_max': 65, 'imc_min': 18, 'imc_max': 32},
            {'name': 'Pilates intermédiaire', 'difficulty': 6, 'duration_min': 20, 'duration_max': 50, 'calories_per_min': 5.5, 'age_min': 20, 'age_max': 65, 'imc_min': 18, 'imc_max': 30},
            {'name': 'Yoga thérapeutique', 'difficulty': 2, 'duration_min': 20, 'duration_max': 60, 'calories_per_min': 2.5, 'age_min': 20, 'age_max': 85, 'imc_min': 16, 'imc_max': 40},
            {'name': 'Stretching dynamique', 'difficulty': 4, 'duration_min': 10, 'duration_max': 30, 'calories_per_min': 4.5, 'age_min': 18, 'age_max': 65, 'imc_min': 16, 'imc_max': 32},
            {'name': 'Yoga avancé', 'difficulty': 8, 'duration_min': 30, 'duration_max': 90, 'calories_per_min': 6.0, 'age_min': 20, 'age_max': 55, 'imc_min': 18, 'imc_max': 28},
            {'name': 'Pilates avancé', 'difficulty': 8, 'duration_min': 25, 'duration_max': 60, 'calories_per_min': 7.0, 'age_min': 20, 'age_max': 55, 'imc_min': 18, 'imc_max': 26}
        ]
        
        for exercise in flexibility_exercises:
            exercises.append({
                'id': exercise_id,
                'name': exercise['name'],
                'type': 'flexibility',
                'category': self._get_category_from_difficulty(exercise['difficulty']),
                'difficulty_level': exercise['difficulty'],
                'duration_min': exercise['duration_min'],
                'duration_max': exercise['duration_max'],
                'calories_per_minute': exercise['calories_per_min'],
                'age_min': exercise['age_min'],
                'age_max': exercise['age_max'],
                'imc_min': exercise['imc_min'],
                'imc_max': exercise['imc_max']
            })
            exercise_id += 1
        
        # BALANCE - 5 exercices
        balance_exercises = [
            {'name': 'Tai Chi débutant', 'difficulty': 2, 'duration_min': 10, 'duration_max': 30, 'calories_per_min': 3.5, 'age_min': 20, 'age_max': 85, 'imc_min': 16, 'imc_max': 40},
            {'name': 'Exercices équilibre', 'difficulty': 3, 'duration_min': 5, 'duration_max': 15, 'calories_per_min': 4.0, 'age_min': 18, 'age_max': 80, 'imc_min': 16, 'imc_max': 35},
            {'name': 'Qi Gong', 'difficulty': 4, 'duration_min': 15, 'duration_max': 45, 'calories_per_min': 3.0, 'age_min': 20, 'age_max': 80, 'imc_min': 16, 'imc_max': 38},
            {'name': 'Proprioception', 'difficulty': 5, 'duration_min': 10, 'duration_max': 25, 'calories_per_min': 4.5, 'age_min': 18, 'age_max': 70, 'imc_min': 18, 'imc_max': 32},
            {'name': 'Tai Chi avancé', 'difficulty': 7, 'duration_min': 20, 'duration_max': 60, 'calories_per_min': 5.0, 'age_min': 25, 'age_max': 70, 'imc_min': 18, 'imc_max': 30}
        ]
        
        for exercise in balance_exercises:
            exercises.append({
                'id': exercise_id,
                'name': exercise['name'],
                'type': 'balance',
                'category': self._get_category_from_difficulty(exercise['difficulty']),
                'difficulty_level': exercise['difficulty'],
                'duration_min': exercise['duration_min'],
                'duration_max': exercise['duration_max'],
                'calories_per_minute': exercise['calories_per_min'],
                'age_min': exercise['age_min'],
                'age_max': exercise['age_max'],
                'imc_min': exercise['imc_min'],
                'imc_max': exercise['imc_max']
            })
            exercise_id += 1
        
        # Sauvegarder en CSV
        df_exercises = pd.DataFrame(exercises)
        df_exercises.to_csv(f'{self.output_dir}/exercises_catalog.csv', index=False)
        
        print(f"✅ Catalogue d'exercices créé : {len(exercises)} exercices")
        return df_exercises
    
    def _get_category_from_difficulty(self, difficulty):
        """Détermine la catégorie selon la difficulté"""
        if difficulty <= 4:
            return 'beginner'
        elif difficulty <= 7:
            return 'intermediate'
        else:
            return 'advanced'
    
    def generate_massive_users_dataset(self):
        """Génère 40K profils utilisateurs diversifiés"""
        print(f"👥 Génération de {self.n_users} profils utilisateurs...")
        
        users = []
        
        # Distribution réaliste des âges
        age_ranges = [
            (16, 25, 0.25),  # 25% jeunes
            (26, 40, 0.35),  # 35% adultes jeunes
            (41, 55, 0.25),  # 25% adultes
            (56, 75, 0.15)   # 15% seniors
        ]
        
        # Distribution réaliste des IMC
        imc_ranges = [
            (16.0, 18.5, 0.05),  # 5% maigreur
            (18.5, 25.0, 0.50),  # 50% normal
            (25.0, 30.0, 0.30),  # 30% surpoids
            (30.0, 40.0, 0.15)   # 15% obésité
        ]
        
        for i in range(self.n_users):
            # Sélection aléatoire pondérée de l'âge
            age_range = np.random.choice(
                len(age_ranges), 
                p=[weight for _, _, weight in age_ranges]
            )
            min_age, max_age, _ = age_ranges[age_range]
            age = random.randint(min_age, max_age)
            
            # Sélection aléatoire pondérée de l'IMC
            imc_range = np.random.choice(
                len(imc_ranges),
                p=[weight for _, _, weight in imc_ranges]
            )
            min_imc, max_imc, _ = imc_ranges[imc_range]
            imc = round(random.uniform(min_imc, max_imc), 1)
            
            # Calcul taille/poids cohérents
            height = random.randint(150, 200)
            weight = round((imc * (height/100) ** 2), 1)
            
            # Détermination du niveau de fitness
            fitness_level = self._determine_fitness_level(age, imc)
            
            # Fréquence d'activité cohérente
            activity_frequency = self._determine_activity_frequency(age, imc, fitness_level)
            
            # Contraintes médicales selon l'âge et l'IMC
            has_constraints = self._has_medical_constraints(age, imc)
            
            users.append({
                'user_id': i + 1,
                'age': age,
                'height': height,
                'weight': weight,
                'imc': imc,
                'imc_category': self._get_imc_category(imc),
                'gender': random.choice(['male', 'female']),
                'fitness_level': fitness_level,
                'activity_frequency': activity_frequency,
                'has_medical_constraints': has_constraints,
                'preferred_exercise_types': self._get_preferred_types(age, imc, fitness_level),
                'available_time_per_session': random.choice([15, 20, 30, 45, 60, 90]),
                'equipment_access': random.choice(['none', 'basic', 'gym']),
                'experience_months': self._calculate_experience(fitness_level, activity_frequency)
            })
            
            if (i + 1) % 5000 == 0:
                print(f"   📊 {i + 1}/{self.n_users} utilisateurs générés...")
        
        # Sauvegarder
        df_users = pd.DataFrame(users)
        df_users.to_csv(f'{self.output_dir}/users_profiles.csv', index=False)
        
        print(f"✅ Dataset utilisateurs créé : {len(users)} profils")
        self._display_user_stats(df_users)
        
        return df_users
    
    def _determine_fitness_level(self, age, imc):
        """Détermine le niveau de fitness de façon réaliste"""
        score = 0
        
        # Score âge (plus jeune = plus de chance d'être fit)
        if age < 30:
            score += random.choice([2, 3, 3])
        elif age < 50:
            score += random.choice([1, 2, 2])
        else:
            score += random.choice([0, 1, 1])
        
        # Score IMC
        if 18.5 <= imc <= 25:
            score += random.choice([2, 3])
        elif 25 < imc <= 30:
            score += random.choice([0, 1, 2])
        else:
            score += random.choice([0, 0, 1])
        
        # Variabilité aléatoire
        score += random.choice([-1, 0, 1])
        
        if score >= 4:
            return 'advanced'
        elif score >= 2:
            return 'intermediate'
        else:
            return 'beginner'
    
    def _determine_activity_frequency(self, age, imc, fitness_level):
        """Détermine la fréquence d'activité hebdomadaire"""
        base_freq = {
            'beginner': [0, 1, 2, 2, 3],
            'intermediate': [2, 3, 4, 4, 5, 6],
            'advanced': [4, 5, 6, 7, 8, 9, 10]
        }
        
        freq = random.choice(base_freq[fitness_level])
        
        # Ajustements
        if age > 60:
            freq = max(0, freq - 1)
        if imc > 30:
            freq = max(0, freq - 2)
        
        return min(10, freq)
    
    def _has_medical_constraints(self, age, imc):
        """Détermine si l'utilisateur a des contraintes médicales"""
        risk_factors = 0
        
        if age > 65:
            risk_factors += 2
        elif age > 50:
            risk_factors += 1
        
        if imc > 35:
            risk_factors += 2
        elif imc > 30:
            risk_factors += 1
        elif imc < 18:
            risk_factors += 1
        
        # Probabilité de contraintes
        probability = min(0.7, risk_factors * 0.15)
        return random.random() < probability
    
    def _get_preferred_types(self, age, imc, fitness_level):
        """Détermine les types d'exercices préférés"""
        preferences = []
        
        if age > 60:
            preferences.extend(['flexibility', 'balance'])
        elif age < 30:
            preferences.extend(['cardio', 'strength'])
        else:
            preferences.extend(['cardio'])
        
        if imc > 25:
            preferences.append('cardio')
        
        if fitness_level == 'advanced':
            preferences.extend(['strength', 'cardio'])
        elif fitness_level == 'beginner':
            preferences.extend(['flexibility'])
        
        return list(set(preferences)) if preferences else ['cardio']
    
    def _calculate_experience(self, fitness_level, activity_frequency):
        """Calcule l'expérience en mois"""
        base_months = {
            'beginner': random.randint(0, 6),
            'intermediate': random.randint(6, 24),
            'advanced': random.randint(12, 60)
        }
        
        months = base_months[fitness_level]
        
        # Ajustement selon la fréquence
        if activity_frequency > 6:
            months += random.randint(6, 12)
        
        return months
    
    def _get_imc_category(self, imc):
        """Catégorie IMC"""
        if imc < 16.5:
            return 'Dénutrition'
        elif imc < 18.5:
            return 'Maigreur'
        elif imc < 25:
            return 'Corpulence normale'
        elif imc < 30:
            return 'Surpoids'
        elif imc < 35:
            return 'Obésité modérée'
        elif imc < 40:
            return 'Obésité sévère'
        else:
            return 'Obésité morbide'
    
    def _display_user_stats(self, df_users):
        """Affiche les statistiques du dataset"""
        print(f"\n📊 STATISTIQUES DU DATASET :")
        print(f"   Total utilisateurs : {len(df_users):,}")
        
        print(f"\n🎂 Répartition par âge :")
        age_stats = df_users.groupby(pd.cut(df_users['age'], bins=[15, 25, 40, 55, 75], labels=['16-25', '26-40', '41-55', '56-75'])).size()
        for age_group, count in age_stats.items():
            print(f"   {age_group}: {count:,} ({count/len(df_users)*100:.1f}%)")
        
        print(f"\n⚖️ Répartition par IMC :")
        imc_stats = df_users['imc_category'].value_counts()
        for category, count in imc_stats.items():
            print(f"   {category}: {count:,} ({count/len(df_users)*100:.1f}%)")
        
        print(f"\n💪 Répartition par niveau :")
        fitness_stats = df_users['fitness_level'].value_counts()
        for level, count in fitness_stats.items():
            print(f"   {level}: {count:,} ({count/len(df_users)*100:.1f}%)")
    
    def generate_training_data(self, df_users, df_exercises):
        """Génère les données d'entraînement (user-exercise combinations)"""
        print("🎯 Génération des données d'entraînement...")
        
        training_data = []
        total_combinations = len(df_users) * len(df_exercises)
        processed = 0
        
        for _, user in df_users.iterrows():
            for _, exercise in df_exercises.iterrows():
                # Vérifier la compatibilité
                if self._is_compatible(user, exercise):
                    # Calculer le score de recommandation
                    score = self._calculate_advanced_recommendation_score(user, exercise)
                    duration = self._calculate_recommended_duration(exercise, user['fitness_level'])
                    
                    training_data.append({
                        'user_id': user['user_id'],
                        'exercise_id': exercise['id'],
                        'user_age': user['age'],
                        'user_imc': user['imc'],
                        'user_fitness_level': user['fitness_level'],
                        'user_activity_frequency': user['activity_frequency'],
                        'user_has_constraints': user['has_medical_constraints'],
                        'user_experience_months': user['experience_months'],
                        'exercise_name': exercise['name'],
                        'exercise_type': exercise['type'],
                        'exercise_category': exercise['category'],
                        'exercise_difficulty': exercise['difficulty_level'],
                        'exercise_duration_min': exercise['duration_min'],
                        'exercise_duration_max': exercise['duration_max'],
                        'exercise_calories_per_min': exercise['calories_per_minute'],
                        'recommendation_score': score,
                        'recommended_duration': duration,
                        'is_highly_recommended': 1 if score >= 75 else 0,
                        'is_recommended': 1 if score >= 60 else 0,
                        'compatibility_score': self._calculate_compatibility_score(user, exercise)
                    })
                
                processed += 1
                if processed % 100000 == 0:
                    print(f"   📊 {processed:,}/{total_combinations:,} combinaisons traitées...")
        
        # Sauvegarder
        df_training = pd.DataFrame(training_data)
        df_training.to_csv(f'{self.output_dir}/training_data.csv', index=False)
        
        print(f"✅ Dataset d'entraînement créé : {len(training_data):,} échantillons")
        self._display_training_stats(df_training)
        
        return df_training
    
    def _is_compatible(self, user, exercise):
        """Vérifie la compatibilité user-exercise"""
        # Vérifications de base
        if (user['age'] < exercise['age_min'] or 
            user['age'] > exercise['age_max'] or
            user['imc'] < exercise['imc_min'] or 
            user['imc'] > exercise['imc_max']):
            return False
        
        # Vérifications avancées
        if user['has_medical_constraints']:
            # Limiter les exercices intenses pour les personnes avec contraintes
            if exercise['difficulty_level'] > 7:
                return False
        
        return True
    
    def _calculate_advanced_recommendation_score(self, user, exercise):
        """Calcul avancé du score de recommandation"""
        score = 50  # Score de base
        
        # 1. Compatibilité âge-type d'exercice (20 points max)
        age_type_bonus = self._get_age_type_bonus(user['age'], exercise['type'])
        score += age_type_bonus
        
        # 2. Compatibilité IMC-type d'exercice (15 points max)
        imc_type_bonus = self._get_imc_type_bonus(user['imc'], exercise)
        score += imc_type_bonus
        
        # 3. Correspondance niveau de fitness (15 points max)
        fitness_bonus = self._get_fitness_level_bonus(user['fitness_level'], exercise['difficulty_level'])
        score += fitness_bonus
        
        # 4. Fréquence d'activité (10 points max)
        frequency_bonus = self._get_frequency_bonus(user['activity_frequency'], exercise)
        score += frequency_bonus
        
        # 5. Expérience utilisateur (10 points max)
        experience_bonus = self._get_experience_bonus(user['experience_months'], exercise)
        score += experience_bonus
        
        # 6. Préférences types d'exercices (10 points max)
        if exercise['type'] in user['preferred_exercise_types']:
            score += 10
        
        # 7. Pénalités pour contraintes médicales
        if user['has_medical_constraints']:
            if exercise['difficulty_level'] > 6:
                score -= 15
            elif exercise['type'] in ['flexibility', 'balance']:
                score += 5
        
        # 8. Bonus durée compatible
        if (exercise['duration_min'] <= user['available_time_per_session'] <= exercise['duration_max']):
            score += 8
        elif exercise['duration_min'] > user['available_time_per_session']:
            score -= 10
        
        # 9. Variabilité naturelle
        score += random.uniform(-3, 3)
        
        # Normalisation
        return max(0, min(100, score))
    
    def _get_age_type_bonus(self, age, exercise_type):
        """Bonus selon l'âge et le type d'exercice"""
        bonuses = {
            'cardio': {
                (16, 30): 15,
                (31, 50): 10,
                (51, 65): 8,
                (66, 80): 5
            },
            'strength': {
                (16, 35): 12,
                (36, 55): 10,
                (56, 70): 6,
                (71, 80): 3
            },
            'flexibility': {
                (16, 30): 8,
                (31, 50): 12,
                (51, 70): 15,
                (71, 80): 20
            },
            'balance': {
                (16, 40): 5,
                (41, 60): 10,
                (61, 80): 18
            }
        }
        
        type_bonuses = bonuses.get(exercise_type, {})
        for (min_age, max_age), bonus in type_bonuses.items():
            if min_age <= age <= max_age:
                return bonus
        return 0
    
    def _get_imc_type_bonus(self, imc, exercise):
        """Bonus selon l'IMC et l'exercice"""
        bonus = 0
        
        if imc < 18.5:  # Maigreur
            if exercise['type'] == 'strength':
                bonus += 12
            elif exercise['difficulty_level'] > 6:
                bonus -= 8
        elif 18.5 <= imc <= 25:  # Normal
            bonus += 8  # Bonus général
        elif 25 < imc <= 30:  # Surpoids
            if exercise['type'] == 'cardio':
                bonus += 15
            elif exercise['difficulty_level'] <= 5:
                bonus += 5
            elif exercise['difficulty_level'] > 7:
                bonus -= 5
        else:  # Obésité
            if exercise['type'] == 'cardio' and exercise['difficulty_level'] <= 4:
                bonus += 18
            elif exercise['type'] == 'flexibility':
                bonus += 10
            elif exercise['difficulty_level'] > 6:
                bonus -= 15
        
        return bonus
    
    def _get_fitness_level_bonus(self, fitness_level, difficulty):
        """Bonus selon la correspondance niveau/difficulté"""
        level_ranges = {
            'beginner': (1, 4),
            'intermediate': (4, 7),
            'advanced': (7, 10)
        }
        
        min_diff, max_diff = level_ranges[fitness_level]
        
        if min_diff <= difficulty <= max_diff:
            return 15
        elif abs(difficulty - (min_diff + max_diff)/2) <= 1:
            return 10
        elif abs(difficulty - (min_diff + max_diff)/2) <= 2:
            return 5
        else:
            return -10
    
    def _get_frequency_bonus(self, frequency, exercise):
        """Bonus selon la fréquence d'activité"""
        if frequency >= 7:  # Très actif
            if exercise['difficulty_level'] >= 6:
                return 8
            else:
                return 3
        elif frequency >= 4:  # Actif
            if 4 <= exercise['difficulty_level'] <= 7:
                return 6
            else:
                return 2
        elif frequency >= 2:  # Modérément actif
            if exercise['difficulty_level'] <= 5:
                return 5
            else:
                return -3
        else:  # Sédentaire
            if exercise['difficulty_level'] <= 3:
                return 8
            else:
                return -8
    
    def _get_experience_bonus(self, months, exercise):
        """Bonus selon l'expérience"""
        if months >= 24:  # Expérimenté
            if exercise['difficulty_level'] >= 6:
                return 8
        elif months >= 6:  # Intermédiaire
            if 3 <= exercise['difficulty_level'] <= 7:
                return 6
        else:  # Débutant
            if exercise['difficulty_level'] <= 4:
                return 8
            else:
                return -5
        return 0
    
    def _calculate_compatibility_score(self, user, exercise):
        """Score de compatibilité général"""
        score = 0
        
        # Compatibilité âge (0-25)
        age_range = exercise['age_max'] - exercise['age_min']
        age_position = (user['age'] - exercise['age_min']) / age_range
        score += 25 * (1 - abs(age_position - 0.5) * 2)
        
        # Compatibilité IMC (0-25)
        imc_range = exercise['imc_max'] - exercise['imc_min']
        imc_position = (user['imc'] - exercise['imc_min']) / imc_range
        score += 25 * (1 - abs(imc_position - 0.5) * 2)
        
        # Compatibilité niveau (0-50)
        level_mapping = {'beginner': 2, 'intermediate': 5, 'advanced': 8}
        user_level_score = level_mapping[user['fitness_level']]
        level_diff = abs(user_level_score - exercise['difficulty_level'])
        score += max(0, 50 - level_diff * 8)
        
        return max(0, min(100, score))
    
    def _calculate_recommended_duration(self, exercise, fitness_level):
        """Calcule la durée recommandée"""
        min_dur = exercise['duration_min']
        max_dur = exercise['duration_max']
        
        multipliers = {
            'beginner': 0.4,
            'intermediate': 0.7,
            'advanced': 0.9
        }
        
        multiplier = multipliers[fitness_level]
        duration = min_dur + (max_dur - min_dur) * multiplier
        
        return round(duration)
    
    def _display_training_stats(self, df_training):
        """Affiche les statistiques du dataset d'entraînement"""
        print(f"\n📊 STATISTIQUES DATASET D'ENTRAÎNEMENT :")
        print(f"   Total échantillons : {len(df_training):,}")
        
        print(f"\n🎯 Répartition des scores :")
        score_ranges = [(0, 40), (40, 60), (60, 75), (75, 90), (90, 100)]
        for min_score, max_score in score_ranges:
            count = len(df_training[(df_training['recommendation_score'] >= min_score) & 
                                  (df_training['recommendation_score'] < max_score)])
            print(f"   {min_score}-{max_score}: {count:,} ({count/len(df_training)*100:.1f}%)")
        
        print(f"\n🏃‍♂️ Répartition par type d'exercice :")
        type_stats = df_training['exercise_type'].value_counts()
        for ex_type, count in type_stats.items():
            print(f"   {ex_type}: {count:,} ({count/len(df_training)*100:.1f}%)")
        
        print(f"\n⭐ Recommandations hautement recommandées: {df_training['is_highly_recommended'].sum():,}")
        print(f"✅ Recommandations recommandées: {df_training['is_recommended'].sum():,}")
    
    def generate_all_datasets(self):
        """Génère tous les datasets"""
        print("🚀 GÉNÉRATION COMPLÈTE DU DATASET MASSIF")
        print("=" * 60)
        
        start_time = pd.Timestamp.now()
        
        # 1. Générer le catalogue d'exercices
        print("\n1️⃣ GÉNÉRATION DU CATALOGUE D'EXERCICES")
        df_exercises = self.generate_comprehensive_exercises_catalog()
        
        # 2. Générer les profils utilisateurs
        print(f"\n2️⃣ GÉNÉRATION DE {self.n_users:,} PROFILS UTILISATEURS")
        df_users = self.generate_massive_users_dataset()
        
        # 3. Générer les données d'entraînement
        print(f"\n3️⃣ GÉNÉRATION DES DONNÉES D'ENTRAÎNEMENT")
        df_training = self.generate_training_data(df_users, df_exercises)
        
        end_time = pd.Timestamp.now()
        duration = end_time - start_time
        
        print("\n" + "=" * 60)
        print("🎉 GÉNÉRATION TERMINÉE !")
        print("=" * 60)
        print(f"⏱️  Temps total : {duration}")
        print(f"📁 Fichiers générés dans : {self.output_dir}/")
        print(f"   - users_profiles.csv ({len(df_users):,} profils)")
        print(f"   - exercises_catalog.csv ({len(df_exercises)} exercices)")
        print(f"   - training_data.csv ({len(df_training):,} échantillons)")
        
        # Calcul de la taille totale
        total_size = 0
        for file in ['users_profiles.csv', 'exercises_catalog.csv', 'training_data.csv']:
            filepath = f'{self.output_dir}/{file}'
            if os.path.exists(filepath):
                size = os.path.getsize(filepath) / (1024 * 1024)  # MB
                total_size += size
                print(f"   - {file}: {size:.1f} MB")
        
        print(f"💾 Taille totale : {total_size:.1f} MB")
        
        return df_users, df_exercises, df_training

if __name__ == "__main__":
    generator = MassiveDatasetGenerator()
    df_users, df_exercises, df_training = generator.generate_all_datasets()
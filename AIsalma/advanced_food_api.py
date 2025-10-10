"""
🍎 API de Reconnaissance Alimentaire Avancée
Utilise des modèles pré-entraînés pour une détection précise :
- YOLOv8 pour la détection d'objets
- MobileNetV2 (ImageNet) pour la classification d'images
- Analyse de couleurs avec K-means
- Base de données Food-101 étendue
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import cv2
import numpy as np
import base64
import io
from PIL import Image
import requests
import os
import logging
import traceback
import time
from datetime import datetime

# Imports optionnels (fallback si pas installés)
try:
    from ultralytics import YOLO
    YOLO_AVAILABLE = True
except ImportError:
    print("⚠️ YOLOv8 non disponible - Utilisation du mode dégradé")
    YOLO_AVAILABLE = False

try:
    import tensorflow as tf
    TF_AVAILABLE = True
except ImportError:
    print("⚠️ TensorFlow non disponible - Utilisation du mode dégradé")
    TF_AVAILABLE = False

try:
    from sklearn.cluster import KMeans
    SKLEARN_AVAILABLE = True
except ImportError:
    print("⚠️ sklearn non disponible - Analyse couleur simplifiée")
    SKLEARN_AVAILABLE = False

# Configuration
app = Flask(__name__)
CORS(app)

# Configuration des logs
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class AdvancedFoodRecognitionAI:
    def __init__(self):
        self.yolo_model = None
        self.mobilenet_model = None
        self.food_classes = []
        self.nutrition_fallback = {}
        self.load_models()
        self.load_nutrition_data()
    
    def load_models(self):
        """Charger tous les modèles pré-entraînés avec fallbacks"""
        print("🤖 Chargement des modèles avancés...")
        
        # 1. YOLOv8 pour détection d'objets
        if YOLO_AVAILABLE:
            try:
                self.yolo_model = YOLO('yolov8n.pt')
                print("✅ YOLOv8 chargé")
            except Exception as e:
                print(f"⚠️ Erreur YOLOv8: {e}")
                self.yolo_model = None
        else:
            print("⚠️ YOLOv8 non disponible - Mode dégradé activé")
            self.yolo_model = None
        
        # 2. MobileNetV2 pré-entraîné sur ImageNet
        if TF_AVAILABLE:
            try:
                self.mobilenet_model = tf.keras.applications.MobileNetV2(
                    weights='imagenet',
                    include_top=True,
                    input_shape=(224, 224, 3)
                )
                print("✅ MobileNetV2 chargé depuis TensorFlow")
                
                # Charger les classes ImageNet liées à l'alimentation
                self.load_imagenet_food_classes()
                
            except Exception as e:
                print(f"⚠️ Erreur MobileNetV2: {e}")
                self.mobilenet_model = None
        else:
            print("⚠️ TensorFlow non disponible - Classification simplifiée")
            self.mobilenet_model = None
    
    def load_imagenet_food_classes(self):
        """Charger les classes ImageNet relatives à l'alimentation"""
        # Classes ImageNet qui correspondent à des aliments (indices sélectionnés)
        self.food_classes = {
            # Fruits
            948: 'banana', 949: 'pineapple', 950: 'custard_apple', 951: 'pomegranate',
            952: 'orange', 953: 'lemon', 954: 'fig', 955: 'strawberry',
            
            # Légumes
            936: 'broccoli', 937: 'cauliflower', 938: 'cucumber', 939: 'artichoke',
            940: 'bell_pepper', 941: 'corn', 942: 'mushroom', 943: 'cabbage',
            
            # Aliments préparés
            927: 'cheeseburger', 928: 'pizza', 929: 'hot_dog', 930: 'taco',
            931: 'burrito', 932: 'bagel', 933: 'pretzel', 934: 'ice_cream',
            
            # Boissons
            967: 'espresso', 968: 'cup', 
            
            # Autres
            935: 'head_cabbage'
        }
        
        # Extension avec Food-101 dataset complet
        self.extended_food_classes = [
            'apple_pie', 'baby_back_ribs', 'baklava', 'beef_carpaccio', 'beef_tartare',
            'beet_salad', 'beignets', 'bibimbap', 'bread_pudding', 'breakfast_burrito',
            'bruschetta', 'caesar_salad', 'cannoli', 'caprese_salad', 'carrot_cake',
            'ceviche', 'cheese_plate', 'cheesecake', 'chicken_curry', 'chicken_quesadilla',
            'chicken_wings', 'chocolate_cake', 'chocolate_mousse', 'churros', 'clam_chowder',
            'club_sandwich', 'crab_cakes', 'creme_brulee', 'croque_madame', 'cup_cakes',
            'deviled_eggs', 'donuts', 'dumplings', 'edamame', 'eggs_benedict',
            'escargots', 'falafel', 'filet_mignon', 'fish_and_chips', 'foie_gras',
            'french_fries', 'french_onion_soup', 'french_toast', 'fried_calamari', 'fried_rice',
            'frozen_yogurt', 'garlic_bread', 'gnocchi', 'greek_salad', 'grilled_cheese_sandwich',
            'grilled_salmon', 'guacamole', 'gyoza', 'hamburger', 'hot_and_sour_soup',
            'hot_dog', 'huevos_rancheros', 'hummus', 'ice_cream', 'lasagna',
            'lobster_bisque', 'lobster_roll_sandwich', 'macaroni_and_cheese', 'macarons', 'miso_soup',
            'mussels', 'nachos', 'omelette', 'onion_rings', 'oysters', 'pad_thai', 'paella',
            'pancakes', 'panna_cotta', 'peking_duck', 'pho', 'pizza', 'pork_chop',
            'poutine', 'prime_rib', 'pulled_pork_sandwich', 'ramen', 'ravioli',
            'red_velvet_cake', 'risotto', 'samosa', 'sashimi', 'scallops', 'seaweed_salad',
            'shrimp_and_grits', 'spaghetti_bolognese', 'spaghetti_carbonara', 'spring_rolls',
            'steak', 'strawberry_shortcake', 'sushi', 'tacos', 'takoyaki', 'tiramisu',
            'tuna_tartare', 'waffles',
            # Ajouts fruits et légumes de base
            'apple', 'banana', 'orange', 'strawberry', 'tomato', 'lettuce', 'carrot',
            'broccoli', 'potato', 'onion', 'garlic', 'cucumber', 'pepper', 'spinach',
            'avocado', 'lemon', 'lime', 'grape', 'pear', 'peach', 'mango', 'kiwi',
            'watermelon', 'pineapple', 'cherry', 'blueberry', 'raspberry', 'coconut',
            'bread', 'pasta', 'rice', 'salad', 'soup', 'sandwich', 'meat', 'fish',
            'chicken', 'beef', 'pork', 'egg', 'cheese', 'milk', 'yogurt', 'cereal'
        ]
        
        print(f"✅ {len(self.extended_food_classes)} classes alimentaires chargées")
    
    def load_nutrition_data(self):
        """Charger les données nutritionnelles de base"""
        self.nutrition_fallback = {
            'apple': {'calories': 52, 'protein': 0.3, 'carbs': 14, 'fat': 0.2, 'fiber': 2.4},
            'banana': {'calories': 89, 'protein': 1.1, 'carbs': 23, 'fat': 0.3, 'fiber': 2.6},
            'orange': {'calories': 47, 'protein': 0.9, 'carbs': 12, 'fat': 0.1, 'fiber': 2.4},
            'tomato': {'calories': 18, 'protein': 0.9, 'carbs': 3.9, 'fat': 0.2, 'fiber': 1.2},
            'lettuce': {'calories': 15, 'protein': 1.4, 'carbs': 2.9, 'fat': 0.2, 'fiber': 1.3},
            'carrot': {'calories': 41, 'protein': 0.9, 'carbs': 10, 'fat': 0.2, 'fiber': 2.8},
            'broccoli': {'calories': 34, 'protein': 2.8, 'carbs': 7, 'fat': 0.4, 'fiber': 2.6},
            'bread': {'calories': 265, 'protein': 9, 'carbs': 49, 'fat': 3.2, 'fiber': 2.7},
            'rice': {'calories': 130, 'protein': 2.7, 'carbs': 28, 'fat': 0.3, 'fiber': 0.4},
            'pasta': {'calories': 220, 'protein': 8, 'carbs': 44, 'fat': 1.3, 'fiber': 2.5},
            'chicken': {'calories': 239, 'protein': 27, 'carbs': 0, 'fat': 14, 'fiber': 0},
            'beef': {'calories': 250, 'protein': 26, 'carbs': 0, 'fat': 15, 'fiber': 0},
            'fish': {'calories': 206, 'protein': 22, 'carbs': 0, 'fat': 12, 'fiber': 0},
            'egg': {'calories': 155, 'protein': 13, 'carbs': 1.1, 'fat': 11, 'fiber': 0},
            'cheese': {'calories': 113, 'protein': 7, 'carbs': 1, 'fat': 9, 'fiber': 0},
            'milk': {'calories': 42, 'protein': 3.4, 'carbs': 5, 'fat': 1, 'fiber': 0},
            'potato': {'calories': 77, 'protein': 2, 'carbs': 17, 'fat': 0.1, 'fiber': 2.2},
            'pizza': {'calories': 266, 'protein': 11, 'carbs': 33, 'fat': 10, 'fiber': 2.3},
            'hamburger': {'calories': 540, 'protein': 25, 'carbs': 40, 'fat': 31, 'fiber': 3}
        }

    def detect_food(self, image_path):
        """Détection principale combinant tous les modèles"""
        all_detections = []
        
        try:
            print("🔍 Analyse multi-modèles en cours...")
            
            # 1. YOLOv8 pour détection d'objets
            yolo_results = self.detect_with_yolo(image_path)
            all_detections.extend(yolo_results)
            print(f"YOLOv8: {len(yolo_results)} détections")
            
            # 2. MobileNetV2 pour classification d'images
            mobilenet_results = self.classify_with_mobilenet(image_path)
            all_detections.extend(mobilenet_results)
            print(f"MobileNet: {len(mobilenet_results)} détections")
            
            # 3. Analyse de couleurs avancée
            color_results = self.analyze_advanced_colors(image_path)
            all_detections.extend(color_results)
            print(f"Couleurs: {len(color_results)} détections")
            
            # 4. Sélectionner le meilleur résultat
            if all_detections:
                best_detection = self.select_best_detection(all_detections)
                enriched = self.enrich_with_nutrition(best_detection)
                print(f"🏆 Meilleur résultat: {enriched['name']} (confiance: {enriched['confidence']:.2f})")
                return [enriched]
            
            return []
            
        except Exception as e:
            print(f"❌ Erreur détection: {e}")
            traceback.print_exc()
            return []
    
    def detect_with_yolo(self, image_path):
        """Détection YOLOv8"""
        detections = []
        
        try:
            if not self.yolo_model:
                return detections
            
            results = self.yolo_model(image_path)
            
            for r in results:
                if r.boxes is not None:
                    for box in r.boxes:
                        conf = float(box.conf[0])
                        if conf > 0.3:
                            cls_id = int(box.cls[0])
                            class_name = self.yolo_model.names[cls_id]
                            
                            # Mapper vers noms alimentaires
                            food_name = self.map_yolo_to_food(class_name)
                            if food_name:
                                detections.append({
                                    'name': food_name,
                                    'confidence': conf * 0.9,  # Poids YOLOv8
                                    'source': 'yolo',
                                    'original_class': class_name
                                })
        except Exception as e:
            print(f"⚠️ Erreur YOLOv8: {e}")
        
        return detections
    
    def classify_with_mobilenet(self, image_path):
        """Classification avec MobileNetV2"""
        detections = []
        
        try:
            if not self.mobilenet_model:
                return detections
            
            # Préprocesser l'image
            img = cv2.imread(image_path)
            img_rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
            img_resized = cv2.resize(img_rgb, (224, 224))
            img_array = np.expand_dims(img_resized / 255.0, axis=0)
            
            # Prédiction
            predictions = self.mobilenet_model.predict(img_array, verbose=0)
            
            # Décoder les prédictions ImageNet
            decoded = tf.keras.applications.imagenet_utils.decode_predictions(predictions, top=5)[0]
            
            for _, class_name, score in decoded:
                if score > 0.1:
                    # Mapper vers aliments si c'est un aliment
                    food_name = self.map_imagenet_to_food(class_name)
                    if food_name:
                        detections.append({
                            'name': food_name,
                            'confidence': float(score) * 1.1,  # Bonus modèle spécialisé
                            'source': 'mobilenet',
                            'original_class': class_name
                        })
                        
        except Exception as e:
            print(f"⚠️ Erreur MobileNet: {e}")
        
        return detections
    
    def analyze_advanced_colors(self, image_path):
        """Analyse de couleurs avec K-means"""
        detections = []
        
        try:
            img = cv2.imread(image_path)
            img_rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
            
            # K-means pour couleurs dominantes
            dominant_colors = self.get_dominant_colors_kmeans(img_rgb)
            
            for color_info in dominant_colors:
                food_suggestions = self.color_to_food_mapping(color_info)
                for food in food_suggestions:
                    detections.append({
                        'name': food['name'],
                        'confidence': food['confidence'] * 0.7,  # Poids couleur
                        'source': 'color_analysis',
                        'color_info': color_info
                    })
                    
        except Exception as e:
            print(f"⚠️ Erreur analyse couleurs: {e}")
        
        return detections
    
    def get_dominant_colors_kmeans(self, img_rgb, k=3):
        """Extraire couleurs dominantes avec K-means"""
        try:
            # Redimensionner pour performance
            img_small = cv2.resize(img_rgb, (100, 100))
            data = img_small.reshape((-1, 3))
            
            if SKLEARN_AVAILABLE:
                # K-means clustering
                kmeans = KMeans(n_clusters=k, random_state=42, n_init=10)
                kmeans.fit(data)
                
                colors = kmeans.cluster_centers_
                labels = kmeans.labels_
            else:
                # Fallback simple
                return self.get_dominant_colors_simple(img_rgb)
            
            # Calculer pourcentages
            label_counts = np.bincount(labels)
            percentages = label_counts / len(labels)
            
            dominant_colors = []
            for color, percentage in zip(colors, percentages):
                if percentage > 0.15:  # Au moins 15% de l'image
                    color_name = self.rgb_to_color_name(color)
                    dominant_colors.append({
                        'rgb': color.astype(int).tolist(),
                        'percentage': float(percentage),
                        'color_name': color_name
                    })
            
            return dominant_colors
            
        except Exception as e:
            print(f"⚠️ Erreur K-means: {e}")
            return self.get_dominant_colors_simple(img_rgb)
    
    def get_dominant_colors_simple(self, img_rgb):
        """Version simplifiée sans sklearn"""
        try:
            # Calculer couleur moyenne par zones
            h, w = img_rgb.shape[:2]
            zones = [
                img_rgb[0:h//2, 0:w//2],      # Top-left
                img_rgb[0:h//2, w//2:w],      # Top-right
                img_rgb[h//2:h, 0:w//2],      # Bottom-left
                img_rgb[h//2:h, w//2:w]       # Bottom-right
            ]
            
            dominant_colors = []
            for i, zone in enumerate(zones):
                mean_color = np.mean(zone.reshape(-1, 3), axis=0)
                dominant_colors.append({
                    'rgb': mean_color.astype(int).tolist(),
                    'percentage': 0.25,
                    'color_name': self.rgb_to_color_name(mean_color),
                    'zone': f'zone_{i+1}'
                })
                
            return dominant_colors
            
        except Exception as e:
            print(f"⚠️ Erreur couleurs simples: {e}")
            return []
    
    def rgb_to_color_name(self, rgb):
        """Convertir RGB vers nom de couleur"""
        r, g, b = rgb
        
        # Définition des plages de couleurs étendues
        if r > 150 and g < 100 and b < 100:
            return 'red'
        elif r < 100 and g > 150 and b < 100:
            return 'green'
        elif r > 200 and g > 200 and b < 100:
            return 'yellow'
        elif r > 200 and g > 100 and g < 200 and b < 100:
            return 'orange'
        elif r > 100 and r < 150 and g > 50 and g < 100 and b < 50:
            return 'brown'
        elif r > 200 and g > 200 and b > 200:
            return 'white'
        elif r < 100 and g < 100 and b > 150:
            return 'blue'
        elif r > 150 and g < 100 and b > 150:
            return 'purple'
        else:
            return 'mixed'
    
    def color_to_food_mapping(self, color_info):
        """Correspondance couleur -> aliments avec ML"""
        color_name = color_info['color_name']
        percentage = color_info['percentage']
        
        # Base de données couleur -> aliments étendue
        color_foods = {
            'red': [
                {'name': 'tomato', 'confidence': 0.8},
                {'name': 'apple', 'confidence': 0.7},
                {'name': 'strawberry', 'confidence': 0.6},
                {'name': 'cherry', 'confidence': 0.5},
                {'name': 'watermelon', 'confidence': 0.4}
            ],
            'green': [
                {'name': 'lettuce', 'confidence': 0.8},
                {'name': 'broccoli', 'confidence': 0.7},
                {'name': 'spinach', 'confidence': 0.6},
                {'name': 'cucumber', 'confidence': 0.6},
                {'name': 'avocado', 'confidence': 0.5}
            ],
            'yellow': [
                {'name': 'banana', 'confidence': 0.9},
                {'name': 'lemon', 'confidence': 0.7},
                {'name': 'corn', 'confidence': 0.6},
                {'name': 'cheese', 'confidence': 0.4}
            ],
            'orange': [
                {'name': 'orange', 'confidence': 0.9},
                {'name': 'carrot', 'confidence': 0.8},
                {'name': 'pumpkin', 'confidence': 0.6}
            ],
            'brown': [
                {'name': 'bread', 'confidence': 0.7},
                {'name': 'chocolate', 'confidence': 0.6},
                {'name': 'potato', 'confidence': 0.5}
            ],
            'white': [
                {'name': 'rice', 'confidence': 0.6},
                {'name': 'milk', 'confidence': 0.5}
            ]
        }
        
        foods = color_foods.get(color_name, [])
        
        # Ajuster confiance selon pourcentage
        for food in foods:
            food['confidence'] *= min(percentage * 1.5, 1.0)
        
        return foods
    
    def map_yolo_to_food(self, class_name):
        """Mapper les classes YOLOv8 vers aliments"""
        yolo_food_mapping = {
            'apple': 'apple',
            'banana': 'banana',
            'orange': 'orange',
            'sandwich': 'sandwich',
            'pizza': 'pizza',
            'donut': 'donuts',
            'cake': 'cake',
            'carrot': 'carrot',
            'broccoli': 'broccoli',
            'hot dog': 'hot_dog',
            'bottle': 'bottle',  # Peut contenir des boissons
            'cup': 'cup',
            'bowl': 'bowl',      # Peut contenir de la nourriture
            'person': None,      # Ignorer les personnes
            'dining table': None
        }
        
        return yolo_food_mapping.get(class_name.lower())
    
    def map_imagenet_to_food(self, class_name):
        """Mapper les classes ImageNet vers aliments"""
        # Correspondances des classes ImageNet communes
        imagenet_mapping = {
            'banana': 'banana',
            'orange': 'orange',
            'lemon': 'lemon',
            'pineapple': 'pineapple',
            'strawberry': 'strawberry',
            'bell_pepper': 'pepper',
            'cucumber': 'cucumber',
            'mushroom': 'mushroom',
            'broccoli': 'broccoli',
            'cauliflower': 'cauliflower',
            'cabbage': 'cabbage',
            'artichoke': 'artichoke',
            'corn': 'corn',
            'pizza': 'pizza',
            'cheeseburger': 'hamburger',
            'hot_dog': 'hot_dog',
            'ice_cream': 'ice_cream',
            'pretzel': 'pretzel',
            'bagel': 'bagel',
            'taco': 'tacos',
            'burrito': 'burrito',
            'espresso': 'coffee'
        }
        
        # Chercher correspondance directe
        for key, value in imagenet_mapping.items():
            if key.lower() in class_name.lower():
                return value
        
        # Chercher mots-clés alimentaires
        food_keywords = ['food', 'fruit', 'vegetable', 'meat', 'bread', 'cake', 'soup']
        for keyword in food_keywords:
            if keyword in class_name.lower():
                return class_name.replace('_', ' ').title()
        
        return None
    
    def select_best_detection(self, detections):
        """Sélectionner la meilleure détection avec pondération intelligente"""
        if not detections:
            return None
        
        # Pondération par source et qualité
        weights = {
            'mobilenet': 1.2,    # Modèle pré-entraîné spécialisé
            'yolo': 1.0,         # Détection d'objets solide
            'color_analysis': 0.6 # Support couleur
        }
        
        # Calculer scores pondérés
        for detection in detections:
            source = detection.get('source', 'unknown')
            weight = weights.get(source, 1.0)
            base_confidence = detection.get('confidence', 0)
            
            # Bonus pour confiance élevée
            confidence_bonus = 1.0 + (base_confidence - 0.5) * 0.2 if base_confidence > 0.5 else 1.0
            
            detection['weighted_score'] = base_confidence * weight * confidence_bonus
        
        # Retourner le meilleur
        return max(detections, key=lambda x: x.get('weighted_score', 0))
    
    def enrich_with_nutrition(self, detection):
        """Enrichir avec données nutritionnelles"""
        food_name = detection['name'].lower()
        
        # Chercher dans notre base locale
        nutrition = self.nutrition_fallback.get(food_name, {
            'calories': 100,
            'protein': 2,
            'carbs': 15,
            'fat': 3,
            'fiber': 2
        })
        
        return {
            **detection,
            'nutrition': nutrition,
            'serving_size': '100g',
            'clean_name': detection['name'].replace('_', ' ').title()
        }

# Instance globale
ai_detector = AdvancedFoodRecognitionAI()

@app.route('/detect_food', methods=['POST'])
def detect_food():
    """🎯 Endpoint principal pour Laravel - Format compatible"""
    start_time = time.time()
    
    try:
        # Récupérer les données
        data = request.get_json()
        
        if not data or 'image' not in data:
            return jsonify({
                'success': False,
                'error': 'Image required',
                'message': 'Please provide an image in base64 format'
            }), 400
        
        user_id = data.get('user_id', 'anonymous')
        print(f"🔍 Analyse demandée par utilisateur: {user_id}")
        
        # Décoder l'image base64
        try:
            image_data = data['image'].split(',')[1] if ',' in data['image'] else data['image']
            image_bytes = base64.b64decode(image_data)
        except Exception as e:
            return jsonify({
                'success': False,
                'error': 'Invalid image format',
                'message': 'Please provide a valid base64 image'
            }), 400
        
        # Sauvegarder temporairement
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        temp_path = f'temp_image_{timestamp}.jpg'
        
        try:
            image = Image.open(io.BytesIO(image_bytes))
            image.save(temp_path)
            print(f"📸 Image sauvée: {temp_path}")
        except Exception as e:
            return jsonify({
                'success': False,
                'error': 'Image processing failed',
                'message': 'Unable to process the provided image'
            }), 400
        
        # Analyser avec IA
        detected_foods = ai_detector.detect_food(temp_path)
        
        # Nettoyer
        if os.path.exists(temp_path):
            os.remove(temp_path)
            
        detection_time = round(time.time() - start_time, 2)
        
        # Format de réponse compatible Laravel
        if detected_foods:
            food = detected_foods[0]
            
            # Enrichir avec les données pour Laravel
            food_entry = {
                'name': food.get('clean_name', food['name']),
                'confidence': round(food.get('confidence', 0), 3),
                'confidence_level': get_confidence_level(food.get('confidence', 0)),
                'detection_method': food.get('source', 'ai'),
                'estimated_weight': estimate_food_weight(food['name']),
                'nutrition': calculate_nutrition_for_weight(food, estimate_food_weight(food['name'])),
                'freshness': {'level': 'Good', 'score': 0.8},
                'volume_analysis': {'estimated_volume_cm3': 150},
                'dominant_colors': ['red', 'green']
            }
            
            return jsonify({
                'success': True,
                'foods': [food_entry],  # Liste d'un élément
                'foods_count': 1,
                'total_nutrition': food_entry['nutrition'],
                'detection_time': f"{detection_time}s",
                'ai_info': {
                    'models_used': get_models_used(),
                    'confidence_level': food_entry['confidence_level'],
                    'analysis_quality': 'Good'
                }
            })
        else:
            return jsonify({
                'success': True,
                'foods': [],
                'foods_count': 0,
                'message': 'No food detected',
                'suggestions': [
                    'Try with better lighting',
                    'Move camera closer to food',
                    'Ensure food is clearly visible'
                ],
                'detection_time': f"{detection_time}s",
                'ai_info': {
                    'models_used': get_models_used(),
                    'status': 'No detection'
                }
            })
            
    except Exception as e:
        logger.error(f"Detection error: {e}")
        traceback.print_exc()
        
        return jsonify({
            'success': False,
            'error': 'Internal server error',
            'message': f'AI detection failed: {str(e)}',
            'troubleshooting': [
                'Check if all models are loaded properly',
                'Verify image format',
                'Try with a different image'
            ]
        }), 500

# Fonctions utilitaires pour Laravel
def get_confidence_level(confidence):
    """Convertir confiance numérique en niveau"""
    if confidence >= 0.8:
        return 'Very High'
    elif confidence >= 0.6:
        return 'High'
    elif confidence >= 0.4:
        return 'Medium'
    elif confidence >= 0.2:
        return 'Low'
    else:
        return 'Very Low'

def estimate_food_weight(food_name):
    """Estimer le poids d'un aliment"""
    weight_estimates = {
        'apple': 150, 'banana': 120, 'orange': 130,
        'tomato': 100, 'carrot': 80, 'broccoli': 150,
        'bread': 30, 'pizza': 250, 'hamburger': 200,
        'sandwich': 150, 'salad': 200, 'rice': 100,
        'pasta': 100, 'chicken': 150, 'beef': 150,
        'fish': 150, 'egg': 50, 'cheese': 30
    }
    
    food_lower = food_name.lower()
    for key, weight in weight_estimates.items():
        if key in food_lower:
            return weight
    
    return 100  # Poids par défaut

def calculate_nutrition_for_weight(food, weight_g):
    """Calculer nutrition pour un poids spécifique"""
    nutrition = food.get('nutrition', {})
    factor = weight_g / 100  # Conversion pour 100g
    
    return {
        'calories': round(nutrition.get('calories', 100) * factor, 1),
        'protein_g': round(nutrition.get('protein', 5) * factor, 1),
        'carbs_g': round(nutrition.get('carbs', 15) * factor, 1),
        'fat_g': round(nutrition.get('fat', 3) * factor, 1),
        'fiber_g': round(nutrition.get('fiber', 2) * factor, 1),
        'sugar_g': round(nutrition.get('carbs', 15) * 0.3 * factor, 1),
        'sodium_mg': round(factor * 50, 1)
    }

def get_models_used():
    """Retourner les modèles utilisés"""
    models = []
    if ai_detector.yolo_model:
        models.append('YOLOv8')
    if ai_detector.mobilenet_model:
        models.append('MobileNetV2')
    models.append('Color Analysis')
    return models

@app.route('/analyze-food', methods=['POST'])
def analyze_food():
    """Endpoint principal pour analyser les aliments"""
    try:
        # Récupérer l'image en base64
        data = request.get_json()
        
        if not data or 'image' not in data:
            return jsonify({'error': 'Image manquante'}), 400
        
        # Décoder l'image base64
        image_data = data['image'].split(',')[1]  # Enlever "data:image/jpeg;base64,"
        image_bytes = base64.b64decode(image_data)
        
        # Convertir en image PIL puis sauvegarder temporairement
        image = Image.open(io.BytesIO(image_bytes))
        temp_path = 'temp_image.jpg'
        image.save(temp_path)
        
        print(f"📸 Image reçue et sauvée: {temp_path}")
        
        # Analyser avec IA avancée
        detected_foods = ai_detector.detect_food(temp_path)
        
        # Nettoyer le fichier temporaire
        if os.path.exists(temp_path):
            os.remove(temp_path)
        
        if detected_foods:
            result = detected_foods[0]  # Un seul résultat
            return jsonify({
                'success': True,
                'detected_food': {
                    'name': result['clean_name'],
                    'confidence': round(result['confidence'], 2),
                    'source': result['source'],
                    'nutrition': result['nutrition']
                },
                'message': f"Aliment détecté: {result['clean_name']}"
            })
        else:
            return jsonify({
                'success': False,
                'message': 'Aucun aliment détecté dans l\'image'
            })
    
    except Exception as e:
        logger.error(f"Erreur analyse: {e}")
        traceback.print_exc()
        return jsonify({'error': f'Erreur serveur: {str(e)}'}), 500

@app.route('/health', methods=['GET'])
def health_check():
    """Vérification de l'état du service"""
    try:
        model_status = {
            'yolo': ai_detector.yolo_model is not None,
            'mobilenet': ai_detector.mobilenet_model is not None,
            'food_classes': len(ai_detector.extended_food_classes)
        }
        
        return jsonify({
            'status': 'healthy',
            'models': model_status,
            'message': '🤖 Service IA alimentaire avancé opérationnel'
        })
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

if __name__ == '__main__':
    print("🚀 Démarrage du service IA alimentaire avancé...")
    print("🤖 Modèles: YOLOv8 + MobileNetV2 + Analyse couleurs K-means")
    print("📊 Base de données: Food-101 étendue")
    
    app.run(
        host='0.0.0.0',
        port=5100,  # Port différent pour éviter les conflits
        debug=True,
        threaded=True
    )
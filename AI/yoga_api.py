# AI/yoga_api.py
import os
import logging
import pymysql

import cv2
import mediapipe as mp
import math
import base64
import numpy as np
from PIL import Image
import io
#import mysql.connector
from flask import Flask, request, jsonify
from flask_cors import CORS
from datetime import datetime
import logging
import time


# Configuration de logging
logging.basicConfig(level=logging.INFO)

# Configuration de la base de données MySQL
#DB_CONFIG = {
 #   'host': 'localhost',
  #  'user': 'root',  # Remplacez par votre utilisateur MySQL
  #  'password': '',  # Remplacez par votre mot de passe MySQL
 #   'database': 'SmartHealth'  # Remplacez par le nom de votre base de données Laravel
#}

DB_CONFIG = {
    'host': os.getenv('DB_HOST', 'database'),
    'port': int(os.getenv('DB_PORT', 3306)),
    'user': os.getenv('DB_USER', 'smarthealth'),
    'password': os.getenv('DB_PASSWORD', 'smarthealth_password'),
    'database': os.getenv('DB_NAME', 'SmartHealth')
}

app = Flask(__name__)
CORS(app)

# Initialize mediapipe pose class - Configuration plus tolérante
mp_pose = mp.solutions.pose
pose = mp_pose.Pose(static_image_mode=True, min_detection_confidence=0.5, model_complexity=1)
mp_drawing = mp.solutions.drawing_utils

def get_db_connection():
    """Créer une connexion à la base de données MySQL"""
    try:
        connection = pymysql.connect(**DB_CONFIG)
        return connection
    except Exception as err:
        logging.error(f"Erreur de connexion à la base de données: {err}")
        return None

def base64_to_image(base64_string):
    """Convertir une image base64 en format OpenCV"""
    try:
        # Enlever le préfixe data:image si présent
        if ',' in base64_string:
            base64_string = base64_string.split(',')[1]
        
        # Décoder base64
        image_data = base64.b64decode(base64_string)
        
        # Convertir en image PIL puis en array numpy
        pil_image = Image.open(io.BytesIO(image_data))
        
        # Convertir en format BGR pour OpenCV
        opencv_image = cv2.cvtColor(np.array(pil_image), cv2.COLOR_RGB2BGR)
        
        return opencv_image
    except Exception as e:
        logging.error(f"Erreur de conversion base64: {e}")
        return None

def detect_pose_landmarks(image):
    """Détecter les landmarks de pose avec MediaPipe"""
    try:
        # Convertir BGR vers RGB
        rgb_image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        
        # Traitement avec MediaPipe
        results = pose.process(rgb_image)
        
        height, width, _ = image.shape
        landmarks = []
        
        if results.pose_landmarks:
            for landmark in results.pose_landmarks.landmark:
                landmarks.append((
                    int(landmark.x * width), 
                    int(landmark.y * height), 
                    landmark.z * width  # Utiliser z au lieu de visibility pour plus de tolérance
                ))
        
        return landmarks
    except Exception as e:
        logging.error(f"Erreur de détection des landmarks: {e}")
        return []

def calculate_angle(landmark1, landmark2, landmark3):
    """Calculer l'angle entre trois points - Identique à votre app.py"""
    try:
        x1, y1, _ = landmark1
        x2, y2, _ = landmark2
        x3, y3, _ = landmark3
        
        angle = math.degrees(math.atan2(y3 - y2, x3 - x2) - math.atan2(y1 - y2, x1 - x2))
        
        if angle < 0:
            angle += 360
        
        return angle
    except:
        return 0

def classify_pose(landmarks):
    """Classification avec critères précis pour éviter les faux positifs"""
    if len(landmarks) < 33:  # MediaPipe a 33 landmarks
        return "Unknown Pose", False, 0
    
    try:
        # Calculer tous les angles nécessaires
        left_elbow_angle = calculate_angle(
            landmarks[mp_pose.PoseLandmark.LEFT_SHOULDER.value],
            landmarks[mp_pose.PoseLandmark.LEFT_ELBOW.value],
            landmarks[mp_pose.PoseLandmark.LEFT_WRIST.value]
        )
        
        right_elbow_angle = calculate_angle(
            landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER.value],
            landmarks[mp_pose.PoseLandmark.RIGHT_ELBOW.value],
            landmarks[mp_pose.PoseLandmark.RIGHT_WRIST.value]
        )
        
        left_shoulder_angle = calculate_angle(
            landmarks[mp_pose.PoseLandmark.LEFT_ELBOW.value],
            landmarks[mp_pose.PoseLandmark.LEFT_SHOULDER.value],
            landmarks[mp_pose.PoseLandmark.LEFT_HIP.value]
        )
        
        right_shoulder_angle = calculate_angle(
            landmarks[mp_pose.PoseLandmark.RIGHT_HIP.value],
            landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER.value],
            landmarks[mp_pose.PoseLandmark.RIGHT_ELBOW.value]
        )
        
        left_knee_angle = calculate_angle(
            landmarks[mp_pose.PoseLandmark.LEFT_HIP.value],
            landmarks[mp_pose.PoseLandmark.LEFT_KNEE.value],
            landmarks[mp_pose.PoseLandmark.LEFT_ANKLE.value]
        )
        
        right_knee_angle = calculate_angle(
            landmarks[mp_pose.PoseLandmark.RIGHT_HIP.value],
            landmarks[mp_pose.PoseLandmark.RIGHT_KNEE.value],
            landmarks[mp_pose.PoseLandmark.RIGHT_ANKLE.value]
        )

        # Récupérer les coordonnées pour les poses spéciales
        left_wrist_x = landmarks[mp_pose.PoseLandmark.LEFT_WRIST.value][0]
        left_hip_x = landmarks[mp_pose.PoseLandmark.LEFT_HIP.value][0]
        right_wrist_x = landmarks[mp_pose.PoseLandmark.RIGHT_WRIST.value][0]
        right_hip_x = landmarks[mp_pose.PoseLandmark.RIGHT_HIP.value][0]
        left_wrist_y = landmarks[mp_pose.PoseLandmark.LEFT_WRIST.value][1]
        left_hip_y = landmarks[mp_pose.PoseLandmark.LEFT_HIP.value][1]
        right_wrist_y = landmarks[mp_pose.PoseLandmark.RIGHT_WRIST.value][1]
        right_hip_y = landmarks[mp_pose.PoseLandmark.RIGHT_HIP.value][1]

        nose_y = landmarks[mp_pose.PoseLandmark.NOSE.value][1]
        hip_y = (landmarks[mp_pose.PoseLandmark.LEFT_HIP.value][1] + landmarks[mp_pose.PoseLandmark.RIGHT_HIP.value][1]) / 2
        ankle_y = (landmarks[mp_pose.PoseLandmark.LEFT_ANKLE.value][1] + landmarks[mp_pose.PoseLandmark.RIGHT_ANKLE.value][1]) / 2
        body_height = ankle_y - nose_y if ankle_y > nose_y else 1
        hip_height = ankle_y - hip_y if ankle_y > hip_y else 1

        # Variables pour détection de position
        is_seated = hip_height < body_height * 0.4
        left_hand_near_knee = abs(left_wrist_x - left_hip_x) < 100 and (left_wrist_y > left_hip_y)
        right_hand_near_knee = abs(right_wrist_x - right_hip_x) < 100 and (right_wrist_y > right_hip_y)
        hands_in_center = abs(left_wrist_x - right_wrist_x) < 100 and abs(left_wrist_y - right_wrist_y) < 100

        # Log des angles pour debug
        logging.info(f"Angles - Left elbow: {left_elbow_angle:.1f}, Right elbow: {right_elbow_angle:.1f}")
        logging.info(f"Left shoulder: {left_shoulder_angle:.1f}, Right shoulder: {right_shoulder_angle:.1f}")
        logging.info(f"Left knee: {left_knee_angle:.1f}, Right knee: {right_knee_angle:.1f}")
        logging.info(f"Is seated: {is_seated}, Body height: {body_height:.1f}")

        # === DÉTECTION DES POSES AVEC ORDRE DE PRIORITÉ ===

        # 1. T Pose - CRITÈRES EXACTEMENT COMME VOTRE APP.PY
        if (160 < left_knee_angle < 200) and (160 < right_knee_angle < 200) and \
           (125 < left_elbow_angle < 200) and (160 < right_elbow_angle < 225) and \
           (95 < left_shoulder_angle < 205) and (45 < right_shoulder_angle < 135):
            return 'T Pose', True, 5

        # 2. Tree Pose - CRITÈRES EXACTEMENT COMME VOTRE APP.PY
        if (160 < left_knee_angle < 200 or 160 < right_knee_angle < 200) and \
           (310 < left_knee_angle < 340 or 20 < right_knee_angle < 50):
            return 'Tree Pose', True, 8

        # 3. Warrior II Pose - CRITÈRES EXACTEMENT COMME VOTRE APP.PY
        if (160 < left_elbow_angle < 200 and 160 < right_elbow_angle < 200) and \
           (75 < left_shoulder_angle < 115 and 75 < right_shoulder_angle < 115):
            if (160 < left_knee_angle < 200 or 160 < right_knee_angle < 200) and \
               (85 < left_knee_angle < 125 or 85 < right_knee_angle < 125):
                return 'Warrior II Pose', True, 7

        # 4. Lotus Pose (Padmasana) - Priorité pour les positions assises
        if is_seated and body_height > 100:  # S'assurer qu'on a un vrai corps
            left_knee_x = landmarks[mp_pose.PoseLandmark.LEFT_KNEE.value][0]
            right_knee_x = landmarks[mp_pose.PoseLandmark.RIGHT_KNEE.value][0]
            left_knee_y = landmarks[mp_pose.PoseLandmark.LEFT_KNEE.value][1]
            right_knee_y = landmarks[mp_pose.PoseLandmark.RIGHT_KNEE.value][1]
            left_ankle_x = landmarks[mp_pose.PoseLandmark.LEFT_ANKLE.value][0]
            right_ankle_x = landmarks[mp_pose.PoseLandmark.RIGHT_ANKLE.value][0]
            left_ankle_y = landmarks[mp_pose.PoseLandmark.LEFT_ANKLE.value][1]
            right_ankle_y = landmarks[mp_pose.PoseLandmark.RIGHT_ANKLE.value][1]

            knees_wide_apart = abs(left_knee_x - right_knee_x) > body_height * 0.3
            knees_higher_than_ankles = (left_knee_y < left_ankle_y) and (right_knee_y < right_ankle_y)
            ankles_crossed_or_close = (
                abs(left_ankle_x - right_hip_x) < 100 or 
                abs(right_ankle_x - left_hip_x) < 100 or
                abs(left_ankle_x - right_ankle_x) < body_height * 0.2
            )
            
            hands_in_meditation = (
                left_elbow_angle > 80 and left_elbow_angle < 150 and
                right_elbow_angle > 80 and right_elbow_angle < 150 and
                abs(left_wrist_y - right_wrist_y) < 50
            )
            
            is_lotus_pose = knees_wide_apart and knees_higher_than_ankles and (ankles_crossed_or_close or hands_in_meditation)
            
            if is_lotus_pose:
                return 'Lotus Pose', True, 10

        # 5. Seated Meditation Pose - Pour positions assises simples
        if is_seated and body_height > 100:
            if hands_in_center or (left_hand_near_knee and right_hand_near_knee):
                return 'Seated Meditation Pose', True, 6

        # 6. Sukhasana (Easy Pose) - Position assise détendue
        if is_seated and body_height > 100:
            legs_comfortable = (left_knee_angle < 150 and right_knee_angle < 150)
            hands_relaxed = (left_elbow_angle > 90 and right_elbow_angle > 90)
            
            if legs_comfortable and hands_relaxed:
                return "Sukhasana (Easy Pose)", True, 4

        # 7. Bhujangasana (Cobra Pose) - Position allongée
        if body_height > 50:
            torso_horizontal = abs(nose_y - hip_y) < body_height * 0.3
            if torso_horizontal:
                arms_supporting = (120 <= left_elbow_angle <= 200 and 120 <= right_elbow_angle <= 200)
                if arms_supporting:
                    return "Bhujangasana (Cobra)", True, 9

        # 8. Balasana (Child's Pose) - Genoux très pliés
        if (left_knee_angle <= 90 and right_knee_angle <= 90):
            arms_forward = (120 <= left_elbow_angle <= 200 and 120 <= right_elbow_angle <= 200)
            if arms_forward:
                return "Balasana (Child's Pose)", True, 6

        # === MOUNTAIN POSE SUPPRIMÉ - CAUSAIT TROP DE FAUX POSITIFS ===
        
        # Position en préparation - Pour encourager l'utilisateur
        if len(landmarks) >= 25:  # Si on détecte suffisamment de points
            return "Position en cours", False, 0

        # Si aucune pose n'est reconnue
        return "Unknown Pose", False, 0
        
    except Exception as e:
        logging.error(f"Erreur de classification: {e}")
        return "Erreur détection", False, 0

@app.route('/detect_pose', methods=['POST'])
def detect_pose_endpoint():
    """Endpoint principal pour la détection de poses"""
    try:
        start_time = time.time()
        
        data = request.get_json()
        
        if not data or 'image' not in data:
            return jsonify({
                'success': False,
                'error': 'Image manquante'
            }), 400
        
        # Convertir l'image base64
        image = base64_to_image(data['image'])
        if image is None:
            return jsonify({
                'success': False,
                'error': 'Image invalide'
            }), 400
        
        # Détecter les landmarks
        landmarks = detect_pose_landmarks(image)
        
        # Classifier la pose
        pose_name, is_correct, points = classify_pose(landmarks)
        
        processing_time = time.time() - start_time
        
        # Log pour debug
        logging.info(f"Traitement: {processing_time:.3f}s - Pose: {pose_name}, Correct: {is_correct}, Points: {points}")
        
        return jsonify({
            'success': True,
            'pose_name': pose_name,
            'is_correct': is_correct,
            'points': points,
            'landmarks_detected': len(landmarks) > 0,
            'processing_time': round(processing_time, 3)
        })
        
    except Exception as e:
        logging.error(f"Erreur dans detect_pose_endpoint: {e}")
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/health', methods=['GET'])
def health_check():
    """Endpoint de santé pour vérifier que l'API fonctionne"""
    return jsonify({
        'status': 'healthy',
        'message': 'Yoga AI API is running',
        'timestamp': datetime.now().isoformat()
    })

if __name__ == '__main__':
    logging.info("Démarrage de l'API Yoga...")
    #app.run(host='127.0.0.1', port=5000, debug=True, threaded=True)
    app.run(host='0.0.0.0', port=5000, debug=True)
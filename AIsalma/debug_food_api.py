from flask import Flask, request, jsonify
from flask_cors import CORS
import cv2
import numpy as np
import base64
import requests
import json
from datetime import datetime
import os
import traceback

app = Flask(__name__)
CORS(app)

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
        'status': 'healthy',
        'service': 'SmartHealth Simple Food Recognition API',
        'version': '1.0.0',
        'timestamp': datetime.now().isoformat()
    })

@app.route('/detect_food', methods=['POST'])
def detect_food():
    try:
        print("📥 Nouvelle requête reçue")
        
        # Vérifier si on a des données
        if not request.is_json:
            print("❌ Pas de données JSON")
            return jsonify({
                'success': False,
                'message': 'Content-Type must be application/json'
            }), 400
        
        data = request.get_json()
        
        if not data:
            print("❌ Aucune donnée JSON reçue")
            return jsonify({
                'success': False,
                'message': 'No JSON data provided'
            }), 400
        
        if 'image' not in data:
            print("❌ Pas d'image dans les données")
            return jsonify({
                'success': False,
                'message': 'No image data provided'
            }), 400
        
        print("📥 Données reçues, début du traitement...")
        
        # Décoder l'image
        image_data = data['image']
        if image_data.startswith('data:image'):
            image_data = image_data.split(',')[1]
        
        try:
            image_bytes = base64.b64decode(image_data)
            nparr = np.frombuffer(image_bytes, np.uint8)
            image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
            
            if image is None:
                print("❌ Impossible de décoder l'image")
                return jsonify({
                    'success': False,
                    'message': 'Invalid image format'
                }), 400
            
            print(f"✅ Image décodée: {image.shape[1]}x{image.shape[0]} pixels")
            
        except Exception as e:
            print(f"❌ Erreur décodage image: {str(e)}")
            return jsonify({
                'success': False,
                'message': f'Error decoding image: {str(e)}'
            }), 400
        
        # Simple détection mock pour tester
        detected_foods = [
            {
                'name': 'Apple',
                'confidence': 0.85,
                'estimated_weight': 180,
                'detection_method': 'simple_mock',
                'nutrition': {
                    'calories': 94,
                    'protein_g': 0.5,
                    'carbs_g': 25.0,
                    'fat_g': 0.3,
                    'fiber_g': 4.3
                },
                'confidence_level': 'High'
            }
        ]
        
        print(f"✅ Détection simulée terminée: {len(detected_foods)} aliment(s)")
        
        return jsonify({
            'success': True,
            'foods': detected_foods,
            'total_nutrition': {
                'calories': 94,
                'protein_g': 0.5,
                'carbs_g': 25.0,
                'fat_g': 0.3,
                'fiber_g': 4.3
            },
            'detection_time': datetime.now().isoformat(),
            'foods_count': len(detected_foods),
            'ai_info': {
                'models_used': ['Simple Mock'],
                'detection_methods': ['simple_mock'],
                'features_analyzed': ['mock_detection']
            }
        })
        
    except Exception as e:
        print(f"❌ Erreur générale dans detect_food: {str(e)}")
        traceback.print_exc()
        
        return jsonify({
            'success': False,
            'message': f'AI processing error: {str(e)}',
            'error_type': type(e).__name__,
            'debug_info': str(e) if app.debug else None
        }), 500

if __name__ == '__main__':
    print("🤖 SmartHealth Simple Food Recognition API Starting...")
    print("📡 Server will be available at: http://127.0.0.1:5100")
    print("🔧 Debug mode: ON")
    
    app.run(host='127.0.0.1', port=5100, debug=True)
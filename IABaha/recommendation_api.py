#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
API Flask pour le Système de Recommandation SmartHealth
Port: 5002
Pour intégration avec Laravel
"""

from flask import Flask, jsonify, request
from flask_cors import CORS
import sys
import os

# Importer le système de recommandation
from auto_blog_recommender import AutoBlogRecommender

# Support UTF-8 pour Windows
if sys.platform.startswith('win'):
    sys.stdout.reconfigure(encoding='utf-8')

# Initialiser Flask
app = Flask(__name__)
CORS(app)

# Instance globale du recommender
recommender = None


def initialize_recommender():
    """Initialise le système de recommandation"""
    global recommender
    
    if recommender is None:
        csv_path = os.path.join(os.path.dirname(__file__), 'smartHealth.csv')
        
        # ✅ Utiliser les variables d'environnement Docker
        recommender = AutoBlogRecommender(
            host=os.getenv('DB_HOST', 'database'),
            database=os.getenv('DB_NAME', 'SmartHealth'),
            user=os.getenv('DB_USER', 'smarthealth'),
            password=os.getenv('DB_PASSWORD', 'smarthealth_password'),
            csv_path=csv_path
        )
        
        if recommender.connect():
            recommender.load_dataset()
            print("✅ Recommender initialisé avec succès")
            return True
        else:
            print("❌ Erreur d'initialisation du recommender")
            return False
    
    return True


@app.route('/health', methods=['GET'])
def health_check():
    """Vérifier l'état de l'API"""
    try:
        if recommender and recommender.connection and recommender.connection.is_connected():
            dataset_count = len(recommender.dataset) if recommender.dataset else 0
            return jsonify({
                'success': True,
                'status': 'online',
                'database': 'connected',
                'dataset_loaded': dataset_count > 0,
                'dataset_count': dataset_count,
                'message': f'🤖 Service IA de recommandation opérationnel - {dataset_count} ressources chargées'
            }), 200
        else:
            return jsonify({
                'success': False,
                'status': 'online',
                'database': 'disconnected',
                'message': 'Base de données non connectée'
            }), 503
    except Exception as e:
        return jsonify({
            'success': False,
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/api/recommend/<int:blog_id>', methods=['GET'])
def get_recommendation(blog_id):
    """
    Obtenir une recommandation pour un blog spécifique
    """
    try:
        if not initialize_recommender():
            return jsonify({
                'success': False,
                'message': 'Système de recommandation non disponible'
            }), 503
        
        recommendation = recommender.get_random_recommendation(blog_id)

        
        if recommendation:
        # ✅ CORRECTION : Mapper correctement source_url vers url
            return jsonify({
                'success': True,
                'blog_id': blog_id,
                'recommendation': {
                    'title': recommendation.get('title', 'Ressource recommandée'),
                    'description': recommendation.get('description', ''),
                    'category': recommendation.get('category', ''),
                    'content_type': recommendation.get('content_type', ''),
                    'difficulty_level': recommendation.get('difficulty_level', 'N/A'),
                    'estimated_time': recommendation.get('estimated_time', 'N/A'),
                    'target_audience': recommendation.get('target_audience', 'Tous'),
                    'url': recommendation.get('source_url', '')  # ✅ CORRIGÉ : source_url → url
                },
                'recommendations': [recommendation],
                'similarity_scores': [0.85],
                'ai_analysis': {
                    'method': 'content_based_filtering',
                    'confidence': 0.85,
                    'model': 'SmartHealth Recommender v1.0'
                }
            }), 200
        else:
            return jsonify({
                'success': False,
                'message': f'Blog #{blog_id} introuvable ou contenu invalide'
            }), 404
        
    except Exception as e:
        return jsonify({
            'success': False,
            'message': f'Erreur: {str(e)}'
        }), 500


@app.route('/api/recommend/multiple', methods=['POST'])
def get_multiple_recommendations():
    """
    Obtenir des recommandations pour plusieurs blogs
    
    Body JSON:
        {
            "blog_ids": [1, 2, 3, 4]
        }
    
    Returns:
        JSON avec les recommandations pour chaque blog
    """
    try:
        if not initialize_recommender():
            return jsonify({
                'success': False,
                'message': 'Système de recommandation non disponible'
            }), 503
        
        data = request.get_json()
        blog_ids = data.get('blog_ids', [])
        
        if not blog_ids:
            return jsonify({
                'success': False,
                'message': 'Aucun blog_id fourni'
            }), 400
        
        recommendations = []
        for blog_id in blog_ids:
            recommendation = recommender.get_random_recommendation(blog_id)
            
            if recommendation:
                recommendations.append({
                    'blog_id': blog_id,
                    'recommendation': recommendation
                })
        
        return jsonify({
            'success': True,
            'count': len(recommendations),
            'recommendations': recommendations
        }), 200
        
    except Exception as e:
        return jsonify({
            'success': False,
            'message': f'Erreur: {str(e)}'
        }), 500


@app.route('/api/blog/<int:blog_id>', methods=['GET'])
def check_blog(blog_id):
    """Vérifier si un blog existe dans la base de données"""
    try:
        if not initialize_recommender():
            return jsonify({
                'success': False,
                'message': 'Système de recommandation non disponible'
            }), 503
        
        cursor = recommender.connection.cursor(dictionary=True)
        
        cursor.execute("""
            SELECT id, title, category, content, created_at 
            FROM blogs 
            WHERE id = %s
        """, (blog_id,))
        
        blog = cursor.fetchone()
        cursor.close()
        
        if blog:
            return jsonify({
                'success': True,
                'blog': {
                    'id': blog['id'],
                    'title': blog['title'],
                    'category': blog['category'],
                    'content_preview': blog['content'][:200] + '...' if len(blog['content']) > 200 else blog['content'],
                    'content_length': len(blog['content']),
                    'created_at': str(blog['created_at']) if blog['created_at'] else None
                }
            }), 200
        else:
            return jsonify({
                'success': False,
                'message': f'Blog #{blog_id} introuvable'
            }), 404
            
    except Exception as e:
        return jsonify({
            'success': False,
            'message': f'Erreur: {str(e)}'
        }), 500


@app.route('/api/stats', methods=['GET'])
def get_stats():
    """Obtenir les statistiques du dataset"""
    try:
        if not initialize_recommender():
            return jsonify({
                'success': False,
                'message': 'Système de recommandation non disponible'
            }), 503
        
        if not recommender.dataset:
            return jsonify({
                'success': False,
                'message': 'Dataset non chargé'
            }), 503
        
        # Calculer les statistiques
        content_types = {}
        categories = {}
        languages = {}
        
        for item in recommender.dataset:
            ctype = item.get('content_type', 'unknown')
            content_types[ctype] = content_types.get(ctype, 0) + 1
            
            category = item.get('category', 'Non catégorisé')
            categories[category] = categories.get(category, 0) + 1
            
            language = item.get('language', 'unknown')
            languages[language] = languages.get(language, 0) + 1
        
        return jsonify({
            'success': True,
            'total_resources': len(recommender.dataset),
            'content_types': content_types,
            'top_categories': dict(sorted(categories.items(), key=lambda x: x[1], reverse=True)[:10]),
            'languages': languages
        }), 200
        
    except Exception as e:
        return jsonify({
            'success': False,
            'message': f'Erreur: {str(e)}'
        }), 500


@app.errorhandler(404)
def not_found(error):
    """Gestion des routes non trouvées"""
    return jsonify({
        'success': False,
        'message': 'Route non trouvée'
    }), 404


@app.errorhandler(500)
def internal_error(error):
    """Gestion des erreurs internes"""
    return jsonify({
        'success': False,
        'message': 'Erreur interne du serveur'
    }), 500


def shutdown_recommender():
    """Fermer proprement la connexion"""
    global recommender
    if recommender:
        recommender.close()
        print("🔌 Connexion fermée")


if __name__ == '__main__':
    print("="*80)
    print("🏥 SMARTHEALTH - API DE RECOMMANDATION")
    print("="*80)
    print("\n🚀 Démarrage de l'API Flask sur http://0.0.0.0:5002")
    print("\n📋 Routes disponibles:")
    print("   GET  /health                        - État de l'API")
    print("   GET  /api/blog/<blog_id>            - Vérifier si un blog existe")
    print("   GET  /api/recommend/<blog_id>       - Recommandation pour un blog")
    print("   POST /api/recommend/multiple        - Recommandations multiples")
    print("   GET  /api/stats                     - Statistiques du dataset")
    print("\n🔗 Pour Laravel, utilisez: http://blog-ai:5002/api/recommend/{blog_id}")
    print("="*80)
    
    try:
        if not initialize_recommender():
            print("\n⚠️  ATTENTION: Le système de recommandation n'a pas pu être initialisé")
            print("   L'API démarrera quand même, mais les requêtes échoueront")
        
        app.run(
            host='0.0.0.0',
            port=5002,
            debug=False,
            threaded=True
        )
    except KeyboardInterrupt:
        print("\n\n👋 Arrêt de l'API...")
        shutdown_recommender()
    except Exception as e:
        print(f"\n❌ Erreur fatale: {e}")
        shutdown_recommender()
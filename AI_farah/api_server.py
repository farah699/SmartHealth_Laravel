import os
import logging
from datetime import datetime
from typing import Any, Dict, List, Optional

from flask import Flask, jsonify, request
from flask_cors import CORS
import pymysql

from exercise_recommendation_ai import ExerciseRecommendationAI

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s")

app = Flask(__name__)
CORS(app)

DB_CONFIG = {
    "host": os.getenv("DB_HOST", "database"),
    "port": int(os.getenv("DB_PORT", 3306)),
    "user": os.getenv("DB_USER", "smarthealth"),
    "password": os.getenv("DB_PASSWORD", "smarthealth_password"),
    "database": os.getenv("DB_NAME", "SmartHealth"),
    "charset": "utf8mb4",
}

ai = ExerciseRecommendationAI()
_ai_ready = False


def get_db_connection() -> Optional[pymysql.connections.Connection]:
    try:
        return pymysql.connect(**DB_CONFIG)
    except Exception as err:
        logging.error("Erreur connexion DB: %s", err)
        return None


def ensure_model_loaded() -> bool:
    global _ai_ready
    if not _ai_ready:
        try:
            # Essayer de charger le modèle
            if ai.load_model():
                _ai_ready = True
                logging.info("✅ Modèle IA chargé avec succès")
                return True
            else:
                # Si le modèle n'existe pas, l'entraîner
                logging.warning("⚠️ Modèle non trouvé, entraînement en cours...")
                result = ai.train_model_from_files()
                if result:
                    _ai_ready = True
                    logging.info("✅ Modèle entraîné avec succès")
                    return True
                else:
                    logging.error("❌ Échec de l'entraînement du modèle")
                    return False
        except Exception as err:
            logging.error(f"❌ Erreur chargement modèle: {err}")
            import traceback
            traceback.print_exc()
            _ai_ready = False
            return False
    return _ai_ready


@app.route("/health", methods=["GET"])
def health() -> Any:
    model_status = ensure_model_loaded()
    return jsonify(
        {
            "status": "healthy" if model_status else "degraded",
            "model_loaded": model_status,
            "timestamp": datetime.utcnow().isoformat(),
        }
    )


@app.route("/recommendations", methods=["POST"])
def get_recommendations() -> Any:
    try:
        payload: Dict[str, Any] = request.get_json(silent=True) or {}
        
        logging.info(f"📥 Requête reçue: {payload}")
        
        limit = int(payload.pop("limit", 10))

        if not ensure_model_loaded():
            logging.error("❌ Modèle IA non disponible")
            return jsonify({
                "success": False, 
                "error": "Modèle IA indisponible. Entraînement en cours..."
            }), 503

        logging.info("🤖 Génération des recommandations...")
        recs: List[Dict[str, Any]] = ai.get_recommendations(payload, limit=limit)
        
        if not recs:
            logging.warning("⚠️ Aucune recommandation générée")
            return jsonify({
                "success": False,
                "error": "Aucune recommandation générée pour ce profil"
            }), 200
        
        logging.info(f"✅ {len(recs)} recommandations générées")
        return jsonify({"success": True, "recommendations": recs})
        
    except Exception as err:
        logging.exception("💥 Erreur lors de la génération des recommandations")
        return jsonify({
            "success": False, 
            "error": f"Erreur serveur: {str(err)}"
        }), 500


@app.route("/train", methods=["POST"])
def train_model() -> Any:
    try:
        logging.info("🚀 Démarrage de l'entraînement...")
        result = ai.train_model_from_files()
        
        if result:
            global _ai_ready
            _ai_ready = True
            return jsonify({
                "success": True, 
                "message": "Modèle entraîné avec succès",
                "performance": result
            })
        else:
            return jsonify({
                "success": False, 
                "error": "Échec de l'entraînement"
            }), 500
            
    except Exception as err:
        logging.exception("💥 Erreur lors de l'entraînement")
        return jsonify({"success": False, "error": str(err)}), 500


if __name__ == "__main__":
    logging.info("🚀 Démarrage du serveur Exercise AI...")
    
    # Tenter de charger le modèle au démarrage
    if ensure_model_loaded():
        logging.info("✅ Serveur prêt avec modèle IA chargé")
    else:
        logging.warning("⚠️ Serveur démarré mais modèle non disponible")
    
    app.run(host="0.0.0.0", port=5001, debug=False)
import os

DB_CONFIG = {
    'host': os.getenv('DB_HOST', 'database'),  # ✅ Changé de 'localhost' à 'database'
    'database': os.getenv('DB_NAME', 'SmartHealth'),
    'user': os.getenv('DB_USER', 'smarthealth'),  # ✅ Changé de 'root' à 'smarthealth'
    'password': os.getenv('DB_PASSWORD', 'smarthealth_password'),  # ✅ Ajouté le password par défaut
    'charset': 'utf8mb4'
}

DB_PORT = int(os.getenv('DB_PORT', 3306))
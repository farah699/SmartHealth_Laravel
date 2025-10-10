FROM python:3.9-slim

WORKDIR /app

# Installer les dépendances système + netcat pour wait-for-it
RUN apt-get update && apt-get install -y \
    pkg-config \
    gcc \
    g++ \
    netcat-traditional \
    && rm -rf /var/lib/apt/lists/*

# Copier requirements
COPY requirements.txt .

# Installer les dépendances Python
RUN pip install --upgrade pip
RUN pip install --no-cache-dir -r requirements.txt

# Copier le code source
COPY . .

# Copier et rendre exécutable le script wait-for-it
COPY wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

# Créer les dossiers nécessaires
RUN mkdir -p temp

# Exposer le port Flask
EXPOSE 5002

# Variables d'environnement
ENV PYTHONUNBUFFERED=1
ENV FLASK_APP=recommendation_api.py

# Attendre MySQL puis lancer l'application
CMD ["/wait-for-it.sh", "database", "python", "recommendation_api.py"]
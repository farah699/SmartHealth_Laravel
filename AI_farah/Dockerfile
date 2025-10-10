FROM python:3.9-slim

WORKDIR /app

# Mettre à jour les packages et installer les dépendances essentielles
RUN apt-get update && apt-get install -y \
    pkg-config \
    libhdf5-dev \
    gcc \
    g++ \
    cmake \
    make \
    wget \
    && rm -rf /var/lib/apt/lists/*

# Installer les dépendances Python
COPY requirements.txt .
RUN pip install --upgrade pip
RUN pip install --no-cache-dir -r requirements.txt

# Copier le code source
COPY . .

# Créer les dossiers nécessaires
RUN mkdir -p datasets models scripts tests

# Exposer le port Flask
EXPOSE 5001

# Variables d'environnement
ENV PYTHONUNBUFFERED=1
ENV FLASK_APP=api_server.py

# Lancer l'application
CMD ["python", "api_server.py"]
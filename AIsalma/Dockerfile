FROM python:3.9-slim

WORKDIR /app

# Installer les dépendances système pour OpenCV
RUN apt-get update && apt-get install -y \
    pkg-config \
    libhdf5-dev \
    libgl1-mesa-dev \
    libglib2.0-0 \
    libsm6 \
    libxext6 \
    libxrender1 \
    libgomp1 \
    libgcc-s1 \
    gcc \
    g++ \
    cmake \
    make \
    wget \
    && rm -rf /var/lib/apt/lists/*

# Copier requirements
COPY requirements.txt .

# Installer les dépendances Python
RUN pip install --upgrade pip
RUN pip install --no-cache-dir -r requirements.txt

# Copier le code source
COPY . .

# Créer les dossiers nécessaires
RUN mkdir -p temp models

# Exposer le port Flask
EXPOSE 5100

# Variables d'environnement
ENV PYTHONUNBUFFERED=1
ENV FLASK_APP=advanced_food_api.py

# Lancer l'application
CMD ["python", "advanced_food_api.py"]
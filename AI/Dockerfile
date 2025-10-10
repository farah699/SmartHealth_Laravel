FROM python:3.9-slim

WORKDIR /app

# Mettre à jour les packages et installer les dépendances essentielles
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

# Installer les dépendances Python
COPY requirements.txt .
RUN pip install --upgrade pip
RUN pip install --no-cache-dir -r requirements.txt

# Copier le code source
COPY . .

# Exposer le port Flask
EXPOSE 5000

# Variables d'environnement
ENV PYTHONUNBUFFERED=1
ENV FLASK_APP=yoga_api.py

# Lancer l'application
CMD ["python", "yoga_api.py"]
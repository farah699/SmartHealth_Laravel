#!/bin/bash

echo "🚀 Déploiement SmartHealth Production"

# Variables d'environnement
export DOCKERHUB_USERNAME="your_dockerhub_username"
export DB_DATABASE="SmartHealth"
export DB_USERNAME="smarthealth"
export DB_PASSWORD="smarthealth_password"
export DB_ROOT_PASSWORD="root_password"

# Pull des dernières images
echo "📥 Pull des images Docker..."
docker-compose -f docker-compose.prod.yml pull

# Arrêt des anciens conteneurs
echo "🛑 Arrêt des anciens conteneurs..."
docker-compose -f docker-compose.prod.yml down

# Démarrage des nouveaux conteneurs
echo "▶️ Démarrage des conteneurs..."
docker-compose -f docker-compose.prod.yml up -d

# Attendre que les services soient prêts
echo "⏳ Attente des services..."
sleep 30

# Exécution des migrations
echo "🗄️ Migrations de base de données..."
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Configuration du cache
echo "⚡ Configuration du cache..."
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

echo "✅ Déploiement terminé avec succès!"


echo "🚀 Déploiement SmartHealth Production"

# Variables d'environnement
export DOCKERHUB_USERNAME="your_dockerhub_username"
export DB_DATABASE="SmartHealth"
export DB_USERNAME="smarthealth"
export DB_PASSWORD="smarthealth_password"
export DB_ROOT_PASSWORD="root_password"

# ✅ AJOUT: Analyse SonarQube avant déploiement
echo "🔍 Analyse de qualité du code avec SonarQube..."
if command -v sonar-scanner &> /dev/null; then
    sonar-scanner \
        -Dsonar.projectKey=Farah.hmida_SmartHealth_Laravel \
        -Dsonar.organization=farah-hmida \
        -Dsonar.sources=app,resources,routes,config,AI_farah,AIsalma,IABaha \
        -Dsonar.host.url=https://sonarcloud.io \
        -Dsonar.login=$SONAR_TOKEN
else
    echo "⚠️ SonarQube Scanner non installé, analyse ignorée"
fi

# Pull des dernières images
echo "📥 Pull des images Docker..."
docker-compose -f docker-compose.prod.yml pull

# Arrêt des anciens conteneurs
echo "🛑 Arrêt des anciens conteneurs..."
docker-compose -f docker-compose.prod.yml down

# Tests avant déploiement
echo "🧪 Exécution des tests..."
docker-compose -f docker-compose.prod.yml run --rm app php artisan test

# Démarrage des nouveaux conteneurs
echo "▶️ Démarrage des conteneurs..."
docker-compose -f docker-compose.prod.yml up -d

# Attendre que les services soient prêts
echo "⏳ Attente des services..."
sleep 30

# Exécution des migrations
echo "🗄️ Migrations de base de données..."
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Configuration du cache
echo "⚡ Configuration du cache..."
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

# ✅ AJOUT: Vérification post-déploiement
echo "🩺 Vérification des services..."
curl -f http://localhost/health || echo "⚠️ Service principal non disponible"
curl -f http://localhost:5001/health || echo "⚠️ AI Farah non disponible"
curl -f http://localhost:5002/health || echo "⚠️ AI Salma non disponible"
curl -f http://localhost:5003/health || echo "⚠️ AI Baha non disponible"

echo "✅ Déploiement terminé avec succès!"
echo "📊 Consultez SonarQube: https://sonarcloud.io/project/overview?id=Farah.hmida_SmartHealth_Laravel"
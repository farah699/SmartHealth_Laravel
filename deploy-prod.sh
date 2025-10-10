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
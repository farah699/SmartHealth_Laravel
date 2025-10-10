#!/bin/bash

echo "🚀 Déploiement de SmartHealth avec Docker"

# Nettoyer complètement l'environnement Docker
echo "🧹 Nettoyage complet..."
docker-compose down -v
docker system prune -f
docker builder prune -f

# Construire les images
echo "🔨 Construction des images Docker..."
docker-compose build --no-cache

# Démarrer les services
echo "▶️ Démarrage des services..."
docker-compose up -d

# Attendre que les services soient prêts
echo "⏳ Attente du démarrage des services..."
sleep 30

# Vérifier si les conteneurs sont en cours d'exécution
echo "🔍 Vérification des conteneurs..."
docker-compose ps

# Copier le fichier .env pour Docker
echo "📋 Configuration de l'environnement..."
docker-compose exec app cp .env.docker .env

# Nettoyer et réinstaller les dépendances
echo "📦 Nettoyage et installation des dépendances..."
docker-compose exec app composer clear-cache
docker-compose exec app composer install --optimize-autoloader --no-interaction

# Génération de la clé d'application
echo "🔑 Génération de la clé d'application..."
docker-compose exec app php artisan key:generate --force

# Nettoyage du cache avant configuration
echo "🧹 Nettoyage du cache..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear

# Exécution des migrations
echo "🗄️ Exécution des migrations..."
docker-compose exec app php artisan migrate --force

# Exécution des seeders
echo "🌱 Exécution des seeders..."
docker-compose exec app php artisan db:seed --force

# Configuration du cache (après migrations)
echo "⚡ Configuration du cache..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Configuration des permissions
echo "🔒 Configuration des permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Création du lien symbolique pour le storage
echo "🔗 Création du lien symbolique pour le storage..."
docker-compose exec app php artisan storage:link

echo "✅ Déploiement terminé!"
echo "🌐 Application disponible sur: http://localhost"
echo "🗄️ phpMyAdmin disponible sur: http://localhost:8080"
echo ""
echo "📊 Status des conteneurs:"
docker-compose ps
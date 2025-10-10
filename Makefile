.PHONY: help build up down restart logs shell db-shell clean install deploy

# Variables
DOCKER_COMPOSE = docker-compose
APP_CONTAINER = smarthealth-app
DB_CONTAINER = smarthealth-database

help: ## Affiche l'aide
    @echo "SmartHealth Docker Commands:"
    @echo ""
    @grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Construit les images Docker
    $(DOCKER_COMPOSE) build --no-cache

up: ## Démarre tous les services
    $(DOCKER_COMPOSE) up -d

down: ## Arrête tous les services
    $(DOCKER_COMPOSE) down

restart: down up ## Redémarre tous les services

logs: ## Affiche les logs de tous les services
    $(DOCKER_COMPOSE) logs -f

logs-app: ## Affiche les logs de l'application
    $(DOCKER_COMPOSE) logs -f $(APP_CONTAINER)

shell: ## Accède au shell du conteneur de l'application
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) bash

db-shell: ## Accède au shell MySQL
    $(DOCKER_COMPOSE) exec $(DB_CONTAINER) mysql -u smarthealth -p SmartHealth

clean: ## Nettoie les conteneurs, volumes et images
    $(DOCKER_COMPOSE) down -v --rmi all
    docker system prune -f

install: ## Installation complète
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) composer install
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) npm install
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) npm run production

migrate: ## Exécute les migrations
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan migrate

seed: ## Exécute les seeders
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan db:seed

fresh: ## Recrée la base de données
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan migrate:fresh --seed

cache: ## Met en cache la configuration
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan config:cache
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan route:cache
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan view:cache

clear-cache: ## Vide le cache
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan config:clear
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan route:clear
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan view:clear
    $(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan cache:clear

deploy: build up install migrate seed cache ## Déploiement complet
    @echo "✅ Déploiement terminé!"
    @echo "🌐 Application: http://localhost"
    @echo "🗄️ phpMyAdmin: http://localhost:8080"

status: ## Affiche le status des conteneurs
    $(DOCKER_COMPOSE) ps
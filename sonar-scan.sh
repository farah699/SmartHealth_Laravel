#!/bin/bash
# filepath: c:\Users\ferie\OneDrive\Bureau\LaravelDevops\SmartHealth_Laravel-main\sonar-scan.sh

echo "🔍 Lancement de l'analyse SonarQube pour SmartHealth"

# Vérification des prérequis
if [ ! -f "sonar-project.properties" ]; then
    echo "❌ Fichier sonar-project.properties manquant"
    exit 1
fi

# Installation des dépendances
echo "📦 Installation des dépendances..."
composer install --no-dev

# Génération des rapports de tests
echo "🧪 Exécution des tests avec couverture..."
php artisan test --coverage-clover=coverage.xml --log-junit=tests/junit.xml

# Téléchargement du scanner SonarQube
if [ ! -f "sonar-scanner-cli.zip" ]; then
    echo "⬇️ Téléchargement du scanner SonarQube..."
    wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.8.0.2856-linux.zip -O sonar-scanner-cli.zip
    unzip sonar-scanner-cli.zip
    mv sonar-scanner-4.8.0.2856-linux sonar-scanner
fi

# Exécution de l'analyse
echo "🚀 Lancement de l'analyse SonarQube..."
./sonar-scanner/bin/sonar-scanner \
    -Dsonar.projectKey=Farah.hmida_SmartHealth_Laravel \
    -Dsonar.organization=farah-hmida \
    -Dsonar.sources=. \
    -Dsonar.host.url=https://sonarcloud.io \
    -Dsonar.login=$SONAR_TOKEN

echo "✅ Analyse terminée!"
echo "📊 Consultez les résultats sur: https://sonarcloud.io/project/overview?id=Farah.hmida_SmartHealth_Laravel"
# 📚 Guide d'Utilisation - Système de Recommandation de Blogs MySQL

## 🎯 Vue d'ensemble

Ce système analyse directement le contenu de vos blogs stockés dans phpMyAdmin/MySQL et recommande les articles les plus pertinents selon la requête de l'utilisateur.

## 📋 Structure de la Base de Données

**Base de données:** `smarthealth`  
**Table:** `blogs`

### Colonnes utilisées:
```sql
- id              INT (PRIMARY KEY)
- title           VARCHAR/TEXT
- category        VARCHAR
- content         TEXT (contenu HTML du blog)
- image_url       VARCHAR
- audio_url       VARCHAR
- audio_generated BOOLEAN
- audio_generated_at DATETIME
- created_at      TIMESTAMP
- updated_at      TIMESTAMP
- user_id         INT
```

## 🚀 Installation

### Étape 1: Installer MySQL Connector

```bash
cd smarthealth
pip install -r requirements_blog.txt
```

Ou directement:
```bash
pip install mysql-connector-python
```

### Étape 2: Configurer la connexion

Éditez `db_config.py` avec vos informations:

```python
DB_CONFIG = {
    'host': 'localhost',      # ou '127.0.0.1'
    'database': 'smarthealth',
    'user': 'root',           # votre utilisateur MySQL
    'password': '',           # votre mot de passe MySQL
    'charset': 'utf8mb4'
}
```

## 💻 Utilisation

### Mode Interactif

```bash
python blog_recommender.py
```

Le système vous demandera:
1. Hôte MySQL (défaut: localhost)
2. Nom de la base de données (défaut: smarthealth)
3. Utilisateur (défaut: root)
4. Mot de passe

Ensuite, un menu s'affichera avec 4 options:

#### 1️⃣ Rechercher des blogs (par phrase)

Entrez une phrase décrivant ce que vous cherchez:

**Exemples:**
```
"Je veux des conseils pour bien manger"
"Comment gérer le stress des examens"
"J'ai des problèmes de sommeil"
"Faire du sport régulièrement"
```

**Résultat:** 3 blogs les plus pertinents avec scores

#### 2️⃣ Voir tous les blogs d'une catégorie

Entrez le nom d'une catégorie:
```
"Nutrition"
"Sport"
"Bien-être"
```

**Résultat:** Liste de tous les blogs de cette catégorie

#### 3️⃣ Voir les détails d'un blog spécifique

Entrez l'ID du blog:
```
5
```

**Résultat:** Tous les détails du blog (titre, contenu complet, urls, dates)

#### 4️⃣ Quitter

Ferme le programme proprement

## 🔍 Comment fonctionne l'analyse?

### 1. Extraction des mots-clés

Le système détecte automatiquement 8 thèmes de santé:

- **Nutrition:** manger, alimentation, repas, calories, etc.
- **Sport:** exercice, fitness, yoga, musculation, etc.
- **Sommeil:** dormir, repos, fatigue, insomnie, etc.
- **Stress:** anxiété, tension, calme, relaxation, etc.
- **Mental:** psychologie, dépression, bonheur, motivation, etc.
- **Étudiant:** cours, examen, université, concentration, etc.
- **Santé:** médecin, prévention, hygiène, vaccin, etc.
- **Développement:** objectif, succès, habitude, croissance, etc.

### 2. Scoring des blogs

Chaque blog reçoit un score basé sur:

- **Titre** (poids x10) : Mots-clés dans le titre
- **Contenu** (poids x2) : Mots-clés dans le contenu
- **Catégorie** (+15 points) : Correspondance avec la catégorie
- **Audio** (+3 points) : Bonus si audio disponible

### 3. Sélection diversifiée

Le système sélectionne 3 blogs avec des catégories différentes si possible.

## 📊 Exemples d'utilisation

### Exemple 1: Recherche nutrition

**Requête:** "Je veux des recettes saines pour étudiants"

**Mots-clés détectés:** nutrition, alimentation, étudiant

**Résultat:**
```
1. 📝 10 Recettes Rapides pour Étudiants
   📁 Catégorie: Nutrition
   🎯 Score: 145
   🎧 Audio: Oui
   
2. 📝 Manger Équilibré avec Petit Budget
   📁 Catégorie: Alimentation saine
   🎯 Score: 98
   🎧 Audio: Non
   
3. 📝 Meal Prep Semaine Complète
   📁 Catégorie: Cuisine
   🎯 Score: 87
   🎧 Audio: Oui
```

### Exemple 2: Recherche stress

**Requête:** "Je suis stressé par mes examens"

**Mots-clés détectés:** stress, examen, étudiant

**Résultat:**
```
1. 📝 Gérer le Stress des Examens
   📁 Catégorie: Bien-être étudiant
   🎯 Score: 156
   
2. 📝 5 Techniques de Relaxation Express
   📁 Catégorie: Gestion du stress
   🎯 Score: 112
   
3. 📝 Méditation pour Étudiants
   📁 Catégorie: Mindfulness
   🎯 Score: 89
```

## 🔧 Intégration dans une API

### Utilisation programmatique

```python
from blog_recommender import BlogRecommenderSystem

# Créer le système
recommender = BlogRecommenderSystem(
    host='localhost',
    database='smarthealth',
    user='root',
    password=''
)

# Connecter et charger
if recommender.connect():
    recommender.load_blogs()
    
    # Obtenir des recommandations
    recommender.recommend("Je veux faire du yoga", max_results=3)
    
    # Fermer
    recommender.close()
```

### Récupérer les résultats pour une API

Modifiez la méthode `recommend()` pour retourner les données:

```python
def recommend_api(self, user_query, max_results=3):
    """Version API qui retourne les résultats"""
    # ... code d'analyse ...
    
    results = []
    for score, blog in diverse_blogs:
        results.append({
            'id': blog['id'],
            'title': blog['title'],
            'category': blog['category'],
            'score': score,
            'has_audio': bool(blog['audio_url']),
            'audio_url': blog['audio_url'],
            'image_url': blog['image_url'],
            'preview': blog['content'][:200],
            'created_at': str(blog['created_at'])
        })
    
    return results
```

## 🛠️ Dépannage

### Erreur: "Access denied for user"

**Solution:** Vérifiez votre nom d'utilisateur et mot de passe MySQL

### Erreur: "Unknown database 'smarthealth'"

**Solution:** Créez la base de données dans phpMyAdmin:
```sql
CREATE DATABASE smarthealth CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Erreur: "Table 'blogs' doesn't exist"

**Solution:** Créez la table blogs:
```sql
CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    content TEXT,
    image_url VARCHAR(500),
    audio_url VARCHAR(500),
    audio_generated BOOLEAN DEFAULT FALSE,
    audio_generated_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id INT,
    INDEX idx_category (category),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Erreur: "ModuleNotFoundError: No module named 'mysql'"

**Solution:** Installez le connecteur:
```bash
pip install mysql-connector-python
```

## 📈 Performance

- **Temps de chargement:** ~0.5s pour 100 blogs
- **Temps d'analyse:** ~0.1s par requête
- **Mémoire:** ~10MB pour 1000 blogs

## 🔐 Sécurité

⚠️ **Important:**

1. Ne jamais commiter `db_config.py` avec vos vraies credentials
2. Utilisez des variables d'environnement en production
3. Créez un utilisateur MySQL dédié avec des permissions limitées:

```sql
CREATE USER 'smarthealth_app'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT SELECT ON smarthealth.blogs TO 'smarthealth_app'@'localhost';
FLUSH PRIVILEGES;
```

## 🚀 Améliorations futures

- [ ] Cache des résultats pour améliorer la vitesse
- [ ] Support de plusieurs langues
- [ ] Analyse sémantique avancée (NLP)
- [ ] Recommandations basées sur l'historique utilisateur
- [ ] API REST complète
- [ ] Interface web React/Vue

## 📞 Support

Pour toute question ou problème, consultez la documentation MySQL:
- https://dev.mysql.com/doc/
- https://dev.mysql.com/doc/connector-python/en/

---

**Version:** 1.0  
**Date:** Octobre 2025  
**Auteur:** SmartHealth Team

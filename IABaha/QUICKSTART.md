# 🚀 Guide de Démarrage Rapide - SmartHealth Tracker Dataset

## ⚡ Installation et Configuration (5 minutes)

### 1️⃣ Générer le Dataset

```bash
python generate_smarthealth_dataset.py
```

**Résultat attendu :**
- ✅ Fichier `smartHealth.csv` créé (12 000 entrées)
- ✅ Distribution équilibrée sur 8 catégories
- ✅ Sources vérifiables de 98 organisations

---

### 2️⃣ Vérifier le Dataset

```bash
python test_smarthealth.py --full
```

**Ce que vous verrez :**
- 📊 Statistiques complètes
- 🔍 Vérification de la structure
- 📈 Analyse d'engagement
- 🏷️ Distribution des tags

---

### 3️⃣ Explorer les Exemples

```bash
python examples_smarthealth.py
```

**6 exemples pratiques inclus :**
1. Système de recommandation simple
2. Recommandation personnalisée
3. Analyse de tendances
4. Playlist santé journalière
5. Tableau de bord santé
6. Recherche intelligente

---

## 📝 Utilisation Basique

### Charger le Dataset en Python

```python
import csv

# Méthode 1: CSV standard
with open('smartHealth.csv', 'r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    data = list(reader)

print(f"Total: {len(data)} entrées")

# Méthode 2: Pandas (recommandé)
import pandas as pd
df = pd.read_csv('smartHealth.csv')
print(df.head())
```

---

## 🎯 Cas d'Usage Rapides

### 🔍 Recherche Simple

```python
# Trouver toutes les ressources sur le sommeil
sommeil = [entry for entry in data if 'sommeil' in entry['category'].lower()]
print(f"Trouvé: {len(sommeil)} ressources")
```

### ⭐ Filtrer par Qualité

```python
# Obtenir uniquement les contenus hautement pertinents
high_quality = [entry for entry in data if int(entry['relevance_score']) >= 4]
print(f"Contenus de qualité: {len(high_quality)}")
```

### 👥 Filtrer par Public

```python
# Ressources pour étudiants
student_content = [entry for entry in data if entry['target_audience'] == 'étudiants']
print(f"Pour étudiants: {len(student_content)} ressources")
```

### 📊 Analyser une Catégorie

```python
# Statistiques nutrition
nutrition = [e for e in data if e['category'] == 'Alimentation saine']
avg_score = sum(int(e['relevance_score']) for e in nutrition) / len(nutrition)
print(f"Score moyen nutrition: {avg_score:.2f}/5")
```

---

## 🛠️ Commandes Utiles

### Analyse Rapide

```bash
# Analyse complète
python test_smarthealth.py --full

# Rechercher une catégorie
python test_smarthealth.py --category "stress"

# Rechercher un mot-clé
python test_smarthealth.py --search "yoga"
```

### Exemples Spécifiques

```bash
# Exemple 1: Recommandation simple
python examples_smarthealth.py 1

# Exemple 5: Tableau de bord
python examples_smarthealth.py 5

# Exemple 6: Recherche "nutrition sport"
python examples_smarthealth.py 6 "nutrition sport"
```

### Mode Interactif

```bash
# Menu interactif complet
python test_smarthealth.py
```

---

## 📊 Structure des Données

Chaque entrée contient **16 champs** :

| Champ | Exemple |
|-------|---------|
| `id` | SH000042 |
| `category` | Alimentation saine |
| `title` | Guide nutritionnel pour étudiants - OMS |
| `description` | L'Organisation mondiale de la santé... |
| `source_url` | https://www.who.int/... |
| `relevance_score` | 5 |
| `date_added` | 2024-03-15 |
| `views` | 25109 |
| `likes` | 2514 |
| `shares` | 499 |
| `language` | fr |
| `content_type` | article |
| `target_audience` | étudiants |
| `difficulty_level` | débutant |
| `estimated_time` | 15 minutes |
| `tags` | nutrition, santé, alimentation |

---

## 💡 Recettes Rapides

### 🎯 Top 10 Ressources par Score

```python
sorted_data = sorted(data, key=lambda x: int(x['relevance_score']), reverse=True)
for i, entry in enumerate(sorted_data[:10], 1):
    print(f"{i}. {entry['title']} ({entry['relevance_score']}/5)")
```

### 📈 Ressources les Plus Populaires

```python
popular = sorted(data, key=lambda x: int(x['views']), reverse=True)
for entry in popular[:5]:
    print(f"{entry['title']}: {entry['views']:,} vues")
```

### 🏷️ Tags les Plus Fréquents

```python
from collections import Counter

all_tags = []
for entry in data:
    tags = [tag.strip() for tag in entry['tags'].split(',')]
    all_tags.extend(tags)

tag_counts = Counter(all_tags)
print("Top 10 tags:")
for tag, count in tag_counts.most_common(10):
    print(f"  {tag}: {count}")
```

### 📅 Ressources Récentes

```python
from datetime import datetime

# Trier par date
recent = sorted(
    data,
    key=lambda x: datetime.strptime(x['date_added'], '%Y-%m-%d'),
    reverse=True
)

print("5 ressources les plus récentes:")
for entry in recent[:5]:
    print(f"{entry['date_added']}: {entry['title']}")
```

---

## 🎨 Personnalisation

### Créer un Filtre Personnalisé

```python
def filter_resources(data, **criteria):
    """Filtre flexible pour le dataset"""
    results = data
    
    if 'category' in criteria:
        results = [e for e in results if criteria['category'].lower() in e['category'].lower()]
    
    if 'min_score' in criteria:
        results = [e for e in results if int(e['relevance_score']) >= criteria['min_score']]
    
    if 'audience' in criteria:
        results = [e for e in results if criteria['audience'] in e['target_audience']]
    
    if 'level' in criteria:
        results = [e for e in results if e['difficulty_level'] == criteria['level']]
    
    return results

# Utilisation
filtered = filter_resources(
    data,
    category='stress',
    min_score=4,
    audience='étudiants',
    level='débutant'
)

print(f"Trouvé: {len(filtered)} ressources correspondantes")
```

### Calculer un Score d'Engagement

```python
def calculate_engagement_score(entry):
    """Calcule un score d'engagement composite"""
    views = int(entry['views'])
    likes = int(entry['likes'])
    shares = int(entry['shares'])
    relevance = int(entry['relevance_score'])
    
    # Formule pondérée
    score = (
        views * 0.1 +
        likes * 1.0 +
        shares * 5.0 +
        relevance * 1000
    )
    
    return score

# Trier par engagement
ranked = sorted(data, key=calculate_engagement_score, reverse=True)

print("Top 5 contenus engageants:")
for i, entry in enumerate(ranked[:5], 1):
    score = calculate_engagement_score(entry)
    print(f"{i}. {entry['title']} (Score: {score:,.0f})")
```

---

## 🔄 Intégration

### API REST (Flask)

```python
from flask import Flask, jsonify, request
import csv

app = Flask(__name__)

# Charger les données
with open('smartHealth.csv', 'r', encoding='utf-8') as f:
    data = list(csv.DictReader(f))

@app.route('/api/resources', methods=['GET'])
def get_resources():
    category = request.args.get('category')
    if category:
        filtered = [e for e in data if category.lower() in e['category'].lower()]
        return jsonify(filtered)
    return jsonify(data)

@app.route('/api/recommend', methods=['POST'])
def recommend():
    user_prefs = request.json
    # Logique de recommandation ici
    recommendations = data[:10]  # Exemple simplifié
    return jsonify(recommendations)

if __name__ == '__main__':
    app.run(debug=True)
```

### Base de Données (SQLite)

```python
import sqlite3
import csv

# Créer la base de données
conn = sqlite3.connect('smarthealth.db')
cursor = conn.cursor()

# Créer la table
cursor.execute('''
CREATE TABLE IF NOT EXISTS resources (
    id TEXT PRIMARY KEY,
    category TEXT,
    title TEXT,
    description TEXT,
    source_url TEXT,
    relevance_score INTEGER,
    date_added DATE,
    views INTEGER,
    likes INTEGER,
    shares INTEGER,
    language TEXT,
    content_type TEXT,
    target_audience TEXT,
    difficulty_level TEXT,
    estimated_time TEXT,
    tags TEXT
)
''')

# Importer les données
with open('smartHealth.csv', 'r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    for row in reader:
        cursor.execute('''
        INSERT OR REPLACE INTO resources VALUES (
            ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
        )
        ''', tuple(row.values()))

conn.commit()
conn.close()

print("✅ Base de données créée et populée")
```

---

## 📚 Ressources Complémentaires

### Documentation
- 📖 [README Complet](README_SMARTHEALTH.md)
- 🔧 [Guide des Scripts](docs/scripts_guide.md)
- 💻 [Exemples de Code](examples_smarthealth.py)

### Scripts Disponibles
1. `generate_smarthealth_dataset.py` - Générateur
2. `test_smarthealth.py` - Analyseur
3. `examples_smarthealth.py` - Exemples pratiques

### Commandes Essentielles
```bash
# Générer
python generate_smarthealth_dataset.py

# Analyser
python test_smarthealth.py --full

# Tester
python examples_smarthealth.py
```

---

## ❓ FAQ

### Q: Puis-je modifier le nombre d'entrées ?
**R:** Oui ! Éditez `generate_smarthealth_dataset.py` et changez `target_entries=12000`

### Q: Comment ajouter de nouvelles sources ?
**R:** Ajoutez des dictionnaires dans les méthodes `_get_*_content()` du générateur

### Q: Le dataset est-il mis à jour automatiquement ?
**R:** Non, régénérez-le périodiquement pour avoir des dates récentes

### Q: Puis-je utiliser ce dataset commercialement ?
**R:** Oui, c'est sous licence Open Data (attribution requise)

### Q: Les URLs sont-elles toutes valides ?
**R:** Oui, elles pointent vers des organisations réelles, mais vérifiez la disponibilité

---

## 🎯 Prochaines Étapes

1. ✅ **Explorez** le dataset avec `test_smarthealth.py`
2. 📊 **Analysez** les catégories qui vous intéressent
3. 🔨 **Adaptez** les exemples à votre projet
4. 🚀 **Intégrez** dans votre application
5. 🤝 **Partagez** vos retours et améliorations

---

## 💬 Support

- 📧 Email: smarthealth.support@example.com
- 🐛 Issues: GitHub Issues
- 📖 Wiki: Documentation complète

---

**🌟 Bon développement avec SmartHealth Tracker !**

---

*Dernière mise à jour: Octobre 2025*

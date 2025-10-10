# 🏥 Dataset SmartHealth Tracker

![Version](https://img.shields.io/badge/version-1.0-blue)
![Entries](https://img.shields.io/badge/entries-12000-green)
![License](https://img.shields.io/badge/license-Open_Data-yellow)

## 📋 Description

Le **SmartHealth Tracker Dataset** est une base de données complète de **12 000 entrées** axée sur la santé, le bien-être et les habitudes de vie, spécialement conçue pour les **étudiants et enseignants**. Toutes les données proviennent de sources réelles et vérifiables : organisations de santé internationales (OMS, CDC, NIH), universités prestigieuses (Harvard, Stanford, Johns Hopkins), et instituts de recherche reconnus.

Ce dataset est conçu pour alimenter des applications de suivi de santé, des systèmes de recommandation personnalisée, et des outils d'éducation à la santé dans un contexte académique.

---

## 🎯 Objectifs

- ✅ Promouvoir les bonnes pratiques de santé et de bien-être au quotidien
- ✅ Fournir des ressources fiables et vérifiables pour les étudiants
- ✅ Encourager le suivi des habitudes de vie et des progrès
- ✅ Faciliter l'accès à l'information santé scientifiquement validée
- ✅ Soutenir la recherche et l'innovation en santé numérique

---

## 📊 Statistiques du Dataset

### Aperçu Général
- **Total d'entrées** : 12 000
- **Sources uniques** : 98 domaines vérifiés
- **Score moyen de pertinence** : 4.24/5
- **Période couverte** : 2023-2025
- **Langue** : Français (fr)

### Distribution par Catégorie
| Catégorie | Nombre d'entrées | Pourcentage |
|-----------|------------------|-------------|
| 🥗 Alimentation saine | 1 500 | 12.5% |
| 🏃 Activité physique | 1 500 | 12.5% |
| 😴 Sommeil & récupération | 1 500 | 12.5% |
| 🧘 Gestion du stress | 1 500 | 12.5% |
| 🧠 Bien-être mental | 1 500 | 12.5% |
| 🎓 Vie étudiante | 1 500 | 12.5% |
| 🛡️ Prévention santé | 1 500 | 12.5% |
| 📈 Développement personnel | 1 500 | 12.5% |

### Scores de Pertinence
- **5/5** : 4 200 entrées (35.0%) - Hautement pertinent
- **4/5** : 6 500 entrées (54.2%) - Très pertinent
- **3/5** : 1 300 entrées (10.8%) - Pertinent

### Public Cible
- **Tous** : 33.4%
- **Enseignants** : 33.3%
- **Étudiants** : 33.2%

---

## 📁 Structure du Dataset

Le fichier `smartHealth.csv` contient **16 colonnes** :

### Colonnes Principales

| Colonne | Type | Description | Exemple |
|---------|------|-------------|---------|
| `id` | String | Identifiant unique (format: SH000001) | SH000042 |
| `category` | String | Catégorie de santé/bien-être | "Alimentation saine" |
| `title` | String | Titre de la ressource | "Guide nutritionnel pour étudiants - OMS" |
| `description` | String | Description détaillée (2-3 phrases) | "L'Organisation mondiale de la santé recommande..." |
| `source_url` | URL | Lien vers la source vérifiable | https://www.who.int/... |
| `relevance_score` | Integer (1-5) | Score de pertinence pour SmartHealth | 5 |
| `date_added` | Date (YYYY-MM-DD) | Date d'ajout au dataset | 2024-03-15 |
| `views` | Integer | Nombre de vues simulées | 25109 |
| `likes` | Integer | Nombre de likes simulés | 2514 |
| `shares` | Integer | Nombre de partages simulés | 499 |
| `language` | String (ISO 639-1) | Langue du contenu | fr |
| `content_type` | String | Type de contenu | article, video, podcast, pdf, application |
| `target_audience` | String | Public cible | étudiants, enseignants, tous |
| `difficulty_level` | String | Niveau de difficulté | débutant, intermédiaire, avancé |
| `estimated_time` | String | Temps de lecture/visionnage estimé | "15 minutes" |
| `tags` | String (CSV) | Tags séparés par virgules | "nutrition, santé, alimentation" |

---

## 🔗 Sources Principales

### Top 20 Sources Vérifiées

| Organisation | Domaine | Entrées | Type |
|--------------|---------|---------|------|
| Mayo Clinic | mayoclinic.org | 400 | Institution médicale |
| CDC | cdc.gov | 400 | Santé publique |
| OMS/WHO | who.int | 300 | Organisation internationale |
| Harvard Health | health.harvard.edu | 300 | Université |
| ACSM | acsm.org | 300 | Médecine sportive |
| Mindful.org | mindful.org | 300 | Pleine conscience |
| Johns Hopkins | hopkinsmedicine.org | 200 | Hôpital universitaire |
| Academy of Nutrition | eatright.org | 200 | Nutrition |
| American Heart Association | heart.org | 200 | Santé cardiovasculaire |
| FDA | fda.gov | 200 | Régulation alimentaire |
| ACE Fitness | acefitness.org | 200 | Fitness |
| APA | apa.org | 200 | Psychologie |
| NIMH | nimh.nih.gov | 200 | Santé mentale |
| Psychology Today | psychologytoday.com | 200 | Psychologie |
| *... et 84 autres sources* | | | |

**Total : 98 sources uniques et vérifiables**

---

## 📚 Catégories Détaillées

### 🥗 1. Alimentation Saine
Conseils nutritionnels, plans de repas, recettes santé, hydratation, suppléments, gestion du poids, alimentation végétarienne/végétalienne, nutrition sportive.

**Exemples de contenus :**
- Guide nutritionnel OMS
- MyPlate USDA
- Recettes BBC Good Food
- Nutrition et performance cognitive

### 🏃 2. Activité Physique
Programmes d'exercice, sports, yoga, musculation, cardio, étirements, prévention des blessures, sports d'équipe.

**Exemples de contenus :**
- Recommandations activité physique OMS
- Programme 7 minutes workout
- Couch to 5K
- HIIT pour étudiants

### 😴 3. Sommeil & Récupération
Hygiène du sommeil, techniques d'endormissement, gestion du décalage horaire, siestes, environnement de sommeil, troubles du sommeil.

**Exemples de contenus :**
- Hygiène du sommeil (National Sleep Foundation)
- Sommeil et apprentissage
- Applications de suivi du sommeil
- Méditation pour le sommeil

### 🧘 4. Gestion du Stress
Techniques de relaxation, respiration, mindfulness, gestion du temps, exercice anti-stress, soutien social.

**Exemples de contenus :**
- Techniques de gestion du stress (APA)
- Mindfulness pour étudiants
- Respiration anti-stress
- Méditation guidée UCLA

### 🧠 5. Bien-être Mental
Santé mentale, résilience, estime de soi, gestion de l'anxiété, prévention du suicide, thérapie, équilibre vie-études.

**Exemples de contenus :**
- Santé mentale étudiante (NIMH)
- Premiers secours en santé mentale
- Gratitude et bien-être
- Thérapie en ligne

### 🎓 6. Vie Étudiante
Gestion budgétaire, compétences d'étude, vie en résidence, engagement étudiant, préparation aux examens, networking, stages.

**Exemples de contenus :**
- Compétences d'étude efficaces (Cornell)
- Gestion budgétaire étudiante
- Préparation aux examens (Khan Academy)
- Networking étudiant (LinkedIn)

### 🛡️ 7. Prévention Santé
Vaccinations, santé sexuelle, prévention des addictions, examens préventifs, hygiène, protection solaire, ergonomie.

**Exemples de contenus :**
- Vaccinations étudiantes (CDC)
- Santé sexuelle (Planned Parenthood)
- Prévention des addictions (SAMHSA)
- Examens de santé préventifs

### 📈 8. Développement Personnel
Objectifs SMART, intelligence émotionnelle, pensée critique, créativité, communication, leadership, habitudes positives.

**Exemples de contenus :**
- Objectifs SMART (MindTools)
- Intelligence émotionnelle (Daniel Goleman)
- Pensée critique
- Habitudes positives (James Clear)

---

## 🚀 Utilisation

### Installation et Chargement

```python
import pandas as pd

# Charger le dataset
df = pd.read_csv('smartHealth.csv')

# Aperçu
print(df.head())
print(f"Total d'entrées: {len(df)}")
```

### Exemples d'Utilisation

#### 1. Filtrer par catégorie
```python
# Obtenir toutes les entrées sur l'alimentation
nutrition = df[df['category'] == 'Alimentation saine']
print(f"Entrées nutrition: {len(nutrition)}")
```

#### 2. Recommandations par score
```python
# Obtenir les entrées hautement pertinentes
high_quality = df[df['relevance_score'] >= 4]
top_recs = high_quality.sort_values('views', ascending=False).head(10)
```

#### 3. Recherche par mot-clé
```python
# Rechercher "stress" dans titre, description ou tags
stress_content = df[
    df['title'].str.contains('stress', case=False) |
    df['description'].str.contains('stress', case=False) |
    df['tags'].str.contains('stress', case=False)
]
```

#### 4. Analyser l'engagement
```python
# Top 10 contenus les plus engageants
engagement = df.copy()
engagement['engagement_score'] = (
    engagement['views'] + 
    engagement['likes'] * 10 + 
    engagement['shares'] * 50
)
top_engagement = engagement.sort_values('engagement_score', ascending=False).head(10)
```

#### 5. Filtrer par public cible
```python
# Contenus spécifiques aux étudiants
student_content = df[df['target_audience'] == 'étudiants']
```

---

## 🛠️ Scripts Fournis

### 1. `generate_smarthealth_dataset.py`
**Générateur de dataset**
```bash
python generate_smarthealth_dataset.py
```
- Génère le fichier `smartHealth.csv` avec 12 000 entrées
- Données réelles et sources vérifiables
- Distribution équilibrée entre catégories

### 2. `test_smarthealth.py`
**Analyseur de dataset**
```bash
# Mode interactif
python test_smarthealth.py

# Analyse complète
python test_smarthealth.py --full

# Recherche par catégorie
python test_smarthealth.py --category "Alimentation"

# Recherche par mot-clé
python test_smarthealth.py --search "stress"
```

**Fonctionnalités :**
- ✅ Vérification de la structure
- 📊 Statistiques détaillées
- 🔍 Recherche multicritères
- 💡 Système de recommandations
- 📈 Analyse d'engagement
- 🏷️ Analyse des tags

---

## 📈 Cas d'Usage

### 1. Application Mobile de Suivi Santé
Utiliser le dataset pour recommander des contenus personnalisés basés sur :
- Les objectifs santé de l'utilisateur
- Son état émotionnel
- Ses habitudes actuelles

### 2. Système de Recommandation Intelligent
Créer un moteur de recommandation qui suggère :
- Articles adaptés au niveau de l'étudiant
- Ressources selon la disponibilité de temps
- Contenus en fonction des centres d'intérêt

### 3. Tableau de Bord Académique
Intégrer dans un LMS (Learning Management System) pour :
- Promouvoir le bien-être étudiant
- Fournir des ressources de santé mentale
- Encourager des habitudes saines

### 4. Chatbot de Bien-être
Alimenter un chatbot conversationnel qui :
- Répond aux questions santé
- Suggère des ressources pertinentes
- Offre un soutien personnalisé

### 5. Analyse et Recherche
Utiliser pour :
- Études sur la santé étudiante
- Analyse de tendances en bien-être
- Recherche en santé publique

---

## 🔍 Tags les Plus Utilisés

Les 25 tags les plus fréquents dans le dataset :

1. **nutrition** (1 298 occurrences)
2. **sommeil** (1 068 occurrences)
3. **étudiant** (942 occurrences)
4. **zen** (935 occurrences)
5. **mindfulness** (933 occurrences)
6. **leadership** (930 occurrences)
7. **croissance** (926 occurrences)
8. **régime** (924 occurrences)
9. **exercice** (922 occurrences)
10. **stress** (921 occurrences)

... et 34 autres tags uniques

---

## ⚠️ Considérations Importantes

### ✅ Points Forts
- ✅ **Sources vérifiables** : Toutes les URLs pointent vers des sites reconnus
- ✅ **Diversité** : 8 catégories couvrant tous les aspects de la santé
- ✅ **Qualité** : 89% des entrées ont un score de pertinence ≥ 4/5
- ✅ **Actualité** : Données ajoutées entre 2023-2025
- ✅ **Adaptabilité** : Public varié (étudiants, enseignants, tous)

### ⚠️ Limitations
- ⚠️ **Langue** : Contenu principalement en français
- ⚠️ **Données simulées** : Views, likes, shares sont générés (non réels)
- ⚠️ **Statique** : Dataset nécessite une mise à jour régulière
- ⚠️ **Géographie** : Biais vers sources américaines et européennes

### 📝 Recommandations d'Utilisation
1. **Vérifier les URLs** : Certains liens peuvent devenir obsolètes
2. **Mettre à jour régulièrement** : Santé = domaine en évolution constante
3. **Contextualiser** : Adapter les recommandations au contexte local
4. **Valider médicalement** : Ne remplace pas un avis médical professionnel

---

## 📊 Métriques d'Engagement

### Statistiques Globales
- **👁️ Vues totales** : 301 311 025
- **👁️ Vues moyennes** : 25 109 par entrée
- **👍 Likes totaux** : 30 165 473
- **👍 Likes moyens** : 2 514 par entrée
- **🔄 Partages totaux** : 5 983 498
- **🔄 Partages moyens** : 499 par entrée

> **Note** : Ces métriques sont simulées pour permettre des tests de systèmes de recommandation basés sur la popularité.

---

## 🤝 Contribution

Ce dataset est ouvert aux contributions ! Vous pouvez :

1. **Ajouter de nouvelles sources** : Proposez des ressources vérifiables
2. **Corriger des erreurs** : Signalez les URLs obsolètes
3. **Enrichir les métadonnées** : Améliorez descriptions et tags
4. **Traduire** : Créez des versions dans d'autres langues

### Comment Contribuer
1. Fork le repository
2. Ajoutez vos modifications dans `generate_smarthealth_dataset.py`
3. Testez avec `test_smarthealth.py --full`
4. Soumettez une Pull Request

---

## 📜 Licence et Citation

### Licence
Ce dataset est distribué sous **licence Open Data**. Libre d'utilisation pour :
- ✅ Projets académiques
- ✅ Recherche scientifique
- ✅ Applications commerciales
- ✅ Projets open source

**Attribution requise** : Mentionnez "SmartHealth Tracker Dataset" dans vos publications.

### Citation Suggérée
```
SmartHealth Tracker Dataset (2025). Base de données de 12 000 ressources 
sur la santé et le bien-être pour étudiants et enseignants. 
Sources: OMS, CDC, Harvard, NIH, et 94 autres institutions reconnues.
```

---

## 🔗 Liens Utiles

### Documentation
- [Guide d'utilisation complet](./docs/usage_guide.md)
- [API Reference](./docs/api_reference.md)
- [Exemples de code](./examples/)

### Ressources Externes
- [OMS - Santé des jeunes](https://www.who.int/health-topics/adolescent-health)
- [CDC - Santé universitaire](https://www.cdc.gov/healthyyouth/)
- [NIMH - Santé mentale](https://www.nimh.nih.gov/)

---

## 📧 Contact et Support

Pour toute question, suggestion ou signalement de problème :

- **Email** : smarthealth.support@example.com
- **Issues** : Ouvrez une issue sur le repository
- **Documentation** : Consultez le wiki du projet

---

## 📅 Historique des Versions

### Version 1.0 (Octobre 2025)
- ✨ Lancement initial avec 12 000 entrées
- 📚 8 catégories de santé et bien-être
- 🔗 98 sources vérifiables
- 🏷️ 44 tags uniques
- 📊 Métadonnées enrichies (engagement, difficulté, audience)

---

## 🙏 Remerciements

Merci aux organisations suivantes pour leurs ressources publiques :
- Organisation Mondiale de la Santé (OMS/WHO)
- Centers for Disease Control and Prevention (CDC)
- National Institutes of Health (NIH)
- Universités Harvard, Stanford, Johns Hopkins, Cornell
- American Psychological Association (APA)
- Mayo Clinic, Cleveland Clinic
- Et toutes les autres institutions contributrices

---

## 🎯 Prochaines Étapes

### Roadmap Future
- [ ] **v1.1** : Ajout de contenu multilingue (EN, ES, AR)
- [ ] **v1.2** : Intégration d'APIs en temps réel
- [ ] **v1.3** : Système de feedback utilisateur
- [ ] **v2.0** : ML pour recommandations personnalisées
- [ ] **v2.1** : Extension à 20 000 entrées

---

## 💡 Inspiration

> "La santé est un état de complet bien-être physique, mental et social, 
> et ne consiste pas seulement en une absence de maladie ou d'infirmité."
> 
> — Organisation Mondiale de la Santé

---

**SmartHealth Tracker Dataset** - Votre partenaire pour une vie étudiante saine et équilibrée 🌟

![Made with ❤️](https://img.shields.io/badge/Made%20with-%E2%9D%A4%EF%B8%8F-red)
![Python](https://img.shields.io/badge/Python-3.7%2B-blue)
![Data](https://img.shields.io/badge/Data-Verified-success)

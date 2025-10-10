# 🚀 GUIDE DE DÉMARRAGE - API Flask (Port 5002)

## 📁 Structure des fichiers

```
IABaha/
├── recommendation_api.py          ← API Flask (PORT 5002) - NOUVEAU
├── auto_blog_recommender.py       ← Système de recommandation (inchangé)
├── smartHealth.csv                ← Dataset 12,000 ressources (inchangé)
├── requirements_api.txt           ← Dépendances Flask
└── db_config.py                   ← Configuration MySQL
```

---

## 🔧 INSTALLATION

### 1. Installer les dépendances Flask

```powershell
cd c:\Users\Lenovo\Desktop\recomm\IABaha
pip install -r requirements_api.txt
```

**Dépendances installées :**
- `Flask==3.0.0` - Serveur web Python
- `Flask-CORS==4.0.0` - Autoriser les requêtes depuis Laravel
- `mysql-connector-python==8.2.0` - Connexion MySQL (déjà installé)

---

## 🚀 DÉMARRAGE DE L'API

### Méthode 1 : Lancer directement

```powershell
cd c:\Users\Lenovo\Desktop\recomm\IABaha
python recommendation_api.py
```

### Méthode 2 : Lancer en arrière-plan (pour production)

```powershell
cd c:\Users\Lenovo\Desktop\recomm\IABaha
Start-Process python -ArgumentList "recommendation_api.py" -WindowStyle Hidden
```

**L'API sera accessible sur :** `http://localhost:5002`

---

## 📋 ROUTES DISPONIBLES

### 1. **Vérifier l'état de l'API**

```
GET http://localhost:5002/health
```

**Réponse :**
```json
{
    "success": true,
    "status": "online",
    "database": "connected",
    "dataset_loaded": true,
    "dataset_count": 12000
}
```

---

### 2. **Obtenir une recommandation pour un blog**

```
GET http://localhost:5002/api/recommend/{blog_id}
```

**Exemple :**
```
GET http://localhost:5002/api/recommend/213
```

**Réponse :**
```json
{
    "success": true,
    "blog_id": 213,
    "recommendation": {
        "id": "5647",
        "title": "Depression: The Secret We Share",
        "category": "Santé Mentale",
        "description": "Andrew Solomon parle de sa dépression...",
        "url": "https://www.youtube.com/watch?v=xyz",
        "content_type": "video",
        "target_audience": "Tous",
        "difficulty_level": "Débutant",
        "estimated_time": "15 minutes",
        "tags": "depression,mental health,ted",
        "language": "fr"
    }
}
```

---

### 3. **Obtenir des recommandations multiples**

```
POST http://localhost:5002/api/recommend/multiple
Content-Type: application/json

{
    "blog_ids": [1, 2, 3, 4]
}
```

**Réponse :**
```json
{
    "success": true,
    "count": 4,
    "recommendations": [
        {
            "blog_id": 1,
            "recommendation": { ... }
        },
        {
            "blog_id": 2,
            "recommendation": { ... }
        }
    ]
}
```

---

### 4. **Obtenir les statistiques du dataset**

```
GET http://localhost:5002/api/stats
```

**Réponse :**
```json
{
    "success": true,
    "total_resources": 12000,
    "content_types": {
        "video": 5263,
        "application": 3311,
        "article": 3197,
        "podcast": 229
    },
    "top_categories": {
        "Santé Mentale": 2450,
        "Nutrition": 2100,
        ...
    },
    "languages": {
        "fr": 12000
    }
}
```

---

## 🔗 INTÉGRATION AVEC LARAVEL

### Dans votre contrôleur Laravel :

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class BlogController extends Controller
{
    public function showBlog($id)
    {
        // Récupérer le blog depuis votre DB Laravel
        $blog = Blog::findOrFail($id);
        
        // Appeler l'API Python pour la recommandation
        $response = Http::timeout(10)
            ->get("http://localhost:5002/api/recommend/{$id}");
        
        $recommendation = null;
        if ($response->successful()) {
            $data = $response->json();
            if ($data['success']) {
                $recommendation = $data['recommendation'];
            }
        }
        
        return view('blog.show', [
            'blog' => $blog,
            'recommendation' => $recommendation
        ]);
    }
}
```

### Dans votre vue Blade (blog.blade.php) :

```blade
@if($recommendation)
    <div class="recommendation-card">
        <h3>📚 Recommandation pour vous</h3>
        
        <div class="card">
            <h4>{{ $recommendation['title'] }}</h4>
            <p class="badge">{{ $recommendation['content_type'] }}</p>
            <p>{{ $recommendation['description'] }}</p>
            
            <a href="{{ $recommendation['url'] }}" target="_blank" class="btn">
                📖 Consulter la ressource
            </a>
            
            <p class="meta">
                ⏱️ {{ $recommendation['estimated_time'] }} | 
                🎯 {{ $recommendation['target_audience'] }}
            </p>
        </div>
    </div>
@endif
```

---

## ⚙️ MODIFICATIONS APPORTÉES

### ✅ Fichier créé : `recommendation_api.py`

**Ce qui a été ajouté :**

1. **Serveur Flask sur le port 5002**
   - `app.run(host='0.0.0.0', port=5002)`
   - Accessible depuis Laravel via `http://localhost:5002`

2. **CORS activé**
   - `CORS(app)` permet les requêtes depuis n'importe quel domaine
   - Laravel peut appeler l'API sans problème de sécurité

3. **Routes RESTful**
   - `/health` - Vérifier l'état
   - `/api/recommend/<blog_id>` - Recommandation unique
   - `/api/recommend/multiple` - Recommandations multiples
   - `/api/stats` - Statistiques

4. **Réponses JSON standardisées**
   - `success: true/false`
   - `message` en cas d'erreur
   - Données structurées et propres

5. **Gestion des erreurs**
   - Codes HTTP appropriés (200, 404, 500, 503)
   - Messages d'erreur clairs en français
   - Gestion des exceptions

6. **Initialisation automatique**
   - Connexion MySQL au démarrage
   - Chargement du dataset CSV (12,000 ressources)
   - Affichage des statistiques

### ⚙️ Fichier inchangé : `auto_blog_recommender.py`

**Aucune modification** - Le système de recommandation fonctionne exactement pareil.
L'API Flask l'utilise simplement comme une bibliothèque.

### ⚙️ Fichier inchangé : `smartHealth.csv`

**Aucune modification** - Les 12,000 ressources restent identiques.

---

## 🧪 TESTER L'API

### Test 1 : Vérifier que l'API fonctionne

```powershell
# Démarrer l'API
python recommendation_api.py

# Dans un autre terminal
curl http://localhost:5002/health
```

### Test 2 : Obtenir une recommandation

```powershell
curl http://localhost:5002/api/recommend/213
```

### Test 3 : Depuis Laravel (routes/web.php)

```php
Route::get('/test-api', function () {
    $response = Http::get('http://localhost:5002/health');
    return $response->json();
});
```

Visitez : `http://votre-laravel.test/test-api`

---

## 🔥 DÉPLOIEMENT EN PRODUCTION

### Option 1 : Lancer manuellement (développement)

```powershell
python recommendation_api.py
```

### Option 2 : Service Windows (production)

Créer un fichier `start_api.bat` :

```bat
@echo off
cd c:\Users\Lenovo\Desktop\recomm\IABaha
python recommendation_api.py
pause
```

Double-cliquer pour démarrer.

### Option 3 : Utiliser Waitress (serveur WSGI)

```powershell
pip install waitress

# Modifier recommendation_api.py (dernière ligne) :
# waitress-serve --port=5002 recommendation_api:app
```

---

## 📊 DIFFÉRENCE AVEC L'ANCIEN SYSTÈME

| Avant | Après |
|-------|-------|
| Script CLI (ligne de commande) | API REST (serveur web) |
| Pas de port | Port 5002 |
| Pas de communication avec Laravel | Laravel peut appeler l'API |
| Exécution manuelle | Toujours accessible en arrière-plan |
| Pas de format JSON | Réponses JSON structurées |

---

## ❓ TROUBLESHOOTING

### Erreur : Port 5002 déjà utilisé

```powershell
# Trouver le processus qui utilise le port
netstat -ano | findstr :5002

# Tuer le processus (remplacer PID par le numéro trouvé)
taskkill /PID <PID> /F
```

### Erreur : MySQL non connecté

Vérifier que MySQL est démarré :
```powershell
# Démarrer MySQL
net start MySQL80
```

### Erreur : Dataset non trouvé

Vérifier que `smartHealth.csv` est dans le même dossier que `recommendation_api.py`.

---

## 🎯 PROCHAINES ÉTAPES

1. ✅ Installer Flask : `pip install -r requirements_api.txt`
2. ✅ Démarrer l'API : `python recommendation_api.py`
3. ✅ Tester : `curl http://localhost:5002/health`
4. ✅ Intégrer dans Laravel (contrôleur + vue)
5. ✅ Déployer en production

---

**Questions ? Contactez l'équipe de développement ! 🚀**

#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Système de Recommandation Automatique SmartHealth
Pour chaque blog MySQL, recommande 1 ressource du dataset CSV
"""

import sys
import mysql.connector
from mysql.connector import Error
import re
import random
import csv
import os
import time

# Support UTF-8 pour Windows
if sys.platform.startswith('win'):
    sys.stdout.reconfigure(encoding='utf-8')


class AutoBlogRecommender:
    def __init__(self, host='localhost', database='SmartHealth', user='root', password='', csv_path='smartHealth.csv'):
        """Initialise la connexion à la base de données et charge le dataset CSV"""
        self.host = host
        self.database = database
        self.user = user
        self.password = password
        self.connection = None
        self.csv_path = csv_path
        self.dataset = []
        
    def connect(self):
        """Établit la connexion à la base de données"""
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                database=self.database,
                user=self.user,
                password=self.password,
                charset='utf8mb4',
                autocommit=True,  # Activer l'autocommit pour voir les dernières données
                get_warnings=True
            )
            return self.connection.is_connected()
        except Error as e:
            print(f"❌ Erreur de connexion: {e}")
            return False
    
    def load_dataset(self):
        """Charge le dataset CSV smartHealth.csv"""
        if not os.path.exists(self.csv_path):
            print(f"❌ Fichier CSV introuvable: {self.csv_path}")
            return False
        
        try:
            with open(self.csv_path, 'r', encoding='utf-8') as f:
                reader = csv.DictReader(f)
                self.dataset = list(reader)
            
            print(f"📊 {len(self.dataset)} ressources chargées depuis {self.csv_path}")
            
            # Afficher les types de contenu disponibles
            content_types = {}
            for item in self.dataset:
                ctype = item.get('content_type', 'unknown')
                content_types[ctype] = content_types.get(ctype, 0) + 1
            
            print(f"\n📦 Types de contenu disponibles:")
            for ctype, count in sorted(content_types.items(), key=lambda x: x[1], reverse=True):
                print(f"   • {ctype}: {count}")
            
            return True
            
        except Exception as e:
            print(f"❌ Erreur lors du chargement du CSV: {e}")
            return False
    
    def is_valid_content(self, text):
        """Vérifie si le contenu est valide et compréhensible (pas de lorem ipsum/faker)"""
        if not text or not text.strip():
            return False
        
        text_lower = text.lower().strip()
        
        # Mots trop génériques/simples qui ne méritent pas de recommandation
        generic_words = [
            'hello', 'hi', 'bonjour', 'salut', 'test', 'ok', 'oui', 'non',
            'yes', 'no', 'merci', 'thanks', 'bye', 'au revoir', 'aaa', 'bbb',
            'test test', 'test123', '123', 'abc', 'aaaaa'
        ]
        
        # Si le contenu est exactement un de ces mots génériques
        if text_lower in generic_words:
            return False
        
        # Mots suspects qui indiquent du contenu faker/lorem ipsum
        faker_words = [
            'dignissimos', 'veniam', 'maxime', 'repellendus', 'veritatis',
            'cumque', 'voluptatem', 'numquam', 'explicabo', 'perferendis',
            'ratione', 'nobis', 'aliquid', 'deleniti', 'harum', 'voluptas',
            'consectetur', 'adipiscing', 'lorem', 'ipsum', 'dolor', 'sit', 'amet',
            'quisquam', 'rerum', 'facilis', 'expedita', 'distinctio',
            'nam', 'libero', 'tempore', 'soluta', 'nobis', 'est', 'eligendi'
        ]
        
        # Compter combien de mots suspects sont présents
        suspect_count = sum(1 for word in faker_words if word in text_lower)
        
        # Si plus de 5 mots suspects, considérer comme contenu invalide
        if suspect_count >= 5:
            return False
        
        return True
    
    def analyze_text(self, text):
        """Extrait les mots-clés santé d'un texte"""
        if not text:
            return {}
        
        # Vérifier si le contenu est valide
        if not self.is_valid_content(text):
            return {}
        
        text_lower = text.lower()
        text_clean = re.sub(r'<[^>]+>', '', text_lower)
        
        health_keywords = {
            'nutrition': ['nutrition', 'alimentation', 'manger', 'nourriture', 'repas', 
                         'régime', 'calories', 'protéines', 'vitamines', 'fruits', 'légumes'],
            'sport': ['sport', 'exercice', 'fitness', 'entraînement', 'musculation',
                     'yoga', 'cardio', 'course', 'gym', 'workout'],
            'sommeil': ['sommeil', 'dormir', 'repos', 'fatigue', 'insomnie', 'nuit', 'sieste'],
            'stress': ['stress', 'anxiété', 'angoisse', 'tension', 'zen', 'calme', 
                      'détente', 'méditation', 'respiration'],
            'mental': ['mental', 'psychologie', 'dépression', 'moral', 'humeur', 
                      'émotion', 'bien-être', 'bonheur', 'motivation'],
            'étudiant': ['étudiant', 'étude', 'université', 'école', 'examen', 
                        'révision', 'concentration', 'productivité'],
            'santé': ['santé', 'médecin', 'maladie', 'prévention', 'hygiène', 'soin'],
            'développement': ['développement', 'personnel', 'objectif', 'succès', 
                            'habitude', 'amélioration', 'compétence']
        }
        
        keywords_found = {}
        for theme, keywords in health_keywords.items():
            count = sum(text_clean.count(keyword) for keyword in keywords)
            if count > 0:
                keywords_found[theme] = count
        
        return keywords_found
    
    def get_random_recommendation(self, blog_id):
        """
        Recommande 1 ressource du dataset CSV basée sur le contenu du blog
        
        Args:
            blog_id: ID du blog pour lequel générer une recommandation
            
        Returns:
            dict: Ressource recommandée du CSV ou None si erreur
        """
        if not self.connection or not self.connection.is_connected():
            print("❌ Pas de connexion à la base de données")
            return None
        
        if not self.dataset:
            print("❌ Dataset CSV non chargé")
            return None
        
        try:
            # Reconnecter pour s'assurer d'avoir les dernières données
            if not self.connection.is_connected():
                self.connect()
            
            cursor = self.connection.cursor(dictionary=True)
            
            # 1. Récupérer le blog source avec retry (pour gérer les problèmes de synchronisation)
            max_retries = 3
            source_blog = None
            
            for attempt in range(max_retries):
                cursor.execute("""
                    SELECT id, title, category, content 
                    FROM blogs 
                    WHERE id = %s
                """, (blog_id,))
                
                source_blog = cursor.fetchone()
                
                if source_blog:
                    break
                
                # Si pas trouvé et c'est pas le dernier essai, attendre un peu
                if attempt < max_retries - 1:
                    print(f"⏳ Blog #{blog_id} non trouvé, retry {attempt + 1}/{max_retries}...")
                    time.sleep(0.5)
                    # Forcer un refresh de la connexion
                    self.connection.commit()
            
            if not source_blog:
                print(f"❌ Aucun blog trouvé avec l'ID {blog_id}")
                print(f"   Vérifiez que le blog existe dans la base de données '{self.database}'")
                # Debug: afficher les derniers blogs disponibles
                cursor.execute("SELECT id, title, created_at FROM blogs ORDER BY id DESC LIMIT 5")
                recent_blogs = cursor.fetchall()
                if recent_blogs:
                    print(f"   Derniers blogs disponibles:")
                    for blog in recent_blogs:
                        print(f"     - ID: {blog['id']} | {blog['title'][:50]} | Créé: {blog['created_at']}")
                cursor.close()
                return None
            
            # Vérifier si le blog source a un contenu valide
            if not self.is_valid_content(source_blog['content']):
                print(f"⚠️  Le blog #{blog_id} n'a pas de contenu valide (contenu généré automatiquement)")
                return None
            
            # 2. Analyser le contenu du blog source
            source_text = f"{source_blog['title']} {source_blog['category']} {source_blog['content']}"
            source_keywords = self.analyze_text(source_text)
            
            cursor.close()
            
            if not source_keywords:
                # Si pas de mots-clés, recommander une ressource aléatoire du CSV
                return random.choice(self.dataset)
            
            # 3. Scorer toutes les ressources du dataset CSV
            scored_items = []
            
            for item in self.dataset:
                score = 0
                item_text = f"{item.get('title', '')} {item.get('description', '')} {item.get('category', '')}"
                item_keywords = self.analyze_text(item_text)
                
                # Calculer la similarité basée sur les mots-clés communs
                for theme in source_keywords:
                    if theme in item_keywords:
                        score += min(source_keywords[theme], item_keywords[theme])
                
                # Bonus pour même catégorie
                if source_blog['category'] and item.get('category'):
                    if source_blog['category'].lower() in item.get('category', '').lower():
                        score += 10
                
                # Bonus selon le type de contenu
                content_type = item.get('content_type', '').lower()
                if 'podcast' in content_type or 'audio' in content_type:
                    score += 3
                elif 'video' in content_type or 'vidéo' in content_type:
                    score += 2
                
                if score > 0:
                    scored_items.append((score, item))
            
            # 4. Sélectionner aléatoirement parmi les meilleures ressources
            if scored_items:
                # Trier par score
                scored_items.sort(key=lambda x: x[0], reverse=True)
                
                # Prendre les 10 meilleures ou toutes si moins de 10
                top_items = scored_items[:min(10, len(scored_items))]
                
                # Choisir aléatoirement parmi les top
                _, recommendation = random.choice(top_items)
            else:
                # Aucune ressource pertinente, prendre une aléatoire
                recommendation = random.choice(self.dataset)
            
            return recommendation
            
        except Error as e:
            print(f"❌ Erreur lors de la recommandation: {e}")
            return None
    
    def recommend_for_all_blogs(self):
        """Génère et affiche des recommandations du CSV pour tous les blogs"""
        if not self.connection or not self.connection.is_connected():
            print("❌ Pas de connexion à la base de données")
            return
        
        if not self.dataset:
            print("❌ Dataset CSV non chargé")
            return
        
        try:
            cursor = self.connection.cursor(dictionary=True)
            
            # Récupérer tous les blogs
            cursor.execute("SELECT id, title, category FROM blogs ORDER BY created_at DESC")
            all_blogs = cursor.fetchall()
            
            if not all_blogs:
                print("❌ Aucun blog dans la base de données")
                cursor.close()
                return
            
            print("="*80)
            print(f"🤖 GÉNÉRATION AUTOMATIQUE DE RECOMMANDATIONS")
            print(f"   (depuis le dataset smartHealth.csv)")
            print("="*80)
            print(f"\n📚 {len(all_blogs)} blogs trouvés")
            print(f"📊 {len(self.dataset)} ressources disponibles dans le dataset\n")
            
            # Pour chaque blog, générer une recommandation
            valid_blogs_count = 0
            
            for i, blog in enumerate(all_blogs, 1):
                # Vérifier si le blog a un contenu valide
                cursor_check = self.connection.cursor(dictionary=True)
                cursor_check.execute("SELECT content FROM blogs WHERE id = %s", (blog['id'],))
                blog_content = cursor_check.fetchone()
                cursor_check.close()
                
                if not self.is_valid_content(blog_content['content']):
                    print(f"\n{'-'*80}")
                    print(f"⏭️  Blog #{blog['id']}: {blog['title']}")
                    print(f"   ⚠️  Contenu non valide - Aucune recommandation générée")
                    continue
                
                valid_blogs_count += 1
                
                print(f"\n{'-'*80}")
                print(f"📝 Blog #{blog['id']}: {blog['title']}")
                print(f"📁 Catégorie: {blog['category'] or 'Non catégorisé'}")
                
                recommendation = self.get_random_recommendation(blog['id'])
                
                if recommendation:
                    print(f"\n   ✨ Recommandation (depuis CSV):")
                    print(f"   → Titre: {recommendation.get('title', 'Sans titre')}")
                    print(f"   → Catégorie: {recommendation.get('category', 'Non catégorisé')}")
                    print(f"   → Type: {recommendation.get('content_type', 'Article')}")
                    
                    url = recommendation.get('source_url', recommendation.get('url', ''))
                    if url:
                        print(f"   → URL: {url[:70]}{'...' if len(url) > 70 else ''}")
                else:
                    print(f"   ⚠️  Aucune recommandation disponible")
            
            print(f"\n{'='*80}")
            print(f"✅ Recommandations générées pour {valid_blogs_count}/{len(all_blogs)} blogs valides")
            print(f"⚠️  {len(all_blogs) - valid_blogs_count} blog(s) ignoré(s) (contenu non valide)")
            print("="*80)
            
            cursor.close()
            
        except Error as e:
            print(f"❌ Erreur: {e}")
    
    def close(self):
        """Ferme la connexion"""
        if self.connection and self.connection.is_connected():
            self.connection.close()


def main():
    """Fonction principale"""
    print("="*80)
    print("🏥 SMARTHEALTH - RECOMMANDATION AUTOMATIQUE")
    print("="*80)
    
    # Configuration par défaut
    csv_path = os.path.join(os.path.dirname(__file__), 'smartHealth.csv')
    
    recommender = AutoBlogRecommender(
        host='localhost',
        database='SmartHealth',
        user='root',
        password='',
        csv_path=csv_path
    )
    
    print("\n🔄 Connexion à la base de données...")
    
    if not recommender.connect():
        print("❌ Impossible de se connecter")
        return
    
    print("✅ Connecté avec succès!\n")
    
    print("🔄 Chargement du dataset CSV...")
    if not recommender.load_dataset():
        print("❌ Impossible de charger le dataset")
        recommender.close()
        return
    
    print("✅ Dataset chargé avec succès!\n")
    
    # Menu
    print("="*80)
    print("📋 MENU")
    print("="*80)
    print("\n1️⃣  Recommander pour un blog spécifique")
    print("2️⃣  Générer recommandations pour TOUS les blogs")
    print("3️⃣  Quitter")
    
    while True:
        try:
            print("\n" + "-"*80)
            choice = input("\n💭 Votre choix (1-3): ").strip()
            
            if choice == '1':
                try:
                    blog_id = int(input("\n🆔 ID du blog: ").strip())
                    
                    print("\n🔄 Analyse en cours...")
                    recommendation = recommender.get_random_recommendation(blog_id)
                    
                    if recommendation:
                        print("\n" + "="*80)
                        print("✨ RECOMMANDATION (depuis smartHealth.csv)")
                        print("="*80)
                        print(f"\n📝 Titre: {recommendation.get('title', 'Sans titre')}")
                        print(f"📁 Catégorie: {recommendation.get('category', 'Non catégorisé')}")
                        print(f"📦 Type: {recommendation.get('content_type', 'Article')}")
                        print(f"⏱️  Durée: {recommendation.get('estimated_time', 'N/A')}")
                        print(f"🎯 Public: {recommendation.get('target_audience', 'Tous')}")
                        print(f"📊 Difficulté: {recommendation.get('difficulty_level', 'N/A')}")
                        
                        url = recommendation.get('source_url', recommendation.get('url', ''))
                        if url:
                            print(f"\n🔗 URL: {url}")
                        
                        description = recommendation.get('description', '')
                        if description:
                            desc_preview = description[:150] + "..." if len(description) > 150 else description
                            print(f"\n� Description: {desc_preview}")
                        
                        print("="*80)
                    
                except ValueError:
                    print("⚠️  ID invalide")
            
            elif choice == '2':
                recommender.recommend_for_all_blogs()
            
            elif choice == '3':
                print("\n👋 Au revoir!")
                break
            
            else:
                print("⚠️  Choix invalide")
        
        except KeyboardInterrupt:
            print("\n\n👋 Au revoir!")
            break
        except Exception as e:
            print(f"\n❌ Erreur: {e}")
    
    recommender.close()


if __name__ == "__main__":
    main()

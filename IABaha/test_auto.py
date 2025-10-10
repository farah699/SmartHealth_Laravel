#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Test rapide du système"""

try:
    from auto_blog_recommender import AutoBlogRecommender
    
    print("Module importé avec succès!")
    
    recommender = AutoBlogRecommender(
        host='localhost',
        database='smarthealth',
        user='root',
        password='',
        csv_path='smartHealth.csv'
    )
    
    print("\n1. Connexion MySQL...")
    if recommender.connect():
        print("   ✅ Connecté")
    
    print("\n2. Chargement CSV...")
    if recommender.load_dataset():
        print("   ✅ Dataset chargé")
    
    print("\n3. Test recommandation pour blog #213...")
    rec = recommender.get_random_recommendation(213)
    
    if rec:
        print("\n✨ RECOMMANDATION:")
        print(f"   Titre: {rec.get('title')}")
        print(f"   Type: {rec.get('content_type')}")
        print(f"   Catégorie: {rec.get('category')}")
        print(f"   URL: {rec.get('source_url', rec.get('url', 'N/A'))}")
        print(f"   Durée: {rec.get('estimated_time', 'N/A')}")
        print(f"   Public: {rec.get('target_audience', 'Tous')}")
    
    recommender.close()
    
except Exception as e:
    print(f"❌ ERREUR: {e}")
    import traceback
    traceback.print_exc()

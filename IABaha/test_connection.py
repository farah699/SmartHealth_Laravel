#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script de test pour diagnostiquer les problèmes de connexion MySQL
"""

import mysql.connector
from mysql.connector import Error
import sys

# Support UTF-8 pour Windows
if sys.platform.startswith('win'):
    sys.stdout.reconfigure(encoding='utf-8')

def test_connection():
    """Teste la connexion à la base de données"""
    
    print("="*80)
    print("🔍 TEST DE CONNEXION MYSQL - SMARTHEALTH")
    print("="*80)
    
    databases_to_test = ['SmartHealth', 'smarthealth']
    
    for db_name in databases_to_test:
        print(f"\n📊 Test avec la base de données: '{db_name}'")
        print("-"*80)
        
        try:
            connection = mysql.connector.connect(
                host='localhost',
                database=db_name,
                user='root',
                password='',
                charset='utf8mb4',
                autocommit=True
            )
            
            if connection.is_connected():
                print(f"✅ Connecté avec succès à '{db_name}'!")
                
                cursor = connection.cursor(dictionary=True)
                
                # Test 1: Compter les blogs
                cursor.execute("SELECT COUNT(*) as count FROM blogs")
                result = cursor.fetchone()
                print(f"   📝 Nombre total de blogs: {result['count']}")
                
                # Test 2: Derniers blogs
                cursor.execute("""
                    SELECT id, title, category, created_at 
                    FROM blogs 
                    ORDER BY id DESC 
                    LIMIT 10
                """)
                recent_blogs = cursor.fetchall()
                
                print(f"\n   📋 Les 10 derniers blogs:")
                for blog in recent_blogs:
                    print(f"      ID: {blog['id']:3d} | {blog['title'][:40]:40s} | {blog['category'][:20]:20s} | {blog['created_at']}")
                
                # Test 3: Vérifier si le blog 225 existe
                print(f"\n   🔎 Recherche du blog ID 225...")
                cursor.execute("SELECT * FROM blogs WHERE id = 225")
                blog_225 = cursor.fetchone()
                
                if blog_225:
                    print(f"   ✅ Blog 225 trouvé!")
                    print(f"      Titre: {blog_225['title']}")
                    print(f"      Catégorie: {blog_225['category']}")
                    print(f"      Contenu: {blog_225['content'][:100]}...")
                    print(f"      Créé le: {blog_225['created_at']}")
                else:
                    print(f"   ❌ Blog 225 NON trouvé dans '{db_name}'")
                
                # Test 4: Vérifier les IDs disponibles autour de 225
                cursor.execute("""
                    SELECT id FROM blogs 
                    WHERE id BETWEEN 220 AND 230 
                    ORDER BY id
                """)
                ids_around = cursor.fetchall()
                print(f"\n   📊 IDs disponibles entre 220 et 230: {[b['id'] for b in ids_around]}")
                
                cursor.close()
                connection.close()
                
        except Error as e:
            print(f"   ❌ Erreur de connexion à '{db_name}': {e}")
    
    print("\n" + "="*80)
    print("✅ Test terminé")
    print("="*80)

if __name__ == "__main__":
    test_connection()

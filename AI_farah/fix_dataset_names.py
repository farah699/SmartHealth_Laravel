import os
import shutil

def fix_dataset_names():
    """Corrige les noms de fichiers pour la compatibilité Laravel"""
    print("🔧 CORRECTION DES NOMS DE FICHIERS")
    print("-" * 40)
    
    datasets_dir = 'datasets'
    
    # Mappage ancien nom -> nouveau nom
    rename_map = {
        'users_profiles.csv': 'usersprofiles.csv'  # Laravel attend ce nom sans underscore
    }
    
    for old_name, new_name in rename_map.items():
        old_path = os.path.join(datasets_dir, old_name)
        new_path = os.path.join(datasets_dir, new_name)
        
        if os.path.exists(old_path):
            if os.path.exists(new_path):
                print(f"⚠️  {new_name} existe déjà")
                # Faire une sauvegarde
                backup_path = f"{new_path}.backup"
                shutil.copy2(new_path, backup_path)
                print(f"💾 Sauvegarde créée: {backup_path}")
            
            shutil.copy2(old_path, new_path)  # Copier au lieu de déplacer
            print(f"✅ {old_name} → {new_name}")
        else:
            print(f"❌ {old_name} non trouvé")
    
    print("\n🎯 Vérification des fichiers:")
    required_files = ['users_profiles.csv', 'usersprofiles.csv', 'exercises_catalog.csv', 'training_data.csv']
    for filename in required_files:
        filepath = os.path.join(datasets_dir, filename)
        if os.path.exists(filepath):
            size_mb = os.path.getsize(filepath) / 1024 / 1024
            print(f"   ✅ {filename}: {size_mb:.1f} MB")
        else:
            print(f"   ❌ {filename}: MANQUANT")
    
    print("🎯 Correction terminée !")

if __name__ == "__main__":
    fix_dataset_names()
<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Informations personnelles
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->decimal('height', 5, 2)->nullable(); // cm
            $table->decimal('weight', 5, 2)->nullable(); // kg
            
            // Niveau d'activité
            $table->enum('activity_level', [
                'sedentary',     // Bureau, peu d'exercice
                'light',         // Exercice léger 1-3 jours/semaine
                'moderate',      // Exercice modéré 3-5 jours/semaine
                'active',        // Exercice intense 6-7 jours/semaine
                'very_active'    // Exercice très intense + travail physique
            ])->default('sedentary');
            
            // Objectif
            $table->enum('goal', ['lose', 'maintain', 'gain'])->default('maintain');
            
            // Calculs automatiques (mis à jour à chaque modification du profil)
            $table->integer('bmr')->nullable(); // Métabolisme de base
            $table->integer('tdee')->nullable(); // Dépense énergétique totale
            $table->integer('daily_calories')->nullable(); // Objectif calorique quotidien
            $table->decimal('daily_water_ml', 8, 2)->nullable(); // Objectif hydratation (ml)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
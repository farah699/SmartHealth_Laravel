<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('summary_date');
            
            // Résumé alimentaire
            $table->decimal('total_calories', 8, 2)->default(0);
            $table->decimal('total_proteins', 8, 2)->default(0);
            $table->decimal('total_carbs', 8, 2)->default(0);
            $table->decimal('total_fats', 8, 2)->default(0);
            $table->decimal('total_fiber', 8, 2)->default(0);
            
            // Résumé hydratation
            $table->decimal('total_water_ml', 8, 2)->default(0);
            
            // Objectifs du jour (snapshot des objectifs du profil)
            $table->integer('calorie_goal')->nullable();
            $table->decimal('water_goal_ml', 8, 2)->nullable();
            
            // Calculs de progression
            $table->decimal('calorie_percentage', 5, 2)->default(0); // % de l'objectif atteint
            $table->decimal('water_percentage', 5, 2)->default(0);
            
            $table->timestamps();
            
            // Contrainte unique pour éviter les doublons
            $table->unique(['user_id', 'summary_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
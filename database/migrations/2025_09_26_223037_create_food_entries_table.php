<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Informations de l'aliment
            $table->string('food_name');
            $table->decimal('quantity', 8, 2); // grammes ou ml
            $table->string('unit', 10)->default('g'); // g, ml, piece, cup, etc.
            
            // Catégorie du repas
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack'])->default('snack');
            
            // Informations nutritionnelles (via API)
            $table->decimal('calories_per_100g', 8, 2)->nullable();
            $table->decimal('total_calories', 8, 2)->nullable();
            $table->decimal('proteins', 8, 2)->nullable(); // g
            $table->decimal('carbs', 8, 2)->nullable(); // g
            $table->decimal('fats', 8, 2)->nullable(); // g
            $table->decimal('fiber', 8, 2)->nullable(); // g
            
            // Métadonnées
            $table->date('entry_date');
            $table->time('entry_time');
            $table->text('notes')->nullable();
            $table->json('api_response')->nullable(); // Stocker la réponse complète de l'API
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['user_id', 'entry_date']);
            $table->index('meal_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_entries');
    }
};
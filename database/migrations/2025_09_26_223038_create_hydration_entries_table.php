<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hydration_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Type de boisson
            $table->enum('drink_type', [
                'water',         // Eau pure
                'tea',           // Thé sans sucre
                'coffee',        // Café sans sucre
                'herbal_tea',    // Tisane
                'sparkling_water', // Eau gazeuse
                'other'          // Autre boisson non sucrée
            ])->default('water');
            
            $table->decimal('amount_ml', 8, 2); // Quantité en ml
            $table->date('entry_date');
            $table->time('entry_time');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['user_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hydration_entries');
    }
};
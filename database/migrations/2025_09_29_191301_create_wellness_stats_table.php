<?php
// filepath: database/migrations/xxxx_create_wellness_stats_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellness_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('stat_date');
            $table->integer('total_planned_minutes')->default(0);
            $table->integer('total_completed_minutes')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0); // Pourcentage
            $table->decimal('average_mood_before', 3, 2)->nullable();
            $table->decimal('average_mood_after', 3, 2)->nullable();
            $table->decimal('average_stress_before', 3, 2)->nullable();
            $table->decimal('average_stress_after', 3, 2)->nullable();
            $table->integer('streak_days')->default(0);
            $table->json('category_breakdown')->nullable(); // Temps par catégorie
            $table->timestamps();

            $table->unique(['user_id', 'stat_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellness_stats');
    }
};
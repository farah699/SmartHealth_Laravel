<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exercise_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->string('exercise_name');
            $table->string('exercise_type');
            $table->integer('duration_minutes');
            $table->string('intensity_level');
            $table->decimal('calories_burned', 6, 2);
            $table->text('description');
            $table->text('instructions');
            $table->json('equipment_needed')->nullable();
            $table->json('target_muscle_groups');
            $table->integer('user_age');
            $table->decimal('user_imc', 5, 2);
            $table->string('user_fitness_level');
            $table->decimal('recommended_score', 5, 2); // Score de recommandation (0-100)
            $table->boolean('is_watched')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->integer('watch_duration')->nullable(); // temps regardé en secondes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exercise_recommendations');
    }
};
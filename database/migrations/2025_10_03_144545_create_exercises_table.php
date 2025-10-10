<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // cardio, strength, flexibility, balance
            $table->string('category'); // beginner, intermediate, advanced
            $table->integer('difficulty_level'); // 1-10
            $table->integer('duration_min'); // minutes minimum
            $table->integer('duration_max'); // minutes maximum
            $table->decimal('calories_per_minute', 5, 2);
            $table->text('description');
            $table->text('instructions');
            $table->json('equipment_needed')->nullable();
            $table->json('target_muscle_groups');
            $table->integer('age_min')->default(18);
            $table->integer('age_max')->default(80);
            $table->decimal('imc_min', 5, 2)->default(15.0);
            $table->decimal('imc_max', 5, 2)->default(35.0);
            $table->json('contraindications')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_thumbnail')->nullable();
            $table->integer('video_duration')->nullable(); // en secondes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exercises');
    }
};
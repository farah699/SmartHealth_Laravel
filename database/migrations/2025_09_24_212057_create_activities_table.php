<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nom de l'activité
            $table->enum('type', ['course', 'marche', 'velo', 'fitness']); // Type d'activité
            $table->integer('duration')->comment('Durée en minutes');
            $table->decimal('distance', 8, 2)->nullable()->comment('Distance en km');
            $table->integer('calories')->nullable()->comment('Calories brûlées');
            $table->text('description')->nullable();
            $table->date('activity_date'); // Date de l'activité
            $table->time('start_time')->nullable(); // Heure de début
            $table->enum('intensity', ['faible', 'modere', 'intense'])->default('modere');
            $table->json('additional_data')->nullable()->comment('Données supplémentaires (fréquence cardiaque, etc.)');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
};
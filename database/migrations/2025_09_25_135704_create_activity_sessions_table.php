<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('session_date'); 
            $table->time('start_time')->nullable(); 
            $table->integer('duration')->comment('Durée en minutes de cette session');
            $table->decimal('distance', 8, 2)->nullable()->comment('Distance de cette session');
            $table->integer('calories')->nullable()->comment('Calories de cette session');
            $table->enum('intensity', ['faible', 'modere', 'intense'])->default('modere');
            $table->text('session_notes')->nullable()->comment('Notes spécifiques à cette session');
            $table->json('session_data')->nullable()->comment('Données spécifiques à cette session');
            $table->decimal('rating', 2, 1)->nullable()->comment('Note de satisfaction 1-5');
            $table->enum('difficulty', ['tres_facile', 'facile', 'normal', 'difficile', 'tres_difficile'])->nullable();
            $table->timestamps();
            
            $table->index(['activity_id', 'session_date']);
            $table->index(['user_id', 'session_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_sessions');
    }
};
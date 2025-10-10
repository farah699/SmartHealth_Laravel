<?php
// database/migrations/xxxx_xx_xx_create_yoga_sessions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('yoga_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->integer('duration')->default(0); // en secondes
            $table->integer('total_points')->default(0);
            $table->json('poses_data')->nullable(); // stockage des poses détectées
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('yoga_sessions');
    }
};
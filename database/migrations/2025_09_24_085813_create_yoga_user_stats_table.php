<?php
// database/migrations/xxxx_xx_xx_create_yoga_user_stats_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('yoga_user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->integer('total_sessions')->default(0);
            $table->integer('total_duration')->default(0); // en secondes
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->date('last_practice_date')->nullable();
            $table->integer('level')->default(1);
            $table->json('pose_mastery')->nullable(); // progression par pose
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('yoga_user_stats');
    }
};
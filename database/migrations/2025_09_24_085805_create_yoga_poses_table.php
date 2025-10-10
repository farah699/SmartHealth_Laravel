<?php
// database/migrations/xxxx_xx_xx_create_yoga_poses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('yoga_poses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained('yoga_sessions')->onDelete('cascade');
            $table->string('pose_name');
            $table->integer('correct_count')->default(0);
            $table->integer('total_attempts')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0);
            $table->integer('points_earned')->default(0);
            $table->timestamp('detected_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('yoga_poses');
    }
};
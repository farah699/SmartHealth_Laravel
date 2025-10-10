<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exercise_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('video_url');
            $table->string('thumbnail_url')->nullable();
            $table->integer('duration'); // en secondes
            $table->string('quality'); // 720p, 1080p, etc.
            $table->string('language')->default('fr');
            $table->json('subtitles')->nullable();
            $table->integer('views_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exercise_videos');
    }
};
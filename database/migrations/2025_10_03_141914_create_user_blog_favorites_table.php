<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_blog_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['favorite', 'read_later'])->default('favorite');
            $table->timestamp('read_at')->nullable(); // Pour marquer comme lu
            $table->timestamps();
            
            // Index unique pour éviter les doublons
            $table->unique(['user_id', 'blog_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_blog_favorites');
    }
};
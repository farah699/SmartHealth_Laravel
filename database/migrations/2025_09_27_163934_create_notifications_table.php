<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Utilisateur qui reçoit la notification
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Utilisateur qui a fait l'action
            $table->string('type'); // 'comment', 'comment_like', 'blog_like'
            $table->string('title'); // Titre de la notification
            $table->text('message'); // Message de la notification
            $table->json('data')->nullable(); // Données supplémentaires (blog_id, comment_id, etc.)
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
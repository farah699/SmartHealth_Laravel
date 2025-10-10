<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('content_type')->nullable();
            $table->text('description')->nullable();
            $table->string('target_audience')->nullable();
            $table->string('difficulty_level')->nullable();
            $table->string('estimated_time')->nullable();
            $table->string('url')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_recommendations');
    }
};
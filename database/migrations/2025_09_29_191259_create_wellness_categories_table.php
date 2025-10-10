<?php
// filepath: database/migrations/xxxx_create_wellness_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellness_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7); // Code couleur hex
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellness_categories');
    }
};
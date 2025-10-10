<?php
// filepath: database/migrations/xxxx_create_wellness_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellness_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wellness_category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes'); // Calculé automatiquement
            $table->enum('status', ['planned', 'completed', 'cancelled', 'missed'])->default('planned');
            $table->enum('mood_before', ['very_bad', 'bad', 'neutral', 'good', 'very_good'])->nullable();
            $table->enum('mood_after', ['very_bad', 'bad', 'neutral', 'good', 'very_good'])->nullable();
            $table->tinyInteger('stress_level_before')->nullable(); // 1-10
            $table->tinyInteger('stress_level_after')->nullable(); // 1-10
            $table->text('notes')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_config')->nullable(); // Configuration récurrence
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellness_events');
    }
};
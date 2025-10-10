<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            // Rendre ces champs nullable car maintenant ils seront dans les sessions
            $table->date('activity_date')->nullable()->change();
            $table->time('start_time')->nullable()->change();
            $table->integer('duration')->nullable()->change();
            $table->decimal('distance', 8, 2)->nullable()->change();
            $table->integer('calories')->nullable()->change();
            $table->enum('intensity', ['faible', 'modere', 'intense'])->nullable()->change();
            
            // Nouveaux champs pour les activités récurrentes
            $table->boolean('is_recurring')->default(false)->after('description');
            $table->enum('status', ['active', 'paused', 'completed'])->default('active')->after('is_recurring');
            $table->integer('target_sessions_per_week')->nullable()->after('status');
            $table->text('activity_description')->nullable()->after('target_sessions_per_week');
        });
    }

    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['is_recurring', 'status', 'target_sessions_per_week', 'activity_description']);
        });
    }
};
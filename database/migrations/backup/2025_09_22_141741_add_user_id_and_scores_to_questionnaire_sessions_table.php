<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questionnaire_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('questionnaire_sessions', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('questionnaire_sessions', 'phq9_score')) {
                $table->integer('phq9_score')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('questionnaire_sessions', 'gad7_score')) {
                $table->integer('gad7_score')->nullable()->after('phq9_score');
            }
            if (!Schema::hasColumn('questionnaire_sessions', 'phq9_interpretation')) {
                $table->string('phq9_interpretation')->nullable()->after('gad7_score');
            }
            if (!Schema::hasColumn('questionnaire_sessions', 'gad7_interpretation')) {
                $table->string('gad7_interpretation')->nullable()->after('phq9_interpretation');
            }
            if (!Schema::hasColumn('questionnaire_sessions', 'is_completed')) {
                $table->boolean('is_completed')->default(false)->after('gad7_interpretation');
            }
            if (!Schema::hasColumn('questionnaire_sessions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('is_completed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('questionnaire_sessions', 'completed_at')) $table->dropColumn('completed_at');
            if (Schema::hasColumn('questionnaire_sessions', 'is_completed')) $table->dropColumn('is_completed');
            if (Schema::hasColumn('questionnaire_sessions', 'gad7_interpretation')) $table->dropColumn('gad7_interpretation');
            if (Schema::hasColumn('questionnaire_sessions', 'phq9_interpretation')) $table->dropColumn('phq9_interpretation');
            if (Schema::hasColumn('questionnaire_sessions', 'gad7_score')) $table->dropColumn('gad7_score');
            if (Schema::hasColumn('questionnaire_sessions', 'phq9_score')) $table->dropColumn('phq9_score');
            if (Schema::hasColumn('questionnaire_sessions', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
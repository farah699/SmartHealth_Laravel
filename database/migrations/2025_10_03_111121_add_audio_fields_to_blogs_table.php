<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('audio_url')->nullable()->after('content');
            $table->boolean('audio_generated')->default(false)->after('audio_url');
            $table->integer('estimated_duration')->nullable()->after('audio_generated'); // en minutes
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['audio_url', 'audio_generated', 'estimated_duration']);
        });
    }
};
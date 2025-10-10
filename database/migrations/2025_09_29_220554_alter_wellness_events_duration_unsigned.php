<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         DB::statement('UPDATE wellness_events SET duration_minutes = ABS(duration_minutes) WHERE duration_minutes < 0');
    Schema::table('wellness_events', function (Blueprint $table) {
        $table->unsignedInteger('duration_minutes')->default(0)->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

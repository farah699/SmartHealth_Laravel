<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('height', 5, 2)->nullable()->after('email');
            $table->decimal('weight', 5, 2)->nullable()->after('height');
            $table->decimal('imc', 4, 1)->nullable()->after('weight');
            $table->string('imc_category')->nullable()->after('imc');
            $table->timestamp('imc_calculated_at')->nullable()->after('imc_category');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['height', 'weight', 'imc', 'imc_category', 'imc_calculated_at']);
        });
    }
};
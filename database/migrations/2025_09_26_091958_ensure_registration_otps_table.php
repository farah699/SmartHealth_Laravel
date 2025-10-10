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
        if (!Schema::hasTable('registration_otps')) {
            Schema::create('registration_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->string('otp', 6);
                $table->datetime('expires_at');
                $table->timestamps();
                
                // Index pour améliorer les performances
                $table->index('email');
                $table->index('expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_otps');
    }
};
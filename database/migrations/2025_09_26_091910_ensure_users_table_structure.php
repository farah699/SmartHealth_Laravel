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
        // S'assurer que la table users a la bonne structure
        if (!Schema::hasTable('users')) {
            // Si la table n'existe pas, la créer complètement
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['Student', 'Teacher'])->default('Student');
                $table->boolean('enabled')->default(false);
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            // La table existe, ajouter seulement les colonnes manquantes
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['Student', 'Teacher'])->default('Student')->after('password');
                }
                
                if (!Schema::hasColumn('users', 'enabled')) {
                    $table->boolean('enabled')->default(false)->after('role');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne supprimer que les colonnes ajoutées, pas toute la table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            if (Schema::hasColumn('users', 'enabled')) {
                $table->dropColumn('enabled');
            }
        });
    }
};
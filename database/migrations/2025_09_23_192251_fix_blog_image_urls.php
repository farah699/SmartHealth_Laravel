<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Corriger les URLs d'images existantes
        DB::table('blogs')
            ->where('image_url', 'like', '/storage/images/%')
            ->update([
                'image_url' => DB::raw("REPLACE(image_url, '/storage/', '')")
            ]);
    }

    public function down()
    {
        // Remettre les URLs comme avant
        DB::table('blogs')
            ->where('image_url', 'like', 'images/%')
            ->update([
                'image_url' => DB::raw("CONCAT('/storage/', image_url)")
            ]);
    }
};
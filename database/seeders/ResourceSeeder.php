<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        Resource::factory()->count(10)->create();
    }
}


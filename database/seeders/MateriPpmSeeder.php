<?php

namespace Database\Seeders;

use App\Models\MateriPpm;
use Illuminate\Database\Seeder;

class MateriPpmSeeder extends Seeder
{
    /**
     * Seed the application's materi PPM data.
     */
    public function run(): void
    {
        if (MateriPpm::query()->exists()) {
            return;
        }

        MateriPpm::factory()->count(5)->create();
    }
}

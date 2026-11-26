<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Ppm;
use Illuminate\Database\Seeder;

class PpmSeeder extends Seeder
{
    /**
     * Seed the application's PPM data.
     */
    public function run(): void
    {
        if (Ppm::query()->exists()) {
            return;
        }

        Pegawai::query()->each(function (Pegawai $pegawai): void {
            Ppm::factory()->count(2)->create([
                'pegawai_id' => $pegawai->id,
            ]);
        });
    }
}
